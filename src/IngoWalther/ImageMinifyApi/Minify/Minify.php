<?php

namespace IngoWalther\ImageMinifyApi\Minify;

use IngoWalther\ImageMinifyApi\Compressor\Compressor;
use IngoWalther\ImageMinifyApi\File\FileHandler;
use IngoWalther\ImageMinifyApi\File\FileSizeFormatter;
use IngoWalther\ImageMinifyApi\File\SavingCalculator;
use IngoWalther\ImageMinifyApi\Response\CompressedFileResponse;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Minify
 * @package IngoWalther\ImageMinifyApi\Minify
 */
class Minify
{
    /**
     * @var Compressor[]
     */
    private $compressors = [];

    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Minify constructor.
     * @param FileHandler $fileHandler
     */
    public function __construct(FileHandler $fileHandler, Logger $logger)
    {
        $this->fileHandler = $fileHandler;
        $this->logger = $logger;
    }

    /**
     * @param Compressor $compressor
     */
    public function addCompressor(Compressor $compressor)
    {
        $this->compressors[] = $compressor;
    }

    /**
     * Minifies the given Image
     *
     * @param UploadedFile $file
     * @param array $user
     * @return CompressedFileResponse
     */
    public function minify(UploadedFile $file, $user)
    {
        $fileType = $this->fileHandler->getFileType($file->getRealPath());

        $compressorToUse = $this->getCompressorToUse($fileType);
        $path = $compressorToUse->compress($file);

        $oldSize = $this->fileHandler->getFileSize($file->getRealPath());
        $newSize = $this->fileHandler->getFileSize($path);

        if ($newSize < $oldSize) {
            return $this->handleSuccess($file, $user, $path, $oldSize, $newSize);
        }

        return $this->handleNewFileBigger($file, $user, $path, $oldSize, $newSize);
    }

    /**
     * @param $fileType
     * @return Compressor
     */
    private function getCompressorToUse($fileType)
    {
        foreach ($this->compressors as $compressor) {
            if ($compressor->getFileTypeToHandle() == $fileType) {
                if ($compressor->checkLibaryIsInstalled()) {
                    return $compressor;
                }
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Filetype "%s" not supported', $fileType)
        );
    }

    /**
     * @param UploadedFile $file
     * @param $user
     * @param $path
     * @param $oldSize
     * @param $newSize
     * @return CompressedFileResponse
     */
    private function handleSuccess(UploadedFile $file, $user, $path, $oldSize, $newSize)
    {
        $binaryContent = $this->fileHandler->getFileContent($path);
        $this->fileHandler->delete($path);

        $savingCalculator = new SavingCalculator();
        $saving = $savingCalculator->calculate($oldSize, $newSize);

        $fomatter = new FileSizeFormatter();

        $this->logger->info(
            sprintf('[%s] Succesfully compressed Image (%s) - Old: %s, New: %s, Saving: %d%%',
                $user['name'],
                $file->getClientOriginalName(),
                $fomatter->humanReadable($oldSize),
                $fomatter->humanReadable($newSize),
                $saving)
        );

        return new CompressedFileResponse($oldSize, $newSize, $saving, $binaryContent);
    }

    /**
     * @param UploadedFile $file
     * @param $user
     * @param $oldSize
     * @param $newSize
     * @param $path
     * @return CompressedFileResponse
     */
    private function handleNewFileBigger(UploadedFile $file, $user, $path, $oldSize, $newSize)
    {
        $savingCalculator = new SavingCalculator();
        $saving = $savingCalculator->calculate($oldSize, $newSize);

        $fomatter = new FileSizeFormatter();

        $this->logger->info(
            sprintf('[%s] New image is bigger than the original one - returning original image (%s) - Old: %s, New: %s, Saving: %d%%',
                $user['name'],
                $file->getClientOriginalName(),
                $fomatter->humanReadable($oldSize),
                $fomatter->humanReadable($newSize),
                $saving)
        );

        $binaryContent = $this->fileHandler->getFileContent($file->getRealPath());
        $this->fileHandler->delete($path);
        $newSize = $oldSize;
        $saving = 0;

        return new CompressedFileResponse($oldSize, $newSize, $saving, $binaryContent);
    }

}
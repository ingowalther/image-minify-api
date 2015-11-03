<?php

namespace IngoWalther\ImageMinifyApi\Minify;

use IngoWalther\ImageMinifyApi\Compressor\Compressor;
use IngoWalther\ImageMinifyApi\File\FileHandler;
use IngoWalther\ImageMinifyApi\Response\CompressedFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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
     * Minify constructor.
     * @param FileHandler $fileHandler
     */
    public function __construct(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
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
     * @param Request $request
     * @return CompressedFileResponse
     */
    public function minify(Request $request)
    {
        $this->checkForImage($request);

        /** @var UploadedFile $file */
        $file = $request->files->get('image');

        $fileType = $this->fileHandler->getFileType($file->getRealPath());

        $compressorToUse = $this->getCompressorToUse($fileType);

        $path = $compressorToUse->compress($file);

        $oldSize = $this->fileHandler->getFileSize($file->getRealPath());
        $newSize = $this->fileHandler->getFileSize($path);

        $binaryContent = $this->fileHandler->getFileContent($path);
        $this->fileHandler->delete($path);

        return new CompressedFileResponse($oldSize, $newSize, $binaryContent);
    }

    /**
     * Check if image is in request
     *
     * @param Request $request
     */
    private function checkForImage(Request $request)
    {
        if (!$request->files->has('image')) {
            throw new \InvalidArgumentException('No Image given');
        }
    }

    /**
     * @param $fileType
     * @return Compressor
     */
    private function getCompressorToUse($fileType)
    {
        foreach ($this->compressors as $compressor) {
            if ($compressor->getFileTypeToHandle() == $fileType) {
                return $compressor;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Filetype "%s" not supported', $fileType)
        );
    }


}
<?php


namespace IngoWalther\ImageMinifyApi\Compressor;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Runners\Exec;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class GifsicleCompressor
 * @package IngoWalther\ImageMinifyApi\Compressor
 */
class GifsicleCompressor implements Compressor
{

    /**
     * @var string
     */
    private $binaryPath;

    /**
     * @var string
     */
    private $command;

    /**
     * GifsicleCompressor constructor.
     * @param $binaryPath
     * @param $command
     */
    public function __construct($binaryPath, $command)
    {
        $this->binaryPath = $binaryPath;
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getFileTypeToHandle()
    {
        return 'image/gif';
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function compress(UploadedFile $file)
    {
        $shell = new Exec();

        $command = new Command(
            sprintf($this->command, $file->getRealPath(), $file->getRealPath() . 'compressed')
        );

        $shell->run($command);

        if (!file_exists($file->getRealPath() . 'compressed')) {
            throw new \RuntimeException('No compressed Image created!');
        }

        return $file->getRealPath() . 'compressed';
    }

    /**
     * @return bool
     */
    public function checkLibaryIsInstalled()
    {
        $shell = new Exec();

        $command = new Command($this->binaryPath);
        $command->addFlag(new Command\Flag('-version'));

        $shell->run($command);

        if ($shell->getReturnValue() === 0) {
            return true;
        }

        return false;
    }
}

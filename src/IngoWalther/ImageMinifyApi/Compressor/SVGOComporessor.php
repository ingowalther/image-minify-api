<?php

namespace IngoWalther\ImageMinifyApi\Compressor;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Runners\Exec;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class SVGOComporessor
 * @package IngoWalther\ImageMinifyApi\Compressor
 */
class SVGOComporessor implements Compressor
{
    /**
     * @return string
     */
    public function getFileTypeToHandle()
    {
        return 'image/svg+xml';
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function compress(UploadedFile $file)
    {
        $shell = new Exec();

        $command = new Command(
            sprintf('svgo %s %s', $file->getRealPath(), $file->getRealPath() . 'compressed')
        );

        $shell->run($command);

        if (!file_exists($file->getRealPath() . 'compressed')) {
            throw new \RuntimeException('No compressed Image created! Is SVGO installed?');
        }

        return $file->getRealPath() . 'compressed';
    }

}
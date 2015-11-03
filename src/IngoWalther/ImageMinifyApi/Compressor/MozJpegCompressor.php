<?php

namespace IngoWalther\ImageMinifyApi\Compressor;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Runners\Exec;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MozJpegCompressor
 * @package IngoWalther\ImageMinifyApi\Compressor
 */
class MozJpegCompressor implements Compressor
{
    /**
     * @return string
     */
    public function getFileTypeToHandle()
    {
        return 'image/jpeg';
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function compress(UploadedFile $file)
    {
        $shell = new Exec();

        $command = new Command(
            sprintf('/opt/mozjpeg/bin/cjpeg -quality 70 %s > %s', $file->getRealPath(), $file->getRealPath() . 'compressed')
        );

        $shell->run($command);

        if (!file_exists($file->getRealPath() . 'compressed')) {
            throw new \RuntimeException('No compressed Image created! Is mozjpeg installed?');
        }

        return $file->getRealPath() . 'compressed';
    }
}
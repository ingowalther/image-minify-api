<?php

namespace IngoWalther\ImageMinifyApi\Compressor;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface Compressor
 * @package IngoWalther\ImageMinifyApi\Compressor
 */
interface Compressor
{
    /**
     * Must return the MimeType the compressor is able to handle
     * (e.g. image/jpeg)
     *
     * @return string
     */
    public function getFileTypeToHandle();

    /**
     * Must return the path of the compressed file
     *
     * @param UploadedFile $file
     * @return string
     */
    public function compress(UploadedFile $file);

    /**
     * Returns if the library is installed
     *
     * @return bool
     */
    public function checkLibaryIsInstalled();
}

<?php

namespace IngoWalther\ImageMinifyApi\File;

/**
 * Class FileHandler
 * @package IngoWalther\ImageMinifyApi\File
 */
class FileHandler
{
    /**
     * @param $path
     * @return string
     */
    public function getFileType($path)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($path);
    }

    /**
     * @param $path
     * @return int
     */
    public function getFileSize($path)
    {
        return filesize($path);
    }

    /**
     * @param $path
     * @return string
     */
    public function getFileContent($path)
    {
        return file_get_contents($path);
    }

    /**
     * @param $path
     * @return bool
     */
    public function delete($path)
    {
        return unlink($path);
    }
}
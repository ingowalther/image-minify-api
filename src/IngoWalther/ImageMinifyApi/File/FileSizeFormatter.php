<?php

namespace IngoWalther\ImageMinifyApi\File;

/**
 * Class FileSizeFormatter
 * @package IngoWalther\ImageMinifyApi\File
 */
class FileSizeFormatter
{
    /**
     * Bytes formatted human readable
     *
     * @param $bytes
     * @param $decimals
     * @return string
     */
    public function humanReadable($bytes, $decimals = 2)
    {
        if($bytes < 0) {
            throw new \InvalidArgumentException('Bytes must be greater 0');
        }

        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = intval(floor((strlen($bytes) - 1) / 3));
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $size[$factor];
    }

}
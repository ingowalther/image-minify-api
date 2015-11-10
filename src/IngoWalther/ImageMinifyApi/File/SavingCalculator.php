<?php

namespace IngoWalther\ImageMinifyApi\File;

/**
 * Class SavingCalculator
 * @package IngoWalther\ImageMinifyApi\File
 */
class SavingCalculator
{
    /**
     * Calculates the saving between old and new size
     *
     * @param $oldSize
     * @param $newSize
     * @return float
     */
    public function calculate($oldSize, $newSize)
    {
        if(!$oldSize || !$newSize) {
            return 0;
        }

        $saving = floor(100 - ($newSize / ($oldSize / 100)));
        return $saving;
    }

}
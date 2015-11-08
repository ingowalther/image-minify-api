<?php

namespace IngoWalther\ImageMinifyApi\Security;

/**
 * Class RandomStringGenerator
 * @package IngoWalther\ImageMinifyApi\Security
 */
class RandomStringGenerator
{
    private $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Generates a random String
     *
     * @param int $length
     * @return string
     */
    public function generate($length = 32)
    {
        $string = '';

        $max = strlen($this->characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $string .= $this->characters[rand(0,$max)];
        }
        return $string;
    }

}
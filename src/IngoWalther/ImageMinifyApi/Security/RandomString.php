<?php

namespace IngoWalther\ImageMinifyApi\Security;

/**
 * Class RandomString
 * @package IngoWalther\ImageMinifyApi\Security
 */
class RandomString
{
    /**
     * Generates a random String
     *
     * @param int $length
     * @return string
     */
    public static function generate($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }

}
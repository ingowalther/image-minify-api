<?php

namespace IngoWalther\ImageMinifyApi\Error;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ErrorHandler
 * @package IngoWalther\ImageMinifyApi\Error
 */
class ErrorHandler
{
    /**
     * @param \Exception $e
     * @param $code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function handle(\Exception $e, $code)
    {
        switch ($code) {
            case 404:
                $data = array(
                    'success' => 'false',
                    'code' => '404',
                    'message' => 'The requested page could not be found.',
                );
                break;
            default:
                $data = array(
                    'success' => 'false',
                    'code' => $code,
                    'message' => $e->getMessage(),
                );
        }

        return new JsonResponse($data, $code);
    }
}


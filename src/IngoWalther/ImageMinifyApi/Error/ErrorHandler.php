<?php

namespace IngoWalther\ImageMinifyApi\Error;

use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ErrorHandler
 * @package IngoWalther\ImageMinifyApi\Error
 */
class ErrorHandler
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * ErrorHandler constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

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

        $this->logger->error(
            sprintf('%s: %s', get_class($e), $e->getMessage())
        );

        return new JsonResponse($data, $code);
    }
}


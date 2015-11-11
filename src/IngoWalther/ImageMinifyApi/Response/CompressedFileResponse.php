<?php

namespace IngoWalther\ImageMinifyApi\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CompressedFileResponse
 * @package IngoWalther\ImageMinifyApi\Response
 */
class CompressedFileResponse extends JsonResponse
{
    public function __construct($oldSize, $newSize, $saving, $binaryContent, $status = 200, array $headers = array())
    {
        $data = [
            'success' => true,
            'oldSize' => $oldSize,
            'newSize' => $newSize,
            'saving' => $saving,
            'image' => base64_encode($binaryContent)
        ];

        parent::__construct($data, $status, $headers);
    }
}
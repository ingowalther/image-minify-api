<?php

namespace IngoWalther\ImageMinifyApi\Validator;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestValidator
 * @package IngoWalther\ImageMinifyApi\Validator
 */
class RequestValidator
{
    /**
     * @var array
     */
    private $requiredFileFields = ['image'];

    /**
     * @var array
     */
    private $requiredPostFields = ['api_key'];

    /**
     * @param Request $request
     */
    public function validateRequest(Request $request)
    {
        $this->validatePostFields($request);
        $this->validateFileFields($request);
    }

    /**
     * @param Request $request
     */
    private function validatePostFields(Request $request)
    {
        foreach($this->requiredPostFields as $field) {
            $this->validatePostField($request, $field);
        }
    }

    /**
     * @param Request $request
     * @param $field
     */
    private function validatePostField(Request $request, $field)
    {
        if (!$request->request->has($field)) {
            throw new \InvalidArgumentException(sprintf('Postfield "%s" must be set', $field));
        }
    }

    /**
     * @param Request $request
     */
    private function validateFileFields(Request $request)
    {
        foreach($this->requiredFileFields as $field) {
            $this->validateFileField($request, $field);
        }
    }

    /**
     * @param Request $request
     * @param $field
     */
    private function validateFileField(Request $request, $field)
    {
        if (!$request->files->has($field)) {
            throw new \InvalidArgumentException(sprintf('Filefield "%s" must be set', $field));
        }
    }

    /**
     * @param array $requiredFileFields
     */
    public function setRequiredFileFields($requiredFileFields)
    {
        $this->requiredFileFields = $requiredFileFields;
    }

    /**
     * @param array $requiredPostFields
     */
    public function setRequiredPostFields($requiredPostFields)
    {
        $this->requiredPostFields = $requiredPostFields;
    }
}
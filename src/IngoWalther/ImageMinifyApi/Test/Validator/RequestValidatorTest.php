<?php

namespace IngoWalther\ImageMinifyApi\Test\Validator;

use IngoWalther\ImageMinifyApi\Validator\RequestValidator;
use Symfony\Component\HttpFoundation\Request;

class RequestValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestValidator
     */
    private $object;

    public function setUp()
    {
        $this->object = new RequestValidator();
    }

    public function testWithOnePostFieldNotSet()
    {
        $this->object->setRequiredPostFields(['foo', 'bar', 'baz']);
        $this->object->setRequiredFileFields([]);

        $request = new Request();
        $request->request->set('foo', '1');
        $request->request->set('bar', '2');

        $this->setExpectedException('InvalidArgumentException');
        $this->object->validateRequest($request);
    }

    public function testWithAllPostFieldSetAndNoFileFieldRequired()
    {
        $this->object->setRequiredPostFields(['foo', 'bar', 'baz']);
        $this->object->setRequiredFileFields([]);

        $request = new Request();
        $request->request->set('foo', '1');
        $request->request->set('bar', '2');
        $request->request->set('baz', '3');

        $this->object->validateRequest($request);
    }

    public function testWithAllPostFieldSetAndRequiredFileFieldNotSet()
    {
        $this->object->setRequiredPostFields(['foo', 'bar', 'baz']);
        $this->object->setRequiredFileFields(['file', 'file2']);

        $request = new Request();
        $request->request->set('foo', '1');
        $request->request->set('bar', '2');
        $request->request->set('baz', '3');

        $request->files->set('file', array());

        $this->setExpectedException('InvalidArgumentException');
        $this->object->validateRequest($request);
    }

    public function testWithAllPostFieldSetAndAllFileFieldsSet()
    {
        $this->object->setRequiredPostFields(['foo', 'bar', 'baz']);
        $this->object->setRequiredFileFields(['file', 'file2']);

        $request = new Request();
        $request->request->set('foo', '1');
        $request->request->set('bar', '2');
        $request->request->set('baz', '3');

        $request->files->set('file', array());
        $request->files->set('file2', array());

        $this->object->validateRequest($request);
    }

}
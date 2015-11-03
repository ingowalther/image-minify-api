<?php

namespace IngoWalther\ImageMinifyApi\Test\Minify;

use IngoWalther\ImageMinifyApi\Minify\Minify;
use Symfony\Component\HttpFoundation\Request;

class MinifyTest extends \PHPUnit_Framework_TestCase
{

    private $fileHandler;

    /** @var  Minify */
    private $object;

    public function setUp()
    {
        $this->fileHandler = $this->getMock(
            'IngoWalther\ImageMinifyApi\File\FileHandler',
            array(),
            array(),
            '',
            false
        );

        $this->object = new Minify($this->fileHandler);
    }

    public function testWithNoFile()
    {
        $request = new Request();
        $this->setExpectedException('InvalidArgumentException');
        $this->object->minify($request);
    }

    public function testWithNoCompressor()
    {
        $request = new Request();
        $request->files->add(array('image' => $this->createMockFile()));

        $this->fileHandler->expects($this->once())
             ->method('getFileType')
             ->with('/tmp/foobar')
            ->will($this->returnValue('image/jpeg'));

        $this->setExpectedException('InvalidArgumentException');
        $this->object->minify($request);
    }

    public function testWithNoMatchingCompressor()
    {
        $request = new Request();
        $request->files->add(array('image' => $this->createMockFile()));

        $this->fileHandler->expects($this->once())
            ->method('getFileType')
            ->with('/tmp/foobar')
            ->will($this->returnValue('image/png'));

        $this->setExpectedException('InvalidArgumentException');

        $this->object->addCompressor($this->createMockCompressor('image/jpeg'));
        $this->object->minify($request);
    }

    public function testWithMatchingCompressor()
    {
        $request = new Request();
        $image = $this->createMockFile();
        $request->files->add(array('image' => $image));

        $this->fileHandler->expects($this->once())
            ->method('getFileType')
            ->with('/tmp/foobar')
            ->will($this->returnValue('image/jpeg'));

        $compressor = $this->createMockCompressor('image/jpeg');

        $compressor->expects($this->once())
                   ->method('compress')
                   ->with($image)
                   ->will($this->returnValue('/tmp/foobar_compressed'));

        $this->fileHandler->expects($this->at(1))
             ->method('getFileSize')
             ->with('/tmp/foobar')
             ->will($this->returnValue(1000));

        $this->fileHandler->expects($this->at(2))
            ->method('getFileSize')
            ->with('/tmp/foobar_compressed')
            ->will($this->returnValue(500));

        $this->fileHandler->expects($this->at(3))
            ->method('getFileContent')
            ->with('/tmp/foobar_compressed')
            ->will($this->returnValue('Foobar'));

        $this->fileHandler->expects($this->at(4))
            ->method('delete')
            ->with('/tmp/foobar_compressed');

        $this->object->addCompressor($compressor);
        $result = $this->object->minify($request);

        $this->assertEquals('IngoWalther\ImageMinifyApi\Response\CompressedFileResponse', get_class($result));
    }

    private function createMockFile()
    {
        $file = $this->getMock(
            'Symfony\Component\HttpFoundation\File\UploadedFile',
            array(),
            array(),
            '',
            false
        );

        $file->expects($this->any())
            ->method('getRealPath')
            ->will($this->returnValue('/tmp/foobar'));

        return $file;
    }

    private function createMockCompressor($type)
    {
        $compressor = $this->getMock(
            'IngoWalther\ImageMinifyApi\Compressor\MozJpegCompressor',
            array(),
            array(),
            '',
            false
        );

        $compressor->expects($this->any())
                   ->method('getFileTypeToHandle')
                   ->will($this->returnValue($type));

        return $compressor;
    }

}
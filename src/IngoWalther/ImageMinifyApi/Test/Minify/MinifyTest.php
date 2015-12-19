<?php

namespace IngoWalther\ImageMinifyApi\Test\Minify;

use IngoWalther\ImageMinifyApi\Minify\Minify;

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

        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new Minify($this->fileHandler, $logger);
    }

    public function testWithNoCompressor()
    {
        $this->fileHandler->expects($this->once())
             ->method('getFileType')
             ->with('/tmp/foobar')
            ->will($this->returnValue('image/jpeg'));

        $this->setExpectedException('InvalidArgumentException');
        $this->object->minify($this->createMockFile(), ['name' => 'foobar']);
    }

    public function testWithNoMatchingCompressor()
    {
        $this->fileHandler->expects($this->once())
            ->method('getFileType')
            ->with('/tmp/foobar')
            ->will($this->returnValue('image/png'));

        $this->setExpectedException('InvalidArgumentException');

        $this->object->addCompressor($this->createMockCompressor('image/jpeg'));
        $this->object->minify($this->createMockFile(), ['name' => 'foobar']);
    }

    public function testWithNoCompressorButLibaryNotInstalled()
    {
        $this->fileHandler->expects($this->once())
            ->method('getFileType')
            ->with('/tmp/foobar')
            ->will($this->returnValue('image/png'));

        $this->setExpectedException('InvalidArgumentException');

        $this->object->addCompressor($this->createMockCompressor('image/jpeg', $installed = false));
        $this->object->minify($this->createMockFile(), ['name' => 'foobar']);
    }

    public function testWithMatchingCompressor()
    {
        $image = $this->createMockFile();

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
        $result = $this->object->minify($image, ['name' => 'foobar']);

        $this->assertEquals('IngoWalther\ImageMinifyApi\Response\CompressedFileResponse', get_class($result));
    }

    public function testWithMatchingCompressorAndNewFileBigger()
    {
        $image = $this->createMockFile();

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
            ->will($this->returnValue(1001));

        $this->fileHandler->expects($this->at(3))
            ->method('getFileContent')
            ->with('/tmp/foobar')
            ->will($this->returnValue('Foobar'));

        $this->fileHandler->expects($this->at(4))
            ->method('delete')
            ->with('/tmp/foobar_compressed');

        $this->object->addCompressor($compressor);
        $result = $this->object->minify($image, ['name' => 'foobar']);

        $this->assertEquals('IngoWalther\ImageMinifyApi\Response\CompressedFileResponse', get_class($result));

        $resultData = json_decode($result->getContent());

        $this->assertEquals(1000, $resultData->newSize);
        $this->assertEquals(0, $resultData->saving);
    }

    private function createMockFile()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                     ->setConstructorArgs(array('/tmp/test', 'test', null, null, 'test'))
                     ->getMock();

        $file->expects($this->any())
            ->method('getRealPath')
            ->will($this->returnValue('/tmp/foobar'));

        return $file;
    }

    private function createMockCompressor($type, $installed = true)
    {
        $compressor = $this->getMockBuilder('IngoWalther\ImageMinifyApi\Compressor\MozJpegCompressor')
                           ->disableOriginalConstructor(true)
                           ->getMock();

        $compressor->expects($this->any())
                   ->method('getFileTypeToHandle')
                   ->will($this->returnValue($type));

        $compressor->expects($this->any())
                   ->method('checkLibaryIsInstalled')
                   ->will($this->returnValue($installed));

        return $compressor;
    }

}
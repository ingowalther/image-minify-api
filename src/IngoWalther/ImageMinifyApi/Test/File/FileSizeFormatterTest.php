<?php

namespace IngoWalther\ImageMinifyApi\Test\File;

use IngoWalther\ImageMinifyApi\File\FileSizeFormatter;

class FileSizeFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testHumanReadable()
    {
        $testData = [
            ['test' => 800, 'expected' => '800.00B'],
            ['test' => 1200, 'expected' => '1.17kB'],
            ['test' => 8000000, 'expected' => '7.63MB'],
            ['test' => 8000000000, 'expected' => '7.45GB'],
            ['test' => 0, 'expected' => '0.00B'],
        ];

        $fomatter = new FileSizeFormatter();

        foreach($testData as $entry) {
            $result = $fomatter->humanReadable($entry['test']);
            $this->assertEquals($entry['expected'], $result);
        }
    }

    public function testHumanReadableWithBytesSmallerZero()
    {
        $fomatter = new FileSizeFormatter();
        $this->setExpectedException('InvalidArgumentException');
        $fomatter->humanReadable(-1);
    }
}
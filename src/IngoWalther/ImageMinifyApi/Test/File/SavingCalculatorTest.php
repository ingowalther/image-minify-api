<?php

namespace IngoWalther\ImageMinifyApi\Test\File;

use IngoWalther\ImageMinifyApi\File\SavingCalculator;

class SavingCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SavingCalculator
     */
    private $object;

    public function setUp()
    {
        $this->object = new SavingCalculator();
    }

    public function testWithOldAndNewSizeZero()
    {
        $result = $this->object->calculate(0, 0);
        $this->assertEquals(0, $result);
    }

    public function testWithOldSizeZeroAndNewSizeSet()
    {
        $result = $this->object->calculate(0, 50);
        $this->assertEquals(0, $result);
    }

    public function testWithOldSizeSetAndNewSizeZero()
    {
        $result = $this->object->calculate(50, 0);
        $this->assertEquals(0, $result);
    }

    public function testWithOldAndNewSizeSet()
    {
        $result = $this->object->calculate(100, 50);
        $this->assertEquals(50, $result);
    }

}
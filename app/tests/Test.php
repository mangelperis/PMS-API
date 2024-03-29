<?php

namespace App\Tests;

use SampleTest;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testAdd(): void
    {
        $calculator = new Calculator();
        $result = $calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }
}

class Calculator
{
    public function add(int $a,int $b): int
    {
        return $a + $b;
    }
}

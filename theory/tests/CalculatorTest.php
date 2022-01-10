<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Services\CalculatorService;

class CalculatorTest extends TestCase
{
    public function testAdd(): void
    {
        $calculator = new CalculatorService();
        $sum = $calculator->add(1, 9);
        $this->assertEquals(10, $sum);
    }
}

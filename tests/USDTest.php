<?php

/*
 * This file is part of the Eophantasy package.
 *
 * (c) Ilya Sitnikov <sitnikovik@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eophantasy\Money\Tests;

use Eophantasy\Money\RUB;
use Eophantasy\Money\USD;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Eophantasy\Money\Currency\USD as USDCurrency;

/**
 * Tests for the USD class.
 *
 * @internal
 * @covers Eophantasy\Money\USD
 */
final class USDTest extends TestCase
{
    /**
     * Tests the USD string representation.
     * 
     * @return void
     * @covers Eophantasy\Money\USD::__toString
     */
    public function test__toString(): void
    {
        $usd = new USD(100, 0);

        $this->assertEquals("$100.00", sprintf("%s", $usd));
    }

    /**
     * Tests the USD currency method.
     * 
     * @return void
     * @covers Eophantasy\Money\USD::currency
     */
    public function testCurrency(): void
    {
        $usd = new USD(100, 00);

        $this->assertInstanceOf(USDCurrency::class, $usd->currency());
    }

    /**
     * Tests the USD units method.
     * 
     * @return void
     * @covers Eophantasy\Money\USD::units
     */
    public function testUnits(): void
    {
        $usd = new USD(100, 00);

        $this->assertEquals(100, $usd->units());
    }

    /**
     * Tests the USD nanos method.
     * 
     * @return void
     * @covers Eophantasy\Money\USD::nanos
     */
    public function testNanos(): void
    {
        $usd = new USD(100, 50);

        $this->assertEquals(50, $usd->nanos());
    }

    /**
     * Tests the USD equals method.
     * 
     * @return void
     * @covers Eophantasy\Money\USD::equals
     */
    public function testNanosThrowsOnNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Nanos cannot be negative.");

        (new USD(100, -1))->nanos();
    }

    /**
     * Tests the USD nanos method with greater than 99 value.
     * 
     * @return void
     * @covers Eophantasy\Money\USD::nanos
     */
    public function testNanosThrowsOnGreaterThan99(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Nanos cannot be greater than 99.");

        (new USD(100, 100))->nanos();
    }

    /**
     * Tests the RUB equals method.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::equals
     */
    public function testEqualsOneCurrency(): void
    {
        $usd1 = new USD(100, 00);
        $usd2 = new USD(100, 00);
        $usd3 = new USD(500, 70);

        $this->assertTrue($usd1->equals($usd2));
        $this->assertFalse($usd2->equals($usd3));
    }

    /**
     * Tests the RUB equals method with different currencies.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::equals
     */
    public function testEqualsDifferentCurrencies(): void
    {
        $rub = new RUB(100, 50);
        $usd = new USD(100, 50);

        $this->assertFalse($rub->equals($usd));
    }

    /**
     * Tests the USD add method.
     *
     * @return void
     * @covers Eophantasy\Money\USD::add
     */
    public function testMoneyAdd(): void
    {
        $usd1 = new USD(100, 50);
        $usd2 = new USD(20, 20);

        $result = $usd1->add($usd2);

        $this->assertInstanceOf(USD::class, $result);
        $this->assertNotSame($result, $usd1);

        $this->assertEquals($result->units(), 120);
        $this->assertEquals($result->nanos(), 70);
    }

    /**
     * Tests the USD add method with different currencies.
     *
     * @return void
     * @covers Eophantasy\Money\USD::add
     */
    public function testMoneyAddWithDifferentCurrencies(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency code of the provided $money instance not the same');

        $usd1 = new USD(100, 50);
        $rub1 = new RUB(20, 20);

        $usd1->add($rub1);
    }

    /**
     * Tests the USD add method with nanos overflow.
     *
     * @return void
     * @covers Eophantasy\Money\USD::add
     */
    public function testMoneyAddWithNanosOverflow(): void
    {
        $usd1 = new USD(100, 50);
        $usd2 = new USD(100, 52);

        $result = $usd1->add($usd2);

        $this->assertInstanceOf(USD::class, $result);
        $this->assertNotSame($result, $usd1);

        $this->assertEquals($result->units(), 201);
        $this->assertEquals($result->nanos(), 2);
    }

    /**
     * Tests the USD subtract method.
     *
     * @return void
     * @covers Eophantasy\Money\USD::subtract
     */
    public function testMoneySubtract(): void
    {
        $usd1 = new USD(100, 50);
        $usd2 = new USD(50, 20);

        $result = $usd1->subtract($usd2);

        $this->assertInstanceOf(USD::class, $result);
        $this->assertNotSame($result, $usd1);

        $this->assertEquals($result->units(), 50);
        $this->assertEquals($result->nanos(), 30);
    }

    /**
     * Tests the USD subtract method with different currencies.
     *
     * @return void
     * @covers Eophantasy\Money\USD::subtract
     */
    public function testMoneySubtractWithDifferentCurrencies(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency code of the provided $money instance not the same');

        $usd1 = new USD(100, 50);
        $rub1 = new RUB(50, 20);

        $usd1->subtract($rub1);
    }

    /**
     * Tests the USD subtract method with nanos overflow.
     *
     * @return void
     * @covers Eophantasy\Money\USD::subtract
     */
    public function testMoneySubtractWithNanosOverflow(): void
    {
        $usd1 = new USD(100, 50);
        $usd2 = new USD(50, 70);

        $result = $usd1->subtract($usd2);

        $this->assertInstanceOf(USD::class, $result);
        $this->assertNotSame($result, $usd1);

        $this->assertEquals($result->units(), 49);
        $this->assertEquals($result->nanos(), 80);
    }

    /**
     * Tests the USD multiply method.
     *
     * @return void
     * @covers Eophantasy\Money\USD::multiply
     */
    public function testMoneyMultiply(): void
    {
        $usd1 = new USD(20, 30);

        $result = $usd1->multiply(3);

        $this->assertInstanceOf(USD::class, $result);
        $this->assertNotSame($result, $usd1);

        $this->assertEquals($result->units(), 60);
        $this->assertEquals($result->nanos(), 90);
    }

    /**
     * Tests the USD multiply method with nanos overflow.
     *
     * @return void
     * @covers Eophantasy\Money\USD::multiply
     */
    public function testMoneyMultiplyWithNanosOverflow(): void
    {
        $usd1 = new USD(100, 50);

        $result = $usd1->multiply(2);

        $this->assertInstanceOf(USD::class, $result);
        $this->assertNotSame($result, $usd1);

        $this->assertEquals($result->units(), 201);
        $this->assertEquals($result->nanos(), 0);
    }

    /**
     * Tests the USD divide method.
     *
     * @return void
     * @covers Eophantasy\Money\USD::divide
     */
    public function testMoneyDivide(): void
    {
        $usd1 = new USD(20, 30);

        $result = $usd1->divide(2);

        $this->assertInstanceOf(USD::class, $result);
        $this->assertNotSame($result, $usd1);

        $this->assertEquals($result->units(), 10);
        $this->assertEquals($result->nanos(), 15);
    }

    /**
     * Tests the USD divide method with zero as argument is fail.
     *
     * @return void
     * @covers Eophantasy\Money\USD::divide
     */
    public function testMoneyDivideByZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$divider cannot be zero');

        $usd1 = new USD(20, 30);

        $usd1->divide(0);
    }
}

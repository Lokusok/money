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
use Eophantasy\Money\Currency\RUB as RUBCurrency;

/**
 * Tests for the RUB class.
 *
 * @internal
 * @covers Eophantasy\Money\RUB
 */
final class RUBTest extends TestCase
{
    /**
     * Tests the RUB string representation.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::__toString
     */
    public function test__toString(): void
    {
        $rub = new RUB(100, 00);

        $this->assertEquals("100.00 руб.", sprintf("%s", $rub));
    }

    /**
     * Tests the RUB currency method.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::currency
     */
    public function testCurrency(): void
    {
        $rub = new RUB(100, 00);

        $this->assertInstanceOf(RUBCurrency::class, $rub->currency());
    }

    /**
     * Tests the RUB units method.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::units
     */
    public function testUnits(): void
    {
        $rub = new RUB(100, 00);

        $this->assertEquals(100, $rub->units());
    }

    /**
     * Tests the RUB nanos method.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::nanos
     */
    public function testNanos(): void
    {
        $rub = new RUB(100, 50);

        $this->assertEquals(50, $rub->nanos());
    }

    /**
     * Tests the RUB nanos method with negative value.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::nanos
     */
    public function testNanosThrowsOnNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Nanos cannot be negative.");

        (new RUB(100, -1))->nanos();
    }

    /**
     * Tests the RUB nanos method with value greater than 99.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::nanos
     */
    public function testNanosThrowsOnGreaterThan99(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Nanos cannot be greater than 99.");

        (new RUB(100, 100))->nanos();
    }

    /**
     * Tests the RUB equals method.
     * 
     * @return void
     * @covers Eophantasy\Money\RUB::equals
     */
    public function testEqualsOneCurrency(): void
    {
        $rub1 = new RUB(100, 50);
        $rub2 = new RUB(100, 50);
        $rub3 = new RUB(200, 50);

        $this->assertTrue($rub1->equals($rub2));
        $this->assertFalse($rub1->equals($rub3));
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
     * Tests the RUB add method.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::add
     */
    public function testMoneyAdd(): void
    {
        $rub1 = new RUB(100, 50);
        $rub2 = new RUB(20, 20);

        $result = $rub1->add($rub2);

        $this->assertInstanceOf(RUB::class, $result);
        $this->assertNotSame($result, $rub1);

        $this->assertEquals($result->units(), 120);
        $this->assertEquals($result->nanos(), 70);
    }

    /**
     * Tests the RUB add method with different currencies.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::add
     */
    public function testMoneyAddWithDifferentCurrencies(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency code of the provided $money instance not the same');

        $rub1 = new RUB(100, 50);
        $usd1 = new USD(20, 20);

        $rub1->add($usd1);
    }

    /**
     * Tests the RUB add method with nanos overflow.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::add
     */
    public function testMoneyAddWithNanosOverflow(): void
    {
        $rub1 = new RUB(100, 50);
        $rub2 = new RUB(100, 52);

        $result = $rub1->add($rub2);

        $this->assertInstanceOf(RUB::class, $result);
        $this->assertNotSame($result, $rub1);

        $this->assertEquals($result->units(), 201);
        $this->assertEquals($result->nanos(), 2);
    }

    /**
     * Tests the RUB subtract method.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::subtract
     */
    public function testMoneySubtract(): void
    {
        $rub1 = new RUB(100, 50);
        $rub2 = new RUB(50, 20);

        $result = $rub1->subtract($rub2);

        $this->assertInstanceOf(RUB::class, $result);
        $this->assertNotSame($result, $rub1);

        $this->assertEquals($result->units(), 50);
        $this->assertEquals($result->nanos(), 30);
    }

    /**
     * Tests the RUB subtract method with different currencies.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::subtract
     */
    public function testMoneySubtractWithDifferentCurrencies(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency code of the provided $money instance not the same');

        $rub1 = new RUB(100, 50);
        $usd1 = new USD(50, 20);

        $rub1->subtract($usd1);
    }

    /**
     * Tests the RUB subtract method with nanos overflow.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::subtract
     */
    public function testMoneySubtractWithNanosOverflow(): void
    {
        $rub1 = new RUB(100, 50);
        $rub2 = new RUB(50, 70);

        $result = $rub1->subtract($rub2);

        $this->assertInstanceOf(RUB::class, $result);
        $this->assertNotSame($result, $rub1);

        $this->assertEquals($result->units(), 49);
        $this->assertEquals($result->nanos(), 80);
    }

    /**
     * Tests the RUB multiply method.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::multiply
     */
    public function testMoneyMultiply(): void
    {
        $rub1 = new RUB(20, 30);

        $result = $rub1->multiply(3);

        $this->assertInstanceOf(RUB::class, $result);
        $this->assertNotSame($result, $rub1);

        $this->assertEquals($result->units(), 60);
        $this->assertEquals($result->nanos(), 90);
    }

    /**
     * Tests the RUB multiply method with nanos overflow.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::multiply
     */
    public function testMoneyMultiplyWithNanosOverflow(): void
    {
        $rub1 = new RUB(100, 50);

        $result = $rub1->multiply(2);

        $this->assertInstanceOf(RUB::class, $result);
        $this->assertNotSame($result, $rub1);

        $this->assertEquals($result->units(), 201);
        $this->assertEquals($result->nanos(), 0);
    }

    /**
     * Tests the RUB divide method.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::divide
     */
    public function testMoneyDivide(): void
    {
        $rub1 = new RUB(20, 30);

        $result = $rub1->divide(2);

        $this->assertInstanceOf(RUB::class, $result);
        $this->assertNotSame($result, $rub1);

        $this->assertEquals($result->units(), 10);
        $this->assertEquals($result->nanos(), 15);
    }

    /**
     * Tests the RUB divide method with zero as argument is fail.
     *
     * @return void
     * @covers Eophantasy\Money\RUB::divide
     */
    public function testMoneyDivideByZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$divider cannot be zero');

        $rub1 = new RUB(20, 30);

        $rub1->divide(0);
    }
}

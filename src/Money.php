<?php

/*
 * This file is part of the Eophantasy package.
 *
 * (c) Ilya Sitnikov <sitnikovik@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eophantasy\Money;

use Stringable;
use InvalidArgumentException;
use Eophantasy\Money\Currency\Currency;

/**
 * Abstract class representing a money object.
 */
abstract class Money implements Stringable
{
    /**
     * Minimum allowed value for nanos
     *
     * @var int
     */
    private const MIN_NANOS = 0;

    /**
     * Maximum allowed value for nanos
     *
     * @var int
     */
    private const MAX_NANOS = 99;

    /**
     * Creates a new instance of the Money class.
     * 
     * @param int $units The number of units.
     * @param int $nanos The number of nanos.
     */
    public function __construct(
        protected int $units,
        protected int $nanos,
    ) {}

    /**
     * Returns the currency of the money object.
     * 
     * @return Currency
     */
    abstract public function currency(): Currency;

    /**
     * Returns the number of units in the money object.
     * 
     * @return int
     */
    final public function units(): int
    {
        return $this->units;
    }

    /**
     * Returns the number of nanos in the money object.
     * 
     * @return int
     */
    final public function nanos(): int
    {
        if ($this->nanos < 0) {
            throw new InvalidArgumentException("Nanos cannot be negative.");
        }
        if ($this->nanos > 99) {
            throw new InvalidArgumentException("Nanos cannot be greater than 99.");
        }

        return $this->nanos;
    }

    /**
     * Compares this money object with another money object.
     * 
     * @param Money $money The money object to compare with.
     * @return bool True if the two money objects are equal, false otherwise.
     */
    final public function equals(Money $money): bool
    {
        return $this->currency()->code() === $money->currency()->code()
            && $this->units() === $money->units()
            && $this->nanos() === $money->nanos();
    }

    /**
     * Add another money to current instance.
     *
     * @param Money $money The money object to add.
     * @return bool True if addition was successfull, false otherwise.
     */
    final public function add(Money $money): bool
    {
        if ($this->currency()->code() !== $money->currency()->code()) {
            return false;
        }

        $this->units += $money->units();

        $nextNanos = $this->nanos + $money->nanos();

        if ($nextNanos > self::MAX_NANOS) {
            $toUnits = (int) ($nextNanos / 100);
            $this->units += $toUnits;
            $nextNanos -= 100 * $toUnits;
        }

        $this->nanos = $nextNanos;

        return true;
    }

    /**
     * Subtract money from current instance.
     *
     * @param Money $money The money object to substract.
     * @return boolean True if substraction was successfull, false otherwise.
     */
    final public function subtract(Money $money): bool
    {
        if ($this->currency()->code() !== $money->currency()->code()) {
            return false;
        }

        $this->units -= $money->units();
        $this->nanos -= $money->nanos();

        if ($this->nanos < self::MIN_NANOS) {
            $this->units--;
            $this->nanos = 100 + $this->nanos;
        }

        return true;
    }

    /**
     * Multiply money by specific amount.
     *
     * @param float $amount Amount to multiply.
     * @return bool True if multiplication was successfull, false otherwise.
     */
    final public function multiply(float $amount): bool
    {
        $this->units = (int) round($this->units * $amount);

        $nextNanos = (int) round($this->nanos * $amount);
        
        if ($nextNanos > self::MAX_NANOS) {
            $toUnits = (int) ($nextNanos / 100);
            $this->units += $toUnits;
            $nextNanos -= 100 * $toUnits;
        }

        $this->nanos = $nextNanos;

        return true;
    }

    /**
     * Divide money by specific divider.
     *
     * @param float $divider Divider to divide.
     * @return boolean True if division was successfull, false otherwise.
     */
    final public function divide(float $divider): bool
    {
        if ($divider === 0.0) {
            return false;
        }

        $this->units = (int) round($this->units / $divider);
        $this->nanos = (int) round($this->nanos / $divider);

        return true;
    }
}

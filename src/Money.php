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
     * @return static New instance
     * 
     * @throws InvalidArgumentException On invalid arguments
     */
    final public function add(Money $money): static
    {
        if ($this->currency()->code() !== $money->currency()->code()) {
            throw new InvalidArgumentException('Currency code of the provided $money instance not the same');
        }

        $instance = new static($this->units, $this->nanos);

        $instance->units += $money->units();

        $nextNanos = $instance->nanos + $money->nanos();

        if ($nextNanos > self::MAX_NANOS) {
            $toUnits = (int) ($nextNanos / 100);
            $instance->units += $toUnits;
            $nextNanos -= 100 * $toUnits;
        }

        $instance->nanos = $nextNanos;

        return $instance;
    }

    /**
     * Subtract money from current instance.
     *
     * @param Money $money The money object to substract.
     * @return static New instance
     * 
     * @throws InvalidArgumentException On invalid arguments
     */
    final public function subtract(Money $money): static
    {
        if ($this->currency()->code() !== $money->currency()->code()) {
            throw new InvalidArgumentException('Currency code of the provided $money instance not the same');
        }

        $instance = new static($this->units, $this->nanos);

        $instance->units -= $money->units();
        $instance->nanos -= $money->nanos();

        if ($instance->nanos < self::MIN_NANOS) {
            $instance->units--;
            $instance->nanos = 100 + $instance->nanos;
        }

        return $instance;
    }

    /**
     * Multiply money by specific amount.
     *
     * @param float $amount Amount to multiply.
     * @return static New instance
     */
    final public function multiply(float $amount): static
    {
        $instance = new static($this->units, $this->nanos);

        $instance->units = (int) round($this->units * $amount);

        $nextNanos = (int) round($this->nanos * $amount);
        
        if ($nextNanos > self::MAX_NANOS) {
            $toUnits = (int) ($nextNanos / 100);
            $instance->units += $toUnits;
            $nextNanos -= 100 * $toUnits;
        }

        $instance->nanos = $nextNanos;

        return $instance;
    }

    /**
     * Divide money by specific divider.
     *
     * @param float $divider Divider to divide.
     * @return static New instance
     * 
     * @throws InvalidArgumentException If divider is zero
     */
    final public function divide(float $divider): static
    {
        if ($divider === 0.0) {
            throw new InvalidArgumentException('$divider cannot be zero');
        }

        $instance = new static($this->units, $this->nanos);

        $instance->units = (int) round($this->units / $divider);
        $instance->nanos = (int) round($this->nanos / $divider);

        return $instance;
    }
}

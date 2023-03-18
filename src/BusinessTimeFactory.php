<?php

namespace BusinessTime;

use BusinessTime\Constraint\BusinessTimeConstraint;

/**
 * Factory for making BusinessTime instances with pre-configured constraints.
 *
 * @see BusinessTimeFactoryTest
 */
class BusinessTimeFactory
{
    /** @var BusinessTimeConstraint[] */
    private $constraints;

    /** @var Interval */
    private $precision;

    /** @var int */
    private $iterationLimit = LimitedIterator::DEFAULT_ITERATION_LIMIT;

    /**
     * @param Interval|null          $precision
     * @param BusinessTimeConstraint ...$constraints
     */
    public function __construct(
        Interval $precision = null,
        BusinessTimeConstraint ...$constraints
    ) {
        // Default to hour precision.
        $this->precision = $precision ?? Interval::hour();
        $this->constraints = $constraints;
    }

    /**
     * @param string $time
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function make(string $time): BusinessTime
    {
        $businessTime = new BusinessTime($time);
        $businessTime->setConstraints(...$this->constraints);
        $businessTime->setPrecision($this->precision);
        $businessTime->setIterationLimit($this->iterationLimit);

        return $businessTime;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function now(): BusinessTime
    {
        return $this->make('now');
    }

    /**
     * @param BusinessTimeConstraint ...$constraints
     *
     * @return BusinessTimeFactory
     */
    public function setConstraints(
        BusinessTimeConstraint ...$constraints
    ): self {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @param Interval $precision
     *
     * @return BusinessTimeFactory
     */
    public function setPrecision(Interval $precision): self
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @param int $iterationLimit
     *
     * @return BusinessTimeFactory
     */
    public function setIterationLimit(int $iterationLimit): self
    {
        $this->iterationLimit = $iterationLimit;

        return $this;
    }
}

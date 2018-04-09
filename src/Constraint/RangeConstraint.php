<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Composite\Combinations;
use BusinessTime\Constraint\Narration\BusinessTimeNarrator;
use DateTimeInterface;

/**
 * Constraint that matches business time by comparing to an integer range.
 *
 * The minimum and maximum are inclusive, i.e. >= and <=.
 *
 * Some implementing classes override this to make the max exclusive, as that
 * is more intuitive for e.g. 17 should exclude times from 5pm onwards.
 */
abstract class RangeConstraint implements
    BusinessTimeConstraint,
    BusinessTimeNarrator
{
    use Combinations;

    /** @var int */
    protected $min;

    /** @var int */
    protected $max;

    /**
     * RangeConstraint constructor.
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
        // Keep the min and max within the valid range.
        $min = max($min, $this->minMin());
        $max = min($max, $this->maxMax());

        // Allow backwards order.
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool
    {
        return ($this->relevantValueOf($time) >= $this->min)
            && ($this->relevantValueOf($time) <= $this->max);
    }

    /**
     * @param DateTimeInterface $time
     *
     * @return string
     */
    public function narrate(DateTimeInterface $time): string
    {
        if ($this->isBusinessTime($time)) {
            return BusinessTimeNarrator::DEFAULT_BUSINESS;
        }

        return BusinessTimeNarrator::DEFAULT_NON_BUSINESS;
    }

    /**
     * Get an integer value from the time that is to be compared to this range.
     *
     * @param DateTimeInterface $time
     *
     * @return int
     */
    abstract public function relevantValueOf(DateTimeInterface $time): int;

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    abstract public function maxMax(): int;

    /**
     * Get the minimum possible value of the range.
     *
     * @return int
     */
    abstract public function minMin(): int;

    /**
     * Helper function for converting names to a given numeric index.
     *
     * E.g. converts "January" to 1.
     *
     * @param string|int $name
     * @param array      $index
     *
     * @return int
     */
    public function nameToIndex(string $name, array $index): int
    {
        if (!is_numeric($name)) {
            $name = $index[$name];
        }

        return (int) $name;
    }
}

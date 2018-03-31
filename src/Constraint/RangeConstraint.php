<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Composite\Combinations;
use DateTime;

/**
 * Constraint that matches business time by comparing to an integer range.
 *
 * The minimum and maximum are inclusive, i.e. >= and <=.
 *
 * Some implementing classes override this to make the max exclusive, as that
 * is more intuitive for e.g. 17 should exclude times from 5pm onwards.
 */
abstract class RangeConstraint implements BusinessTimeConstraint
{
    use Combinations;

    /** @var int */
    private $min;

    /** @var int */
    private $max;

    /**
     * RangeConstraint constructor.
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
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
     * @param DateTime $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTime $time): bool
    {
        return ($this->relevantValueOf($time) >= $this->min)
               && ($this->relevantValueOf($time) <= $this->max);
    }

    /**
     * Get an integer value from the time that is to be compared to this range.
     *
     * @param DateTime $time
     *
     * @return int
     */
    abstract public function relevantValueOf(DateTime $time): int;
}

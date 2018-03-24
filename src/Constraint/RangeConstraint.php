<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * Constraint that matches business time by comparing to an integer range.
 *
 * Note that the minimum is inclusive while the maximum is exclusive. For
 * example, a range of 9 to 17 matches 9 but does not match 17.
 */
class RangeConstraint implements BusinessTimeConstraint
{
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
            && ($this->relevantValueOf($time) < $this->max);
    }

    /**
     * Get an integer value from the time that is to be compared to this range.
     *
     * @param DateTime $time
     *
     * @return int
     */
    protected function relevantValueOf(DateTime $time): int
    {
        return $time->getTimestamp();
    }
}

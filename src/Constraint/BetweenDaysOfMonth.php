<?php

namespace BusinessTime\Constraint;

use DateTimeInterface;

/**
 * Business time constraint that matches time between certain days of the month.
 *
 * e.g.
 * new BetweenDaysOfMonth(10, 20) matches days from the 10th to the 20th of a
 * month.
 *
 * @see BetweenDaysOfMonthTest
 */
class BetweenDaysOfMonth extends RangeConstraint
{
    /**
     * Get an integer value from the time that is to be compared to this range.
     *
     * @param DateTimeInterface $time
     *
     * @return int
     */
    public function relevantValueOf(DateTimeInterface $time): int
    {
        return (int) $time->format('j');
    }

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    public function maxMax(): int
    {
        return 31;
    }

    /**
     * Get the minimum possible value of the range.
     *
     * @return int
     */
    public function minMin(): int
    {
        return 1;
    }
}

<?php

namespace BusinessTime\Constraint;

use DateTime;

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
     * @param DateTime $time
     *
     * @return int
     */
    public function relevantValueOf(DateTime $time): int
    {
        return (int) $time->format('j');
    }
}

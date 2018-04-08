<?php

namespace BusinessTime\Constraint;

use BusinessTime\BusinessTime;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;

/**
 * Constraint that matches business time between certain hours of the day.
 *
 * e.g.
 * new BetweenHourOfDay(9, 17) matches any time after 9am and before 5pm.
 *
 * @see HoursOfDay
 */
class BetweenHoursOfDay extends RangeConstraint
{
    /**
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min = 0, int $max = 24)
    {
        // Subtract one from the max as we want to match it exclusively for
        // times of day. E.g. 17 should be a cut off at 5pm, excluding times
        // from 5pm onwards.
        parent::__construct($min, $max - 1);
    }

    /**
     * @param DateTimeInterface $time
     *
     * @return int
     */
    public function relevantValueOf(DateTimeInterface $time): int
    {
        return BusinessTime::fromDti($time)->hour;
    }

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    public function maxMax(): int
    {
        return 23;
    }

    /**
     * Get the minimum possible value of the range.
     *
     * @return int
     */
    public function minMin(): int
    {
        return 0;
    }
}

<?php

namespace BusinessTime\Constraint;

use Carbon\Carbon;
use DateTime;

/**
 * Business time constraint that matches times between specific dates
 * inclusively.
 *
 * e.g.
 * new BetweenDates('2018-05-23', '2019-01-01') matches any time between 23rd
 * May 2018 and 1st January 2019.
 *
 * @see BetweenDatesTest
 */
class BetweenDates extends RangeConstraint
{
    /**
     * @param string $min e.g. '2018-05-23'
     * @param string $max e.g. '2019-01-01'
     */
    public function __construct(string $min, string $max)
    {
        parent::__construct(
            (new Carbon($min))->startOfDay()->getTimestamp(),
            (new Carbon($max))->endOfDay()->getTimestamp()
        );
    }

    /**
     * Get an integer value from the time that is to be compared to this range.
     *
     * @param DateTime $time
     *
     * @return int
     */
    public function relevantValueOf(DateTime $time): int
    {
        return $time->getTimestamp();
    }

    /**
     * Get the maximum possible value of the range.
     *
     * @return int
     */
    public function maxMax(): int
    {
        return PHP_INT_MAX;
    }

    /**
     * Get the minimum possible value of the range.
     *
     * @return int
     */
    public function minMin(): int
    {
        // Hopefully no-one is making business time calculations for before the
        // formation of the Earth, but managers gonna manage.
        return PHP_INT_MIN;
    }
}

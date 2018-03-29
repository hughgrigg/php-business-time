<?php

namespace BusinessTime\Constraint;

use Carbon\Carbon;
use DateTime;

/**
 * Constraint that matches business time between certain hours of the day.
 *
 * e.g. new BetweenHourOfDay(9, 17) matches any time between 9am and 5pm.
 *
 * @see HoursOfDay
 */
class BetweenHoursOfDay extends RangeConstraint
{
    /**
     * @param DateTime $time
     *
     * @return int
     */
    protected function relevantValueOf(DateTime $time): int
    {
        return Carbon::instance($time)->hour;
    }
}

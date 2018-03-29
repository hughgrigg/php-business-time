<?php

namespace BusinessTime\Constraint;

use Carbon\Carbon;
use DateTime;

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
     * @param DateTime $time
     *
     * @return int
     */
    public function relevantValueOf(DateTime $time): int
    {
        return Carbon::instance($time)->hour;
    }
}

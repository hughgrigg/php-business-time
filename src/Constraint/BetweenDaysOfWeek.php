<?php

namespace BusinessTime\Constraint;

use Carbon\Carbon;
use DateTime;

/**
 * Constraint that matches business time between certain hours of the day.
 *
 * e.g. new BetweenDaysOfWeek('Monday', 'Friday') matches week days apart from
 * Friday. Be careful with the inclusive minimum vs exclusive maximum.
 *
 * @see DaysOfWeek
 * @see WeekDays
 */
class BetweenDaysOfWeek extends RangeConstraint
{
    public const NAME_INDEX = [
        'Sunday'    => 0,
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6,
        'Sun'       => 0,
        'Mon'       => 1,
        'Tues'      => 2,
        'Wed'       => 3,
        'Thur'      => 4,
        'Fri'       => 5,
        'Sat'       => 6,
    ];

    /**
     * @param string|int $min e.g. 'Sunday' or 0
     * @param string|int $max e.g. 'Saturday' or 6
     */
    public function __construct(string $min, string $max)
    {
        if (!is_numeric($min)) {
            $min = self::NAME_INDEX[$min];
        }
        if (!is_numeric($max)) {
            $max = self::NAME_INDEX[$max];
        }

        parent::__construct((int) $min, (int) $max);
    }

    /**
     * @param DateTime $time
     *
     * @return int
     */
    protected function relevantValueOf(DateTime $time): int
    {
        return Carbon::instance($time)->dayOfWeek;
    }
}

<?php

namespace BusinessTime\Constraint;

/**
 * Constraint that matches the given days of week with their full names.
 *
 * @see DaysOfWeekTest
 */
class DaysOfWeek extends FormatConstraint
{
    public const NAME_INDEX = [
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6,
        'Sunday'    => 7,
        'Mon'       => 1,
        'Tues'      => 2,
        'Wed'       => 3,
        'Thur'      => 4,
        'Fri'       => 5,
        'Sat'       => 6,
        'Sun'       => 7,
    ];

    /**
     * @param string ...$daysOfWeek e.g. Wednesday, Thursday
     */
    public function __construct(string ...$daysOfWeek)
    {
        parent::__construct('l', ...$daysOfWeek);
    }
}

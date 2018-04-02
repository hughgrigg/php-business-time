<?php

namespace BusinessTime\Constraint;

/**
 * Constraint that matches the given hours of the day with their numeric index.
 *
 * e.g.
 * new HoursOfDay(8, 13, 23) matches 8am, 1pm and 11pm.
 *
 * @see BetweenHoursOfDay
 * @see HoursOfDayTest
 */
class HoursOfDay extends FormatConstraint
{
    /**
     * HoursOfDay constructor.
     *
     * @param int ...$hoursOfDays e.g. 8, 13, 23
     */
    public function __construct(int ...$hoursOfDays)
    {
        parent::__construct('G', ...$hoursOfDays);
    }
}

<?php

namespace BusinessTime\Constraint;

/**
 * Constraint that matches the given hours of the day with their numeric index.
 *
 * E.g. 1pm = 13
 *
 * @see BetweenHoursOfDay
 */
class HoursOfDay extends FormatConstraint
{
    /**
     * HoursOfDay constructor.
     *
     * @param int ...$hoursOfDay e.g. 8, 13, 23
     */
    public function __construct(int ...$hoursOfDay)
    {
        parent::__construct('H', ...$hoursOfDay);
    }
}

<?php

namespace BusinessTime\Constraint;

/**
 * Business time constraint that matches specific indexed days of the month as
 * business time.
 *
 * e.g.
 * new DaysOfMonth(1, 8, 23) matches the 1st, 8th and 23rd of any month.
 *
 * @see DaysOfMonthTest
 */
class DaysOfMonth extends FormatConstraint
{
    /**
     * @param int ...$daysOfMonth
     */
    public function __construct(int ...$daysOfMonth)
    {
        parent::__construct('j', ...$daysOfMonth);
    }
}

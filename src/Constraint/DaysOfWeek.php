<?php

namespace BusinessTime\Constraint;

/**
 * Constraint that matches the given days of week with their full names.
 */
class DaysOfWeek extends FormatConstraint
{
    /**
     * @param string ...$hoursOfDay e.g. Wednesday, Thursday
     */
    public function __construct(string ...$hoursOfDay)
    {
        parent::__construct('l', ...$hoursOfDay);
    }
}

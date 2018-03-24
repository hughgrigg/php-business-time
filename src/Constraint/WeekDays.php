<?php

namespace BusinessTime\Constraint;

/**
 * Constraint that matches any week day Monday to Friday as business time.
 */
class WeekDays extends FormatConstraint
{
    public function __construct()
    {
        parent::__construct(
            'l',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday'
        );
    }
}

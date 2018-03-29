<?php

namespace BusinessTime\Constraint;

/**
 * Constraint that matches any time on Saturdays or Sundays as business time.
 */
class Weekends extends FormatConstraint
{
    public function __construct()
    {
        parent::__construct(
            'l',
            'Saturday',
            'Sunday'
        );
    }
}

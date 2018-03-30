<?php

namespace BusinessTime\Constraint;

use BusinessTime\Tests\Unit\Constraint\WeekendsTest;

/**
 * Constraint that matches any time on Saturdays or Sundays as business time.
 *
 * @see WeekendsTest
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

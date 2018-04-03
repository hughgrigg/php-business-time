<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Narration\BusinessTimeNarrator;
use BusinessTime\Tests\Unit\Constraint\WeekendsTest;
use DateTimeInterface;

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

    /**
     * @param DateTimeInterface $time
     *
     * @return string
     */
    public function narrate(DateTimeInterface $time): string
    {
        if ($this->isBusinessTime($time)) {
            return BusinessTimeNarrator::DEFAULT_BUSINESS;
        }

        return 'the weekend';
    }
}

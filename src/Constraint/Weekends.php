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
        /*
         * The Weekends constraint matches weekends as business time.
         *
         * @see WeekDays is the opposite.
         */
        if ($this->isBusinessTime($time)) {
            return 'the weekend';
        }

        return BusinessTimeNarrator::DEFAULT_NON_BUSINESS;
    }
}

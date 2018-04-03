<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Narration\BusinessTimeNarrator;
use DateTimeInterface;

/**
 * Constraint that matches any week day Monday to Friday as business time.
 *
 * @see WeekDaysTest
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

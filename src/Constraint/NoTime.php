<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Composite\Combinations;
use DateTime;
use DateTimeInterface;

/**
 * Constraint that matches no times as business time.
 */
class NoTime implements BusinessTimeConstraint
{
    use Combinations;

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool
    {
        return false;
    }
}

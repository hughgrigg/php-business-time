<?php

namespace BusinessTime\Constraint;

use BusinessTime\Constraint\Composite\Combinations;
use DateTimeInterface;

/**
 * Constraint that matches any time as business time.
 */
class AnyTime implements BusinessTimeConstraint
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
        return true;
    }
}

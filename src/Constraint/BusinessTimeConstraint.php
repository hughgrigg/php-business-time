<?php

namespace BusinessTime\Constraint;

use DateTimeInterface;

/**
 * A constraint rule to determine whether a given time is business time or not.
 */
interface BusinessTimeConstraint
{
    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool;
}

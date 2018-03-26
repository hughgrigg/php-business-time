<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * A constraint rule to determine whether a given time is business time or not.
 */
interface BusinessTimeConstraint
{
    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTime $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTime $time): bool;
}

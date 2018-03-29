<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * Constraint that matches any time as business time.
 */
class AnyTime implements BusinessTimeConstraint
{
    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTime $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTime $time): bool
    {
        return true;
    }
}

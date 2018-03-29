<?php

namespace BusinessTime\Constraint;

use DateTime;

/**
 * Constraint that matches no times as business time.
 */
class NoTime implements BusinessTimeConstraint
{
    use Combinations;

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTime $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTime $time): bool
    {
        return false;
    }
}

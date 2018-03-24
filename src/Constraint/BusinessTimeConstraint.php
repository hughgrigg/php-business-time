<?php

namespace BusinessTime\Constraint;

use DateTime;

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

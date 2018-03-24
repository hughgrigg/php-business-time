<?php

namespace BusinessTime\Constraint;

use DateTime;

interface BusinessTimeNarrator
{
    /**
     * Get a business-relevant description for the given time.
     *
     * @param DateTime $time
     *
     * @return string
     */
    public function describe(DateTime $time): string;
}

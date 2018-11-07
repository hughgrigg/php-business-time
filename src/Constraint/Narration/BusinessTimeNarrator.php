<?php

namespace BusinessTime\Constraint\Narration;

use DateTimeInterface;

interface BusinessTimeNarrator
{
    const DEFAULT_BUSINESS = 'business hours';
    const DEFAULT_NON_BUSINESS = 'outside business hours';

    /**
     * Get a business-relevant description for the given time.
     *
     * @param DateTimeInterface $time
     *
     * @return string
     */
    public function narrate(DateTimeInterface $time): string;
}

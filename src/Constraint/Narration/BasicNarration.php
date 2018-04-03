<?php

namespace BusinessTime\Constraint\Narration;

use DateTimeInterface;

/**
 * A reasonable basic behaviour for narrating business times.
 */
trait BasicNarration
{
    /**
     * Get a business-relevant description for the given time.
     *
     * @param DateTimeInterface $time
     *
     * @return string
     */
    public function narrate(DateTimeInterface $time): string
    {
        return $time->format('l jS F H:i');
    }
}

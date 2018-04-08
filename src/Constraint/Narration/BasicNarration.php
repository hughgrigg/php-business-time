<?php

namespace BusinessTime\Constraint\Narration;

use BusinessTime\BusinessTime;
use Carbon\Carbon;
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
        $time = BusinessTime::fromDti($time);
        if ($time->secondsSinceMidnight() === 0) {
            // Only narrate the day if it's midnight.
            return $time->format('l jS F Y');
        }

        return $time->format('l jS F Y H:i');
    }
}

<?php

namespace BusinessTime\Remote\WebCalFi;

use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\Narration\BusinessTimeNarrator;
use DateTimeInterface;

/**
 * Business time constraint that uses dates retrieved from the WebCal.Fi
 * service. The dates are treated as non-business time.
 */
class WebCalFiConstraint implements BusinessTimeConstraint, BusinessTimeNarrator
{
    const FORMAT = 'Y-m-d';

    /** @var WebCalFiDate[] */
    private $dates = [];

    /**
     * @param WebCalFiDate ...$dates Dates that are not business time.
     */
    public function __construct(WebCalFiDate ...$dates)
    {
        foreach ($dates as $date) {
            $this->dates[$date->date->format(self::FORMAT)] = $date;
        }
    }

    /**
     * Is the given time business time according to this constraint?
     *
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    public function isBusinessTime(DateTimeInterface $time): bool
    {
        return $this->dateFor($time) === null;
    }

    /**
     * Get a business-relevant description for the given time.
     *
     * @param DateTimeInterface $time
     *
     * @return string
     */
    public function narrate(DateTimeInterface $time): string
    {
        $date = $this->dateFor($time);
        if ($date === null) {
            return BusinessTimeNarrator::DEFAULT_BUSINESS;
        }

        return $date->name;
    }

    /**
     * @param DateTimeInterface $time
     *
     * @return WebCalFiDate|null
     */
    private function dateFor(DateTimeInterface $time)
    {
        if (isset($this->dates[$time->format(self::FORMAT)])) {
            return $this->dates[$time->format(self::FORMAT)];
        }
    }
}

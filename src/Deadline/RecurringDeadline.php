<?php

namespace BusinessTime\Deadline;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\Composite\All;
use DateTimeInterface;

/**
 * A recurring cut-off point in time. For example, orders might be shipped at
 * 11am on weekdays, and this class could be used to get the next shipping time.
 *
 * @see RecurringDeadlineTest
 */
class RecurringDeadline
{
    /** @var All|BusinessTimeConstraint[] */
    private $deadlineConstraints;

    /**
     * Be careful to ensure that the given constraints will reliably match a
     * deadline without too many iterations.
     *
     * @param BusinessTimeConstraint ...$deadlineConstraints
     */
    public function __construct(BusinessTimeConstraint ...$deadlineConstraints)
    {
        $this->deadlineConstraints = new All(...$deadlineConstraints);
    }

    /**
     * Get the next time this deadline will occur after a given time.
     *
     * It's possible this will loop infinitely if the given constraints never
     * match a time with the given precision.
     *
     * @param BusinessTime $time
     *
     * @return BusinessTime
     */
    public function nextOccurrenceFrom(BusinessTime $time): BusinessTime
    {
        $time = $time->copy();

        // Advance until we're out of a current deadline (as we want the next).
        while ($this->isDeadline($time)) {
            $time = $time->add($time->precision());
        }

        // Advance until we hit the next deadline.
        while (!$this->isDeadline($time)) {
            $time = $time->add($time->precision());
        }

        return $time->floorToPrecision();
    }

    /**
     * Get the previous time this deadline occurred after a given time.
     *
     * It's possible this will loop infinitely if the given constraints never
     * match a time with the given precision.
     *
     * @param BusinessTime $time
     *
     * @return BusinessTime
     */
    public function previousOccurrenceFrom(BusinessTime $time): BusinessTime
    {
        $time = $time->copy();

        // Regress until we're out of a current deadline (as we want the
        // previous one).
        while ($this->isDeadline($time)) {
            $time = $time->sub($time->precision());
        }

        // Regress until we hit the next deadline.
        while (!$this->isDeadline($time)) {
            $time = $time->sub($time->precision());
        }

        return $time->floorToPrecision();
    }

    /**
     * @return bool
     */
    public function hasPassedToday(): bool
    {
        $passed = $this->firstTimePassedBetween(
            BusinessTime::today(),
            BusinessTime::today()->endOfDay()
        );

        return $passed && $passed->lt(BusinessTime::now());
    }

    /**
     * @param BusinessTime $start
     * @param BusinessTime $end
     *
     * @return bool
     */
    public function hasPassedBetween(
        BusinessTime $start,
        BusinessTime $end
    ): bool {
        return (bool) $this->firstTimePassedBetween($start, $end);
    }

    /**
     * Get the first time the deadline passes between two given times, if any.
     *
     * @param BusinessTime $start
     * @param BusinessTime $end
     *
     * @return BusinessTime|null
     */
    public function firstTimePassedBetween(
        BusinessTime $start,
        BusinessTime $end
    ) {
        $time = $start->copy();
        while ($time->lte($end)) {
            if ($this->isDeadline($time)) {
                return $time->floorToPrecision();
            }
            $time = $time->add($time->precision());
        }
    }

    /**
     * @param DateTimeInterface $time
     *
     * @return bool
     */
    private function isDeadline(DateTimeInterface $time): bool
    {
        return $this->deadlineConstraints->isBusinessTime($time);
    }
}

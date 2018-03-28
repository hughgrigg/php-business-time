<?php

namespace BusinessTime;

use BusinessTime\Constraint\All;
use BusinessTime\Constraint\BetweenHoursOfDay;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\WeekDays;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Extend Carbon\Carbon with business time logic.
 */
class BusinessTime extends Carbon
{
    /** @var All */
    private $businessTimeConstraints;

    /** @var Interval */
    private $lengthOfBusinessDay;

    /** @var Interval */
    private $precision;

    /**
     * Is it business time?
     *
     * @return bool
     */
    public function isBusinessTime(): bool
    {
        return $this->businessTimeConstraints()->isBusinessTime($this);
    }

    /**
     * Get the date time after adding one whole business day.
     *
     * @see AddBusinessDaysTest
     *
     * @return BusinessTime
     * @throws \InvalidArgumentException
     */
    public function addBusinessDay(): self
    {
        return $this->addBusinessDays(1);
    }

    /**
     * Get the date time after adding an amount of partial business days.
     *
     * The amount can be fractional, and the accuracy depends on the current
     * precision (the default is hour precision).
     *
     * @see AddBusinessDaysTest
     *
     * @param float $businessDaysToAdd
     *
     * @return BusinessTime
     * @throws \InvalidArgumentException
     */
    public function addBusinessDays(float $businessDaysToAdd): self
    {
        // Optimise by jumping ahead in whole days first.
        $daysToJump = max((int) $businessDaysToAdd, 0);
        $next = $this->copy()->addDays($daysToJump);

        // We need to check how much business time we actually covered by
        // skipping ahead in days. This also solves the "intuitive problem" that
        // Monday 09:00 + 1 business day could technically be Monday 17:00, but
        // intuitively should be Tuesday 09:00.
        $businessDaysToAdd -= $this->diffInPartialBusinessDays($next);

        /** @var Interval $decrement */
        $decrement = $this->precision()->inDays()
            / $this->lengthOfBusinessDay()->inDays();

        while ($businessDaysToAdd > 0) {
            if ($next->isBusinessTime()) {
                $businessDaysToAdd -= $decrement;
            }
            $next = $next->add($this->precision());
        }

        return $next;
    }

    /**
     * Get the date time after adding one whole business hour.
     *
     * @return BusinessTime
     * @throws \InvalidArgumentException
     */
    public function addBusinessHour(): self
    {
        return $this->addBusinessHours(1);
    }

    /**
     * @param float $businessHours
     *
     * @return BusinessTime
     * @throws \InvalidArgumentException
     */
    public function addBusinessHours(float $businessHours): self
    {
        // We can optimise by first jumping ahead by the equivalent amount of
        // full days. We use the desired hours - 1 for this to avoid jumping too
        // far. For example, jumping 1 day for 8 hours could skip into Saturday
        // from Friday, which is incorrect.
        $fullDays = max(
            (int) (
                ($businessHours - 1) / $this->lengthOfBusinessDay()->inHours()
            ),
            0
        );
        $jumpDays = $this->copy()->addDays($fullDays);
        $businessHours -= ($fullDays * $this->lengthOfBusinessDay()->inHours());

        // The number of business hours to add will then be at least the
        // number of real hours, so we can also jump ahead that much. Again we
        // use the -1 tactic to avoid jumping too far.
        $fullHours = max((int) $businessHours - 1, 0);
        $jumpHours = $jumpDays->addHours($fullHours);
        $businessHours -= $fullHours;

        // TODO: allow for gaps in business hours? Currently it's possible we
        // might jump over a gap and miss it in the calculation.

        // Then we iterate over the remaining time at our precision-level.
        $next = $jumpHours->copy();
        while ($jumpHours->diffInPartialBusinessHours($next) < $businessHours) {
            $next = $next->add($this->precision());
        }

        return $next;
    }

    /**
     * Get the difference between this time and another in whole business days.
     *
     * @see DiffInBusinessDaysTest
     *
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    public function diffInBusinessDays(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): int {
        return (int) $this->diffInPartialBusinessDays($time, $absolute);
    }

    /**
     * Get the difference between this time and another in whole business hours.
     *
     * @see DiffInBusinessHoursTest
     *
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return int
     */
    public function diffInBusinessHours(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): int {
        return (int) $this->diffInPartialBusinessHours($time, $absolute);
    }

    /**
     * Get the difference between this time and another in fractional business
     * days, calculated in intervals the size of the precision.
     *
     * @see DiffInBusinessDaysTest
     *
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return float
     * @throws InvalidArgumentException
     */
    public function diffInPartialBusinessDays(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): float {
        return $this->diffInBusinessTime($time, $absolute)
            / $this->lengthOfBusinessDay()->asMultipleOf($this->precision());
    }

    /**
     * Get the difference between this time and another in fractional business
     * hours, calculated in intervals the size of the precision.
     *
     * @see DiffInBusinessHoursTest
     *
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return float
     */
    public function diffInPartialBusinessHours(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): float {
        return $this->diffInBusinessTime($time, $absolute)
            * $this->precision()->inHours();
    }

    /**
     * Get a diff in business time as an interval.
     *
     * Note that seconds are only used as the unit here, not the precision.
     * E.g. with hour precision, we will iterate in steps of one hour, then
     * multiply the result to get the amount in seconds.
     *
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return Interval
     */
    public function diffBusiness(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): Interval {
        return Interval::seconds(
            $this->diffInBusinessTime($time, $absolute)
            * $this->precision()->inSeconds()
        );
    }

    /**
     * @return Interval
     * @throws InvalidArgumentException
     */
    public function lengthOfBusinessDay(): Interval
    {
        if ($this->lengthOfBusinessDay === null) {
            // Determine the length of the business day based on the current
            // constraints.
            $this->determineLengthOfBusinessDay();
        }

        return $this->lengthOfBusinessDay;
    }

    /**
     * @param DateInterval $length
     *
     * @return BusinessTime
     * @throws InvalidArgumentException
     */
    public function setLengthOfBusinessDay(DateInterval $length): self
    {
        $this->lengthOfBusinessDay = Interval::instance($length);
        if ($this->lengthOfBusinessDay->inMinutes() <= 0) {
            throw new InvalidArgumentException(
                'Business day cannot be zero-length.'
            );
        }
        if ($this->lengthOfBusinessDay->inHours() > 24) {
            throw new InvalidArgumentException(
                sprintf(
                    <<<'ERR'
Length of business day cannot be more than 24 hours (was set to %.2f hours).
ERR
                    ,
                    $this->lengthOfBusinessDay->inHours()
                )
            );
        }

        return $this;
    }

    /**
     * @param DateTime $typicalDay
     *
     * @return BusinessTime
     * @throws InvalidArgumentException
     */
    public function determineLengthOfBusinessDay(
        ?DateTime $typicalDay = null
    ): self {
        if ($typicalDay === null) {
            // Default to the length of a reasonable guess at a typical day.
            // We're using a fixed specific day for the default to keep
            // behaviour consistent.
            $typicalDay = new self('Wednesday 23rd May 2018');
        }
        // Ensure we have a Carbon instance.
        $typicalDay = self::instance($typicalDay);

        return $this->setLengthOfBusinessDay(
            $this->copy()
                ->setTimestamp($typicalDay->startOfDay()->getTimestamp())
                ->diffBusiness($typicalDay->endOfDay())
        );
    }

    /**
     * @param BusinessTimeConstraint ...$constraints
     *
     * @return BusinessTime
     */
    public function setBusinessTimeConstraints(
        BusinessTimeConstraint ...$constraints
    ): self {
        $this->businessTimeConstraints = new All(...$constraints);

        return $this;
    }

    /**
     * @param BusinessTimeConstraint ...$constraints
     *
     * @return BusinessTime
     */
    public function addBusinessTimeConstraints(
        BusinessTimeConstraint ...$constraints
    ): self {
        $this->setBusinessTimeConstraints(
            new All($constraints, ...$this->businessTimeConstraints())
        );

        return $this;
    }

    /**
     * Get the business time constraints
     *
     * @return BusinessTimeConstraint
     */
    public function businessTimeConstraints(): BusinessTimeConstraint
    {
        if ($this->businessTimeConstraints === null) {
            // Default to week days 09:00 - 17:00.
            $this->businessTimeConstraints = new All(
                new WeekDays(),
                new BetweenHoursOfDay(9, 17)
            );
        }

        return $this->businessTimeConstraints;
    }

    /**
     * Get the size of interval used when making business time calculations.
     *
     * @return Interval
     */
    public function precision(): Interval
    {
        if ($this->precision === null) {
            // Default to hour precision.
            $this->precision = Interval::hour();
        }

        return $this->precision;
    }

    /**
     * Set the interval size of the precision used to make business time
     * calculations. The finer the precision, the more iterations are required
     * to make calculations.
     *
     * The default precision is one hour. If you're not calculating with
     * intervals smaller than that (e.g. 09:00 to 17:00), there is no benefit
     * to increasing this.
     *
     * If you do need finer precision, avoid setting it more finely than
     * necessary. For example, 15-minute precision is accurate enough for
     * timings like 09:15 to 17:30. Minute-level precision would not be more
     * accurate in that case, but would require 15x more iterations.
     *
     * @param DateInterval $precision
     *
     * @return BusinessTime
     * @throws InvalidArgumentException
     */
    public function setPrecision(DateInterval $precision): self
    {
        $this->precision = Interval::instance($precision);

        return $this;
    }

    /**
     * Difference in business time measured in units of the current precision.
     *
     * This is calculated by stepping through the time period in steps of the
     * precision. Finer precision means more steps but a potentially more
     * accurate result.
     *
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return int
     */
    private function diffInBusinessTime(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): int {
        // TODO: iterate directly here to avoid performance issue of repeated
        // callbacks.

        return $this->diffFiltered(
            $this->precision(),
            function (DateTime $time): bool {
                /** @noinspection NullPointerExceptionInspection */
                $businessTime = $this->copy()->setTimestamp(
                    $time->getTimestamp()
                );

                return $businessTime->isBusinessTime();
            },
            $time,
            $absolute
        );
    }
}

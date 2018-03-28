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
        // TODO: should we round start and end to nearest precision first? That
        // would keep the results "clean" and is reasonable considering we have
        // the precision anyway.

        // TODO: handle negative addition.

        // Jump ahead in whole days first, because the business days to add
        // will be at least this much. This also solves the "intuitive
        // problem" that Monday 09:00 + 1 business day could technically be
        // Monday 17:00, but intuitively should be Tuesday 09:00.
        $daysToJump = (int) $businessDaysToAdd;
        $next = $this->copy()->addDays($daysToJump);

        // We need to check how much business time we actually covered by
        // skipping ahead in days.

        // TODO: can we avoid doing a back-track of iterations over the
        // skipped days here?
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
     * @param float $businessHoursToAdd
     *
     * @return BusinessTime
     * @throws \InvalidArgumentException
     */
    public function addBusinessHours(float $businessHoursToAdd): self
    {
        // TODO: should we round start and end to nearest precision first? That
        // would keep the results "clean" and is reasonable considering we have
        // the precision anyway.

        // TODO: handle negative addition.

        $next = $this->copy();
        $decrement = $this->precision()->inHours();
        while ($businessHoursToAdd > 0) {
            if ($next->isBusinessTime()) {
                $businessHoursToAdd -= $decrement;
            }
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
     * Get the first business time after the start of this day.
     *
     * @return BusinessTime
     */
    public function startOfBusinessDay(): self
    {
        $start = $this->copy()->startOfDay();
        while (!$start->isBusinessTime()) {
            $start = $start->add($this->precision());
        }

        return $start;
    }

    /**
     * Get the last business time before the end of this day.
     *
     * @return BusinessTime
     */
    public function endOfBusinessDay(): self
    {
        $end = $this->copy()->endOfDay();
        while (!$end->isBusinessTime()) {
            $end = $end->sub($this->precision());
        }

        return $end;
    }

    /**
     * Get this date time floored to the given precision interval in terms of
     * seconds since the epoch.
     *
     * Consider methods like startOfBusinessDay() or startOfMonth(), as those
     * are more appropriate in many situations.
     *
     * @see RoundTest
     *
     * @param DateInterval|null $precision
     *
     * @return BusinessTime
     */
    public function floor(?DateInterval $precision = null): self
    {
        $seconds = Interval::instance($precision ?: $this->precision())
            ->inSeconds();

        return $this->copy()->setTimestamp(
            floor($this->timestamp / $seconds) * $seconds
        );
    }

    /**
     * Get this date time rounded to the given precision interval.
     *
     * @see RoundTest
     *
     * @param DateInterval|null $precision
     *
     * @return BusinessTime
     */
    public function round(?DateInterval $precision = null): self
    {
        $seconds = Interval::instance($precision ?: $this->precision())
            ->inSeconds();

        return $this->copy()->setTimestamp(
            round($this->timestamp / $seconds) * $seconds
        );
    }

    /**
     * Get this date time ceil-ed to the given precision interval.
     *
     * Consider methods like endOfBusinessDay() or endOfMonth(), as those
     * are more appropriate in many situations.
     *
     * @see RoundTest
     *
     * @param DateInterval|null $precision
     *
     * @return BusinessTime
     */
    public function ceil(?DateInterval $precision = null): self
    {
        $seconds = Interval::instance($precision ?: $this->precision())
            ->inSeconds();

        return $this->copy()->setTimestamp(
            ceil($this->timestamp / $seconds) * $seconds
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
        // TODO: should we round start and end to nearest precision first? That
        // would keep the results "clean" and is reasonable considering we have
        // the precision anyway.

        // We're taking a basic approach with some variables and a loop here as
        // it turns out to be ~25% faster than using Carbon::diffFiltered().

        $start = $this;
        $end = $time;
        $sign = 1;
        // Swap if we're diffing back in time.
        if ($this > $time) {
            $start = $time;
            $end = $this;
            // We only need to negate if absolute is false.
            $sign = $absolute ? 1 : -1;
        }

        // Count the business time diff by iterating in steps the length of the
        // precision and checking if each step counts as business time.
        $diff = 0;
        $next = $start->copy();
        while ($next < $end) {
            if ($next->isBusinessTime()) {
                ++$diff;
            }
            $next = $next->add($this->precision());
        }

        return $diff * $sign;
    }
}

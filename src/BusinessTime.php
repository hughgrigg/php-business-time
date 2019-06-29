<?php

namespace BusinessTime;

use BusinessTime\Constraint\AnyTime;
use BusinessTime\Constraint\BetweenHoursOfDay;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\Composite\All;
use BusinessTime\Constraint\Narration\BusinessTimeNarrator;
use BusinessTime\Constraint\Narration\DefaultNarrator;
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
    /** @var All|BusinessTimeConstraint[] */
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
     * Is it a business day?
     *
     * @return bool
     */
    public function isBusinessDay(): bool
    {
        return $this->startOfBusinessDay()->isSameDay($this);
    }

    /**
     * Get the date time after adding one whole business day.
     *
     * @see AddBusinessDaysTest
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
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
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function addBusinessDays(float $businessDaysToAdd): self
    {
        if ($businessDaysToAdd < 0) {
            return $this->subBusinessDays($businessDaysToAdd * -1);
        }

        // Jump ahead in whole days first, because the business days to add
        // will be at least this much. This solves the "intuitive problem" that
        // Monday 09:00 + 1 business day could technically be Monday 17:00, but
        // intuitively should be Tuesday 09:00.
        $daysToJump = (int) $businessDaysToAdd;
        /** @var BusinessTime $next */
        $next = $this->copy()->addDays($daysToJump);

        // We need to check how much business time we actually covered by
        // skipping ahead in days.
        $businessDaysToAdd -= $this->diffInPartialBusinessDays($next);

        $decrement = $this->precision()->inDays()
            / $this->lengthOfBusinessDay()->inDays();

        while ($businessDaysToAdd > 0) {
            /* @scrutinizer ignore-call */
            if ($next->isBusinessTime()) {
                $businessDaysToAdd -= $decrement;
            }
            $next = $next->add($this->precision());
        }

        return $next;
    }

    /**
     * Get the date time after subtracting one business day.
     *
     * @see SubBusinessDaysTest
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function subBusinessDay(): self
    {
        return $this->subBusinessDays(1);
    }

    /**
     * Get the date time after subtracting an amount of business days.
     *
     * @see SubBusinessDaysTest
     *
     * @param float $businessDaysToSub
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function subBusinessDays(float $businessDaysToSub): self
    {
        if ($businessDaysToSub < 0) {
            return $this->addBusinessDays($businessDaysToSub * -1);
        }

        // Jump back in whole days first, because the business days to subtract
        // will be at least this much. This also solves the "intuitive
        // problem" that Tuesday 17:00 - 1 business day could technically be
        // Tuesday 09:00, but intuitively should be Monday 17:00.
        $daysToJump = (int) $businessDaysToSub;
        $prev = $this->copy()->subDays($daysToJump);

        // We need to check how much business time we actually covered by
        // skipping back in days.
        $businessDaysToSub -= $this->diffInPartialBusinessDays($prev);

        $decrement = $this->precision()->inDays()
            / $this->lengthOfBusinessDay()->inDays();

        while ($businessDaysToSub > 0) {
            $prev = $prev->sub($this->precision());
            if ($prev->isBusinessTime()) {
                $businessDaysToSub -= $decrement;
            }
        }

        return $prev;
    }

    /**
     * Get the date time after adding one business hour.
     *
     * @see AddBusinessHoursTest
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function addBusinessHour(): self
    {
        return $this->addBusinessHours(1);
    }

    /**
     * Get the date time after adding an amount of business hours.
     *
     * @param float $businessHoursToAdd
     *
     * @see AddBusinessHoursTest
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function addBusinessHours(float $businessHoursToAdd): self
    {
        if ($businessHoursToAdd < 0) {
            return $this->subBusinessHours($businessHoursToAdd * -1);
        }

        /** @var BusinessTime $next */
        $next = $this->copy();
        $decrement = $this->precision()->inHours();
        while ($businessHoursToAdd > 0) {
            /* @scrutinizer ignore-call */
            if ($next->isBusinessTime()) {
                $businessHoursToAdd -= $decrement;
            }
            $next = $next->add($this->precision());
        }

        return $next;
    }

    /**
     * Get the date time after subtracting one business hour.
     *
     * @see SubBusinessHoursTest
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function subBusinessHour(): self
    {
        return $this->subBusinessHours(1);
    }

    /**
     * Get the date time after subtracting an amount of business hours.
     *
     * @see SubBusinessHoursTest
     *
     * @param float $businessHoursToSub
     *
     * @throws \InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function subBusinessHours(float $businessHoursToSub): self
    {
        if ($businessHoursToSub < 0) {
            return $this->addBusinessHours($businessHoursToSub * -1);
        }

        $prev = $this->copy();
        $decrement = $this->precision()->inHours();
        while ($businessHoursToSub > 0) {
            $prev = $prev->sub($this->precision());
            if ($prev->isBusinessTime()) {
                $businessHoursToSub -= $decrement;
            }
        }

        return $prev;
    }

    /**
     * Get the difference between this time and another in whole business days.
     *
     * @see DiffInBusinessDaysTest
     *
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    public function diffInBusinessDays(
        DateTimeInterface $time = null,
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
        DateTimeInterface $time = null,
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
     * @throws InvalidArgumentException
     *
     * @return float
     */
    public function diffInPartialBusinessDays(
        DateTimeInterface $time = null,
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
        DateTimeInterface $time = null,
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
        DateTimeInterface $time = null,
        bool $absolute = true
    ): Interval {
        return Interval::seconds(
            $this->diffInBusinessTime($time, $absolute)
            * $this->precision()->inSeconds()
        );
    }

    /**
     * @return string
     */
    public function businessName(): string
    {
        return $this->canonicalNarrator()->narrate($this);
    }

    /**
     * Get the first business time after the start of this day.
     *
     * @return BusinessTime
     */
    public function startOfBusinessDay(): self
    {
        // Iterate from the beginning of the day until we hit business time.
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
        // Iterate back from the end of the day until we hit business time.
        $end = $this->copy()->endOfDay();
        while (!$end->isBusinessTime()) {
            $end = $end->sub($this->precision());
        }

        // Add a second because we've iterated from 23:59:59.
        return $end->addSecond();
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
    public function floorToPrecision(DateInterval $precision = null): self
    {
        $precisionSeconds = Interval::instance($precision ?: $this->precision())
                                    ->inSeconds();

        // Allow for sub-hour timezone differences:
        // Add the timezone remainder, floor, then take the remainder back off.
        $timezoneOffset = $this->getTimezone()->getOffset(new Carbon());
        $timezoneRemainder = $timezoneOffset % 3600;

        return $this->copy()->setTimestamp(
            (int) (
                floor(
                    (
                        $this->timestamp + $timezoneRemainder
                    ) / $precisionSeconds
                ) * $precisionSeconds
            ) - $timezoneRemainder
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
    public function roundToPrecision(DateInterval $precision = null): self
    {
        $precisionSeconds = Interval::instance($precision ?: $this->precision())
                                    ->inSeconds();

        // Allow for sub-hour timezone differences:
        // Add the timezone remainder, round, then take the remainder back off.
        $timezoneOffset = $this->getTimezone()->getOffset(new Carbon());
        $timezoneRemainder = $timezoneOffset % 3600;

        return $this->copy()->setTimestamp(
            (int) (
                round(
                    (
                        $this->timestamp + $timezoneRemainder
                    ) / $precisionSeconds
                ) * $precisionSeconds
            ) - $timezoneRemainder
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
    public function ceilToPrecision(DateInterval $precision = null): self
    {
        $seconds = Interval::instance($precision ?: $this->precision())
                           ->inSeconds();

        // Allow for sub-hour timezone differences:
        // Add the timezone remainder, ceil, then take the remainder back off.
        $timezoneOffset = $this->getTimezone()->getOffset(new Carbon());
        $timezoneRemainder = $timezoneOffset % 3600;

        return $this->copy()->setTimestamp(
            (int) (
                ceil(
                    (
                        $this->timestamp + $timezoneRemainder
                    ) / $seconds
                ) * $seconds
            ) - $timezoneRemainder
        );
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return Interval
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
     * @throws InvalidArgumentException
     *
     * @return BusinessTime
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
     * @throws InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function determineLengthOfBusinessDay(
        DateTime $typicalDay = null
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
     * Get the business time constraints.
     *
     * @return All|BusinessTimeConstraint[]
     */
    public function businessTimeConstraints(): All
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
     * @throws InvalidArgumentException
     *
     * @return BusinessTime
     */
    public function setPrecision(DateInterval $precision): self
    {
        $this->precision = Interval::instance($precision);

        return $this;
    }

    /**
     * Guarantee copy is instance of BusinessTime to deter analyser complaints.
     *
     * @return BusinessTime
     */
    public function copy(): self
    {
        $copy = clone $this;
        \assert($copy instanceof self);

        return $copy;
    }

    /**
     * @param DateTimeInterface $dti
     *
     * @return BusinessTime
     */
    public static function fromDti(DateTimeInterface $dti): self
    {
        return (new static())
            ->setTimezone($dti->getTimezone())
            ->setTimestamp($dti->getTimestamp());
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
        DateTimeInterface $time = null,
        bool $absolute = true
    ): int {
        // We're taking a basic approach with some variables and a loop here as
        // it turns out to be ~25% faster than using Carbon::diffFiltered().

        /** @var BusinessTime $start */
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
        /** @var BusinessTime $next */
        /** @scrutinizer ignore-call */
        $next = $start->copy();
        while ($next < $end) {
            /* @scrutinizer ignore-call */
            if ($next->isBusinessTime()) {
                $diff++;
            }
            $next = $next->add($this->precision());
        }

        return $diff * $sign;
    }

    /**
     * Get a narrator for the first business time constraint that determines
     * whether this time is business time or not.
     *
     * @return BusinessTimeNarrator
     */
    private function canonicalNarrator(): BusinessTimeNarrator
    {
        /* @var BusinessTimeConstraint $constraint */
        if (!$this->isBusinessTime()) {
            foreach ($this->businessTimeConstraints() as $constraint) {
                if (!$constraint->isBusinessTime($this)) {
                    return new DefaultNarrator($constraint);
                }
            }
        }
        if ($this->isBusinessTime()) {
            foreach ($this->businessTimeConstraints() as $constraint) {
                if ($constraint->isBusinessTime($this)) {
                    return new DefaultNarrator($constraint);
                }
            }
        }

        return new AnyTime();
    }
}

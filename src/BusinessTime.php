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
     * Get the date time after adding one business day.
     *
     * @return BusinessTime
     * @throws \InvalidArgumentException
     */
    public function addBusinessDay(): self
    {
        $next = $this->copy();
        while ($this->diffInBusinessDays($next) < 1) {
            $next = $next->add($this->precision());
        }

        return $next;
    }

    /**
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
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return int
     */
    public function diffInBusinessMinutes(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): int {
        return (int) $this->diffInPartialBusinessMinutes($time, $absolute);
    }

    /**
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
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return float
     */
    public function diffInPartialBusinessMinutes(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): float {
        return $this->diffInBusinessTime($time, $absolute)
            * $this->precision()->inMinutes();
    }

    /**
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return Interval
     */
    public function diffBusiness(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): Interval {
        return Interval::minutes(
            $this->diffInBusinessTime($time, $absolute)
            * $this->precision()->inMinutes()
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
        $this->businessTimeConstraints = new All(
            ...$constraints
        );

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
            ...$this->businessTimeConstraints()->andAlso($constraints)
        );

        return $this;
    }

    /**
     * @return All
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
     * @param DateTimeInterface $time
     * @param bool              $absolute
     *
     * @return int
     */
    private function diffInBusinessTime(
        ?DateTimeInterface $time = null,
        bool $absolute = true
    ): int {
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

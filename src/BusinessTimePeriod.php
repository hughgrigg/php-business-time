<?php

namespace BusinessTime;

use BusinessTime\Constraint\BusinessTimeConstraint;

/**
 * A period of business time that can be divided into business and non-business
 * days, or precise business and non-business sub-periods.
 */
class BusinessTimePeriod
{
    /** @var BusinessTime */
    private $start;

    /** @var BusinessTime */
    private $end;

    /** @var BusinessTimeConstraint[] */
    private $businessTimeConstraints;

    /**
     * BusinessTimePeriod constructor.
     *
     * @param BusinessTime $start
     * @param BusinessTime $end
     */
    public function __construct(BusinessTime $start, BusinessTime $end)
    {
        $this->start = $start;
        $this->end = $end;
        $this->businessTimeConstraints = $start->businessTimeConstraints();
    }

    /**
     * Alternative constructor that takes start and end as date-time strings.
     *
     * @param string $start
     * @param string $end
     *
     * @return BusinessTimePeriod
     */
    public static function fromStrings(string $start, string $end): self
    {
        return new static(new BusinessTime($start), new BusinessTime($end));
    }

    /**
     * Get an array of business time instances, one for each business day in
     * this period.
     *
     * @see BusinessDaysTest
     *
     * @return BusinessTime[]
     */
    public function businessDays(): array
    {
        return array_values(
            array_filter(
                $this->allDays(),
                function (BusinessTime $day): bool {
                    return $day->isBusinessDay();
                }
            )
        );
    }

    /**
     * Get an array of business time instances, one for each non-business day in
     * this period.
     *
     * @see BusinessDaysTest
     *
     * @return BusinessTime[]
     */
    public function nonBusinessDays(): array
    {
        return array_values(
            array_filter(
                $this->allDays(),
                function (BusinessTime $day): bool {
                    return !$day->isBusinessDay();
                }
            )
        );
    }

    /**
     * Get an array of business time instances, one for each day in this period.
     *
     * @see BusinessDaysTest
     *
     * @return BusinessTime[]
     */
    public function allDays(): array
    {
        $days = [];
        $next = $this->start()->copy()->startOfDay();
        $days[] = $next;
        while ($next->lt($this->end())) {
            $next = $next->copy()->addDay();
            $days[] = $next;
        }

        return $days;
    }

    /**
     * Get the precise sub-periods of this period that are business time.
     *
     * E.g. a time period from Monday 06:00 to Monday 20:00 could be:
     *     Monday 09:00 - Monday 17:00
     *
     * @see SubPeriodsTest
     *
     * @return self[]
     */
    public function businessPeriods(): array
    {
        return array_values(
            array_filter(
                $this->subPeriods(),
                function (self $subPeriod): bool {
                    return $subPeriod->isBusinessTime();
                }
            )
        );
    }

    /**
     * Get the precise sub-periods of this period that are not business time.
     *
     * E.g. a time period from Monday 06:00 to Monday 20:00 could be:
     *     Monday 06:00 - Monday 09:00
     *     Monday 17:00 - Monday 20:00
     *
     * @see SubPeriodsTest
     *
     * @return self[]
     */
    public function nonBusinessPeriods(): array
    {
        return array_values(
            array_filter(
                $this->subPeriods(),
                function (self $subPeriod): bool {
                    return !$subPeriod->isBusinessTime();
                }
            )
        );
    }

    /**
     * Get this business time period separated into consecutive business and
     * non-business times.
     *
     * E.g. a time period from Monday 06:00 to Monday 20:00 could be:
     *     Monday 06:00 - Monday 09:00
     *     Monday 09:00 - Monday 17:00
     *     Monday 17:00 - Monday 20:00
     *
     * @see SubPeriodsTest
     *
     * @return self[]
     */
    public function subPeriods(): array
    {
        $subPeriods = [];
        $next = $this->start()->copy();

        // Iterate from the start of the time period until we reach the end.
        while ($next->lt($this->end())) {
            $subStart = $next->copy();

            // When we're in a business sub-period, keep going until we hit a
            // non-business sub-period or the end of the whole period.
            while ($next->isBusinessTime() && $next->lt($this->end())) {
                $next = $next->add($next->precision());
            }
            // If we advanced by doing that, record it as a sub-period.
            if ($next->gt($subStart)) {
                $subPeriods[] = new self($subStart->copy(), $next->copy());
                $subStart = $next->copy();
            }

            // When we're in a non-business sub-period, keep going until we hit
            // a business sub-period or the end of the whole period.
            while (!$next->isBusinessTime() && $next->lt($this->end())) {
                $next = $next->add($next->precision());
            }
            // If we advanced by doing that, record it as a sub-period.
            if ($next->gt($subStart)) {
                $subPeriods[] = new self($subStart->copy(), $next->copy());
            }
        }

        // TODO: this should group the potential names and then offer the
        // most frequently occurring name in that period.
        // e.g. Friday 17:00 to Monday 09:00 should be called "the weekend".

        return $subPeriods;
    }

    /**
     * @return bool
     */
    public function isBusinessTime(): bool
    {
        return $this->start()->isBusinessTime();
    }

    /**
     * @return BusinessTime
     */
    public function start(): BusinessTime
    {
        return $this->start->copy();
    }

    /**
     * @return BusinessTime
     */
    public function end(): BusinessTime
    {
        return $this->end->copy();
    }

    /**
     * @param BusinessTimeConstraint ...$constraints
     *
     * @return BusinessTimePeriod
     */
    public function setBusinessTimeConstraints(
        BusinessTimeConstraint ...$constraints
    ): self {
        $this->businessTimeConstraints = $constraints;

        return $this;
    }
}

<?php

namespace BusinessTime;

use BusinessTime\Constraint\BusinessTimeConstraint;
use DateInterval;
use DatePeriod;

/**
 * @property-read BusinessTime $start
 * @property-read BusinessTime $end
 */
class BusinessTimePeriod extends DatePeriod
{
    /** @var BusinessTimeConstraint[] */
    private $businessTimeConstraints;

    /**
     * @param BusinessTime $start
     * @param DateInterval $interval
     * @param BusinessTime $end
     * @param int          $options
     */
    public function __construct(
        BusinessTime $start,
        DateInterval $interval,
        BusinessTime $end,
        int $options = 0
    ) {
        parent::__construct($start, $interval, $end, $options);
    }

    /**
     * Alternative constructor that takes start and end as date-time strings.
     *
     * @param string $start
     * @param string $end
     *
     * @return BusinessTimePeriod
     */
    public static function fromTo(string $start, string $end): self
    {
        return new static(
            new BusinessTime($start),
            Interval::hour(),
            new BusinessTime($end)
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
     * @return self[]
     */
    public function subPeriods(): array
    {
        $subPeriods = [];
        $next = $this->getStartDate()->copy();

        // Iterate from the start of the time period until we reach the end.
        while ($next->lt($this->getEndDate())) {
            $subStart = $next->copy();

            // When we're in a business sub-period, keep going until we hit a
            // non-business sub-period or the end of the whole period.
            while ($next->isBusinessTime() && $next->lt($this->getEndDate())) {
                $next = $next->add($next->precision());
            }
            // If we advanced by doing that, record it as a sub-period.
            if ($next->gt($subStart)) {
                $subPeriods[] = new self($subStart, $next->precision(), $next);
                $subStart = $next->copy();
            }

            // When we're in a non-business sub-period, keep going until we hit
            // a business sub-period or the end of the whole period.
            while (!$next->isBusinessTime() && $next->lt($this->getEndDate())) {
                $next = $next->add($next->precision());
            }
            // If we advanced by doing that, record it as a sub-period.
            if ($next->gt($subStart)) {
                $subPeriods[] = new self($subStart, $next->precision(), $next);
            }
        }

        // TODO: this should group the potential names and then offer the
        // most frequently occurring name in that period.
        // e.g. Friday 17:00 to Monday 09:00 should be called "the weekend".

        return $subPeriods;
    }

    /**
     * @return self[]
     */
    public function businessPeriods(): array
    {
        return array_filter(
            $this->subPeriods(),
            function (self $subPeriod): bool {
                return $subPeriod->isBusinessTime();
            }
        );
    }

    /**
     * @return self[]
     */
    public function nonBusinessPeriods(): array
    {
        return array_filter(
            $this->subPeriods(),
            function (self $subPeriod): bool {
                return !$subPeriod->isBusinessTime();
            }
        );
    }

    /**
     * @return bool
     */
    public function isBusinessTime(): bool
    {
        return $this->getStartDate()->isBusinessTime();
    }

    /**
     * @return string
     */
    public function businessName(): string
    {
        return $this->getStartDate()->businessName();
    }

    /**
     * @return Interval
     */
    public function businessDiff(): Interval
    {
        return $this->getStartDate()->diffBusiness($this->getEndDate());
    }

    /**
     * @return BusinessTime
     */
    public function getStartDate(): BusinessTime
    {
        $start = parent::getStartDate();
        if (!($start instanceof BusinessTime)) {
            $start = (new BusinessTime())
                ->setTimestamp($start->getTimestamp())
                ->setBusinessTimeConstraints($this->businessTimeConstraints);
        }

        return $start;
    }

    /**
     * @return BusinessTime
     */
    public function getEndDate(): BusinessTime
    {
        $end = parent::getEndDate();
        if (!($end instanceof BusinessTime)) {
            $end = (new BusinessTime())
                ->setTimestamp($end->getTimestamp())
                ->setBusinessTimeConstraints($this->businessTimeConstraints);
        }

        return $end;
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

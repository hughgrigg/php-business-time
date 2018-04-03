<?php

namespace BusinessTime;

use BusinessTime\Constraint\BusinessTimeConstraint;
use DateInterval;
use DatePeriod;

/**
 * @package BusinessTime
 *
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
     * TODO
     *
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
        // TODO: this should group the potential names and then offer the
        // most frequently occuring name in that period.
        // e.g. Friday 17:00 to Monday 09:00 should be called "the weekend".

        $subPeriods = [];
        $next = $this->getStartDate()->copy();
        while ($next->lt($this->getEndDate())) {
            $subStart = $next->copy();
            while ($next->isBusinessTime()) {
                $next = $next->add($next->precision());
            }
            if ($next->gt($subStart)) {
                $subPeriods[] = new self($subStart, $next->precision(), $next);
            }
            while (!$next->isBusinessTime()) {
                $next = $next->add($next->precision());
            }
            if ($next->gt($subStart)) {
                $subPeriods[] = new self($subStart, $next->precision(), $next);
            }
        }

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
        $this->start = parent::getStartDate();
        if (!($this->start instanceof BusinessTime)) {
            $this->start = (new BusinessTime())
                ->setTimestamp($this->start->getTimestamp())
                ->setBusinessTimeConstraints($this->businessTimeConstraints);
        }

        return $this->start;
    }

    /**
     * @return BusinessTime
     */
    public function getEndDate(): BusinessTime
    {
        $this->end = parent::getEndDate();
        if (!($this->end instanceof BusinessTime)) {
            $this->end = (new BusinessTime())
                ->setTimestamp($this->end->getTimestamp())
                ->setBusinessTimeConstraints($this->businessTimeConstraints);
        }

        return $this->end;
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

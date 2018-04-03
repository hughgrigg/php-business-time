<?php

namespace BusinessTime\Tests\Unit\Constraint\Composite;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\AnyTime;
use BusinessTime\Constraint\BetweenDaysOfWeek;
use BusinessTime\Constraint\BetweenHoursOfDay;
use BusinessTime\Constraint\DaysOfWeek;
use BusinessTime\Constraint\HoursOfDay;
use BusinessTime\Constraint\NoTime;
use BusinessTime\Constraint\WeekDays;
use BusinessTime\Constraint\Weekends;
use Generator;

/**
 * Test providers for composite constraint tests.
 */
trait CompositeConstraintProviders
{
    /**
     * Get a business time instance that fits with the constraint providers.
     *
     * @return BusinessTime
     */
    public function wednesdayOnePm(): BusinessTime
    {
        return new BusinessTime('Wednesday 2018-05-23 13:00');
    }

    /**
     * Provides sets of constraints that all match Wednesday 2018-05-23 13:00
     * as business time.
     *
     * @return array[]
     */
    public function allMatchProvider(): array
    {
        return [
            [
                [new AnyTime(), new WeekDays(), new BetweenHoursOfDay(12, 14)],
            ],
            [
                [
                    new AnyTime(),
                    new DaysOfWeek('Wednesday'),
                    new BetweenHoursOfDay(12, 14),
                ],
            ],
            [
                [
                    new AnyTime(),
                    new BetweenDaysOfWeek('Tuesday', 'Thursday'),
                    new HoursOfDay(8, 13, 20),
                ],
            ],
        ];
    }

    /**
     * Provides sets of constraints none of which match Wednesday 2018-05-23
     * 13:00 as business time.
     *
     * @return array[]
     */
    public function noneMatchProvider(): array
    {
        return [
            [
                [new NoTime(), new Weekends(), new BetweenHoursOfDay(3, 7)],
            ],
            [
                [
                    new NoTime(),
                    new DaysOfWeek('Tuesday', 'Friday'),
                    new BetweenHoursOfDay(15, 20),
                ],
            ],
            [
                [
                    new NoTime(),
                    new BetweenDaysOfWeek('Thursday', 'Saturday'),
                    new HoursOfDay(8, 14, 20),
                ],
            ],
        ];
    }

    /**
     * Provides sets of constraints some of which match Wednesday 2018-05-23
     * 13:00 as business time and some of which don't.
     *
     * @return Generator|array[]
     */
    public function someMatchProvider(): Generator
    {
        foreach ($this->allMatchProvider() as $i => $allMatch) {
            yield [
                array_merge(
                    $allMatch[0],
                    $this->noneMatchProvider()[$i][0]
                ),
            ];
        }
    }
}

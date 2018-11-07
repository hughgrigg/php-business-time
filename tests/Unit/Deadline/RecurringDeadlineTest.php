<?php

namespace BusinessTime\Tests\Unit\Deadline;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\DaysOfWeek;
use BusinessTime\Constraint\HoursOfDay;
use BusinessTime\Constraint\WeekDays;
use BusinessTime\Constraint\Weekends;
use BusinessTime\Deadline\RecurringDeadline;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Unit test the RecurringDeadline class.
 */
class RecurringDeadlineTest extends TestCase
{
    /**
     * Test finding the next occurrence of a deadline for weekdays at 11am.
     *
     * @dataProvider nextDeadlineWeekDays11amProvider
     *
     * @param string $time
     * @param string $expectedNextDeadline
     */
    public function testNextDeadlineWeekDays11am(
        string $time,
        string $expectedNextDeadline
    ) {
        // Given we have a recurring deadline for weekdays at 11am;
        $deadline = new RecurringDeadline(new WeekDays(), new HoursOfDay(11));

        // And a specific time;
        $businessTime = new BusinessTime($time);

        // When we get the next occurrence of the deadline;
        $nextOccurrence = $deadline->nextOccurrenceFrom($businessTime);

        // Then it should be as expected.
        self::assertSame(
            $expectedNextDeadline,
            $nextOccurrence->format('l H:i')
        );
    }

    /**
     * Provides times and the expected next occurrence of a deadline for
     * weekdays at 11am.
     *
     * @return array[]
     */
    public function nextDeadlineWeekDays11amProvider(): array
    {
        return [
            // From Monday
            ['Monday 00:00', 'Monday 11:00'],
            ['Monday 09:00', 'Monday 11:00'],
            ['Monday 09:30', 'Monday 11:00'],
            ['Monday 10:59', 'Monday 11:00'],
            ['Monday 11:00', 'Tuesday 11:00'],
            ['Monday 11:01', 'Tuesday 11:00'],
            ['Monday 17:00', 'Tuesday 11:00'],
            ['Monday 23:59', 'Tuesday 11:00'],
            // From Friday
            ['Friday 00:00', 'Friday 11:00'],
            ['Friday 09:00', 'Friday 11:00'],
            ['Friday 09:30', 'Friday 11:00'],
            ['Friday 10:59', 'Friday 11:00'],
            ['Friday 11:00', 'Monday 11:00'],
            ['Friday 11:01', 'Monday 11:00'],
            ['Friday 17:00', 'Monday 11:00'],
            ['Friday 23:59', 'Monday 11:00'],
            // From Saturday
            ['Saturday 00:00', 'Monday 11:00'],
            ['Saturday 09:00', 'Monday 11:00'],
            ['Saturday 09:30', 'Monday 11:00'],
            ['Saturday 10:59', 'Monday 11:00'],
            ['Saturday 11:00', 'Monday 11:00'],
            ['Saturday 11:01', 'Monday 11:00'],
            ['Saturday 17:00', 'Monday 11:00'],
            ['Saturday 23:59', 'Monday 11:00'],
            // From Sunday
            ['Sunday 00:00', 'Monday 11:00'],
            ['Sunday 09:00', 'Monday 11:00'],
            ['Sunday 09:30', 'Monday 11:00'],
            ['Sunday 10:59', 'Monday 11:00'],
            ['Sunday 11:00', 'Monday 11:00'],
            ['Sunday 11:01', 'Monday 11:00'],
            ['Sunday 17:00', 'Monday 11:00'],
            ['Sunday 23:59', 'Monday 11:00'],
        ];
    }

    /**
     * Test finding the previous occurrence of a deadline for weekdays at 11am.
     *
     * @dataProvider previousDeadlineWeekDays11amProvider
     *
     * @param string $time
     * @param string $expectedNextDeadline
     */
    public function testPreviousDeadlineWeekDays11am(
        string $time,
        string $expectedNextDeadline
    ) {
        // Given we have a recurring deadline for weekdays at 11am;
        $deadline = new RecurringDeadline(new WeekDays(), new HoursOfDay(11));

        // And a specific time;
        $businessTime = new BusinessTime($time);

        // When we get the previous occurrence of the deadline;
        $nextOccurrence = $deadline->previousOccurrenceFrom($businessTime);

        // Then it should be as expected.
        self::assertSame(
            $expectedNextDeadline,
            $nextOccurrence->format('l H:i')
        );
    }

    /**
     * Provides times and the expected previous occurrence of a deadline for
     * weekdays at 11am.
     *
     * @return array[]
     */
    public function previousDeadlineWeekDays11amProvider(): array
    {
        return [
            // From Friday
            ['Friday 00:00', 'Thursday 11:00'],
            ['Friday 09:00', 'Thursday 11:00'],
            ['Friday 09:30', 'Thursday 11:00'],
            ['Friday 10:59', 'Thursday 11:00'],
            ['Friday 11:00', 'Thursday 11:00'],
            ['Friday 11:01', 'Thursday 11:00'],
            ['Friday 17:00', 'Friday 11:00'],
            ['Friday 23:59', 'Friday 11:00'],
            // From Monday
            ['Monday 00:00', 'Friday 11:00'],
            ['Monday 09:00', 'Friday 11:00'],
            ['Monday 09:30', 'Friday 11:00'],
            ['Monday 10:59', 'Friday 11:00'],
            ['Monday 11:00', 'Friday 11:00'],
            ['Monday 11:01', 'Friday 11:00'],
            ['Monday 17:00', 'Monday 11:00'],
            ['Monday 23:59', 'Monday 11:00'],
            // From Saturday
            ['Saturday 00:00', 'Friday 11:00'],
            ['Saturday 09:00', 'Friday 11:00'],
            ['Saturday 09:30', 'Friday 11:00'],
            ['Saturday 10:59', 'Friday 11:00'],
            ['Saturday 11:00', 'Friday 11:00'],
            ['Saturday 11:01', 'Friday 11:00'],
            ['Saturday 17:00', 'Friday 11:00'],
            ['Saturday 23:59', 'Friday 11:00'],
            // From Sunday
            ['Sunday 00:00', 'Friday 11:00'],
            ['Sunday 09:00', 'Friday 11:00'],
            ['Sunday 09:30', 'Friday 11:00'],
            ['Sunday 10:59', 'Friday 11:00'],
            ['Sunday 11:00', 'Friday 11:00'],
            ['Sunday 11:01', 'Friday 11:00'],
            ['Sunday 17:00', 'Friday 11:00'],
            ['Sunday 23:59', 'Friday 11:00'],
        ];
    }

    /**
     * Should be able to see if a deadline has passed today.
     *
     * @dataProvider hasPassedTodayProvider
     *
     * @param string                   $now
     * @param BusinessTimeConstraint[] $constraints
     * @param bool                     $shouldHavePassed
     */
    public function testHasPassedToday(
        string $now,
        array $constraints,
        bool $shouldHavePassed
    ) {
        // Given we have a recurring deadline;
        $deadline = new RecurringDeadline(...$constraints);

        // When we check if it has passed today;
        Carbon::setTestNow($now);

        // Then the result should be as expected.
        self::assertSame($shouldHavePassed, $deadline->hasPassedToday());
    }

    /**
     * Provides set times for 'now' with constraints for a recurring deadline,
     * and whether that deadline has passed 'today' accordingly.
     *
     * @return array[]
     */
    public function hasPassedTodayProvider(): array
    {
        return [
            // Now
            // Constraints
            // Passed?
            [
                '2018-05-23 13:00',
                [new WeekDays(), new HoursOfDay(12)],
                true,
            ],
            [
                '2018-05-23 13:00',
                [new WeekDays(), new HoursOfDay(14)],
                false,
            ],
            [
                '2018-05-23 13:00',
                [new Weekends(), new HoursOfDay(12)],
                false,
            ],
            [
                '2018-05-23 15:00',
                [new DaysOfWeek('Wednesday'), new HoursOfDay(12)],
                true,
            ],
            [
                '2018-05-27 13:00',
                [new WeekDays(), new HoursOfDay(12)],
                false,
            ],
        ];
    }

    /**
     * Should be able to see if a deadline has passed between two times.
     *
     * @dataProvider hasPassedBetweenProvider
     *
     * @param string $start
     * @param string $end
     * @param bool   $shouldHavePassed
     */
    public function testHasPassedBetween(
        string $start,
        string $end,
        bool $shouldHavePassed
    ) {
        // Given we have a recurring deadline;
        $deadline = new RecurringDeadline(new WeekDays(), new HoursOfDay(11));

        // When we check if it has passed between two times;
        // Then the result should be as expected.
        self::assertSame(
            $shouldHavePassed,
            $deadline->hasPassedBetween(
                new BusinessTime($start),
                new BusinessTime($end)
            )
        );
    }

    /**
     * Provides set times for 'now' with start and end times, and whether a
     * deadline for weekdays 11am should have passed accordingly.
     *
     * @return array[]
     */
    public function hasPassedBetweenProvider(): array
    {
        return [
            // Start
            // End
            // Passed?
            [
                '2018-05-23 09:00',
                '2018-05-23 17:00',
                true,
            ],
            [
                '2018-05-23 09:00',
                '2018-05-23 10:59',
                false,
            ],
            [
                '2018-05-23 09:00',
                '2018-05-23 11:00',
                true,
            ],
            [
                '2018-05-23 09:00',
                '2018-05-23 11:01',
                true,
            ],
            [
                '2018-05-23 09:00',
                '2018-05-23 11:59',
                true,
            ],
            [
                '2018-05-22 17:00',
                '2018-05-23 12:00',
                true,
            ],
            [
                '2018-05-25 11:00',
                '2018-05-26 12:00',
                true,
            ],
            [
                '2018-05-25 12:00',
                '2018-05-26 12:00',
                false,
            ],
        ];
    }
}

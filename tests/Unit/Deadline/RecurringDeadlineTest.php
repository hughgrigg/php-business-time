<?php

namespace BusinessTime\Tests\Unit\Deadline;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\HoursOfDay;
use BusinessTime\Constraint\WeekDays;
use BusinessTime\Deadline\RecurringDeadline;
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
    ): void {
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
    ): void {
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
}

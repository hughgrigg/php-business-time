<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenTimesOfDay;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BetweenTimesOfDay business time constraint.
 */
class BetweenTimesOfDayTest extends TestCase
{
    /**
     * @dataProvider betweenTimesOfDayProvider
     *
     * @param string $minTimeOfDay
     * @param string $maxTimeOfDay
     * @param string $time
     * @param bool   $shouldMatch
     */
    public function testBetweenTimesOfDay(
        string $minTimeOfDay,
        string $maxTimeOfDay,
        string $time,
        bool $shouldMatch
    ): void {
        // Given we have a constraint for between times of the day;
        $constraint = new BetweenTimesOfDay($minTimeOfDay, $maxTimeOfDay);

        // And a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides min and max times of the day with a time, and whether it should
     * be matched as business time accordingly.
     *
     * @return array[]
     */
    public function betweenTimesOfDayProvider(): array
    {
        return [
            // Min     Max      Time    Match?
            ['09:00', '17:00', '12:00', true],
            ['09:00', '17:00', '08:59', false],
            ['09:00', '17:00', '09:00', true],
            ['09:00', '17:00', '12:00', true],
            ['09:00', '17:00', '15:30', true],
            ['09:00', '17:00', '17:00', false],
            ['09:00', '17:00', '17:01', false],
            ['09:30', '17:30', '09:29', false],
            ['09:30', '17:30', '09:30', true],
            ['09:30', '17:30', '17:29', true],
            ['09:30', '17:30', '17:30', false],
            ['00:00', '23:59', 'now  ', true],
        ];
    }

    /**
     * Should be able to get the minute index of a time of day.
     *
     * @dataProvider minuteOfDayProvider
     *
     * @param string $time
     * @param int    $expectedMinute
     */
    public function testMinuteOfDay(string $time, int $expectedMinute): void
    {
        // When we get the minute of the day;
        $minuteOfTheDay = (new BetweenTimesOfDay())->minuteOfDay(
            new DateTime($time)
        );

        // Then it should be as expected.
        self::assertSame(
            $expectedMinute,
            $minuteOfTheDay,
            sprintf(
                'Minute of the day for %s should be %d; got %d.',
                $time,
                $expectedMinute,
                $minuteOfTheDay
            )
        );
    }

    /**
     * Provides times of day with the expected minute index of the day.
     *
     * @return array[]
     */
    public function minuteOfDayProvider(): array
    {
        return [
            ['00:00', 0],
            ['00:01', 1],
            ['00:30', 30],
            ['01:00', 60],
            ['01:30', 90],
            ['06:00', 360],
            ['08:00', 480],
            ['09:00', 540],
            ['9am', 540],
            ['12:00', 720],
            ['noon', 720],
            ['17:00', 1020],
            ['5pm', 1020],
            ['23:59', 1439],
        ];
    }
}

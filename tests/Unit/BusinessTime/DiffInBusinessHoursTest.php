<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\Interval;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::diffInBusinessHours() and
 * BusinessTime::diffInPartialBusinessHours() method.
 */
class DiffInBusinessHoursTest extends TestCase
{
    /**
     * Test the diffInBusinessHours method with default behaviour.
     *
     * @dataProvider diffInBusinessHoursDefaultProvider
     *
     * @param string $time
     * @param string $otherTime
     * @param int    $expectedDiff
     */
    public function testDiffInBusinessHoursDefault(
        string $time,
        string $otherTime,
        int $expectedDiff
    ): void {
        // Given we have a business time for a particular time;
        $businessTime = new BusinessTime($time);

        // When we get the diff in business hours from another time;
        $diff = $businessTime->diffInBusinessHours(
            new BusinessTime($otherTime)
        );

        // Then we should get the expected diff.
        self::assertSame($expectedDiff, $diff);
    }

    /**
     * Return pairs of hours with their expected diff in business hours with the
     * default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00 and the precision is 1 hour.
     *
     * @return array[]
     */
    public function diffInBusinessHoursDefaultProvider(): array
    {
        // TODO: use short date format.
        return [
            // Going forward in time.
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 09:00', 0],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 09:00', 0],
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 10:00', 1],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 10:00', 1],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 11:00', 2],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 12:00', 3],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 13:00', 4],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 14:00', 5],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 15:00', 6],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 16:00', 7],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 17:00', 8],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 18:00', 8],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 23:00', 8],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 00:00', 8],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 03:00', 8],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 09:00', 8],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 10:00', 9],
            ['Friday 18th May 2018 08:00', 'Saturday 19th May 2018 13:00', 8],
            ['Friday 18th May 2018 09:00', 'Saturday 19th May 2018 10:00', 8],
            ['Friday 18th May 2018 03:00', 'Sunday 20th May 2018 19:00', 8],
            ['Friday 18th May 2018 07:00', 'Monday 20th May 2018 08:00', 8],
            ['Friday 18th May 2018 06:00', 'Monday 20th May 2018 09:00', 8],
            ['Friday 18th May 2018 05:00', 'Monday 20th May 2018 10:00', 9],
            ['Friday 18th May 2018 09:00', 'Monday 20th May 2018 10:00', 9],
            ['Friday 18th May 2018 10:00', 'Monday 20th May 2018 10:00', 8],
            ['Friday 18th May 2018 09:00', 'Monday 20th May 2018 11:00', 10],
            ['Friday 18th May 2018 09:00', 'Monday 20th May 2018 17:00', 16],
            // Going back in time.
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 00:00', 0],
            ['Monday 14th May 2018 10:00', 'Monday 14th May 2018 00:00', 1],
            ['Monday 14th May 2018 10:00', 'Monday 14th May 2018 09:00', 1],
            ['Monday 14th May 2018 11:00', 'Monday 14th May 2018 09:00', 2],
            ['Monday 14th May 2018 12:00', 'Monday 14th May 2018 09:00', 3],
            ['Monday 14th May 2018 13:00', 'Monday 14th May 2018 09:00', 4],
            ['Monday 14th May 2018 14:00', 'Monday 14th May 2018 09:00', 5],
            ['Monday 14th May 2018 15:00', 'Monday 14th May 2018 09:00', 6],
            ['Monday 14th May 2018 16:00', 'Monday 14th May 2018 09:00', 7],
            ['Monday 14th May 2018 17:00', 'Monday 14th May 2018 09:00', 8],
            ['Monday 14th May 2018 18:00', 'Monday 14th May 2018 09:00', 8],
            ['Monday 14th May 2018 23:00', 'Monday 14th May 2018 09:00', 8],
            ['Tuesday 15th May 2018 00:00', 'Monday 14th May 2018 09:00', 8],
            ['Tuesday 15th May 2018 03:00', 'Monday 14th May 2018 09:00', 8],
            ['Tuesday 15th May 2018 09:00', 'Monday 14th May 2018 09:00', 8],
            ['Tuesday 15th May 2018 10:00', 'Monday 14th May 2018 09:00', 9],
            ['Saturday 19th May 2018 13:00', 'Friday 18th May 2018 08:00', 8],
            ['Saturday 19th May 2018 10:00', 'Friday 18th May 2018 09:00', 8],
            ['Sunday 20th May 2018 19:00', 'Friday 18th May 2018 03:00', 8],
            ['Monday 20th May 2018 08:00', 'Friday 18th May 2018 07:00', 8],
            ['Monday 20th May 2018 09:00', 'Friday 18th May 2018 06:00', 8],
            ['Monday 20th May 2018 10:00', 'Friday 18th May 2018 05:00', 9],
            ['Monday 20th May 2018 10:00', 'Friday 18th May 2018 09:00', 9],
            ['Monday 20th May 2018 10:00', 'Friday 18th May 2018 10:00', 8],
            ['Monday 20th May 2018 11:00', 'Friday 18th May 2018 09:00', 10],
            ['Monday 20th May 2018 17:00', 'Friday 18th May 2018 09:00', 16],
        ];
    }

    /**
     * Test the diffInPartialBusinessHours method with default behaviour.
     *
     * @dataProvider diffInPartialBusinessHoursDefaultProvider
     *
     * @param string $time
     * @param string $otherTime
     * @param float  $expectedDiff
     */
    public function testDiffInPartialBusinessHoursDefault(
        string $time,
        string $otherTime,
        float $expectedDiff
    ): void {
        // Given we have a business time for a particular time;
        $businessTime = new BusinessTime($time);

        // And we have 15-minute precision;
        $businessTime->setPrecision(Interval::minutes(15));

        // When we get the diff in partial business hours from another time;
        $diff = $businessTime->diffInPartialBusinessHours(
            new BusinessTime($otherTime)
        );

        // Then we should get the expected diff.
        self::assertSame($expectedDiff, $diff);
    }

    /**
     * Return pairs of hours with their expected diff in partial business hours
     * with the default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00, but with a precision of 15 minutes.
     *
     * @return array[]
     */
    public function diffInPartialBusinessHoursDefaultProvider(): array
    {
        // TODO: use short date format.
        return [
            // Going forward in time.
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 09:00', 0],
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 09:15', 0.25],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 09:30', 0.5],
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 10:45', 1.75],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 10:30', 1.5],
            ['Monday 14th May 2018 09:30', 'Monday 14th May 2018 11:45', 2.25],
            ['Monday 14th May 2018 09:15', 'Monday 14th May 2018 12:45', 3.5],
            ['Monday 14th May 2018 09:30', 'Monday 14th May 2018 13:15', 3.75],
            ['Monday 14th May 2018 09:45', 'Monday 14th May 2018 14:30', 4.75],
            ['Monday 14th May 2018 09:30', 'Monday 14th May 2018 15:30', 6.0],
            ['Monday 14th May 2018 09:45', 'Monday 14th May 2018 16:45', 7.0],
            ['Monday 14th May 2018 09:15', 'Monday 14th May 2018 17:45', 7.75],
            ['Monday 14th May 2018 09:30', 'Monday 14th May 2018 18:15', 7.5],
            ['Monday 14th May 2018 09:45', 'Monday 14th May 2018 23:30', 7.25],
            ['Monday 14th May 2018 09:30', 'Tuesday 15th May 2018 00:15', 7.5],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 03:45', 8.0],
            ['Monday 14th May 2018 09:45', 'Tuesday 15th May 2018 09:30', 7.75],
            ['Monday 14th May 2018 09:30', 'Tuesday 15th May 2018 10:15', 8.75],
            ['Friday 18th May 2018 08:30', 'Saturday 19th May 2018 13:45', 8.0],
            [
                'Friday 18th May 2018 09:45',
                'Saturday 19th May 2018 10:30',
                7.25,
            ],
            ['Friday 18th May 2018 03:00', 'Sunday 20th May 2018 19:15', 8.0],
            ['Friday 18th May 2018 07:30', 'Monday 20th May 2018 08:30', 8.0],
            ['Friday 18th May 2018 06:45', 'Monday 20th May 2018 09:45', 8.75],
            ['Friday 18th May 2018 05:30', 'Monday 20th May 2018 10:30', 9.5],
            ['Friday 18th May 2018 09:00', 'Monday 20th May 2018 10:00', 9.0],
            ['Friday 18th May 2018 10:15', 'Monday 20th May 2018 10:15', 8.0],
            ['Friday 18th May 2018 09:45', 'Monday 20th May 2018 11:30', 9.75],
            ['Friday 18th May 2018 09:30', 'Monday 20th May 2018 17:45', 15.5],
            // Going back in time.
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 00:00', 0.0],
            ['Monday 14th May 2018 09:45', 'Monday 14th May 2018 00:15', 0.75],
            ['Monday 14th May 2018 10:45', 'Monday 14th May 2018 00:15', 1.75],
            ['Monday 14th May 2018 10:30', 'Monday 14th May 2018 09:45', 0.75],
            ['Monday 14th May 2018 11:00', 'Monday 14th May 2018 09:30', 1.5],
            ['Monday 14th May 2018 12:15', 'Monday 14th May 2018 09:15', 3.0],
            ['Monday 14th May 2018 13:45', 'Monday 14th May 2018 09:30', 4.25],
            ['Monday 14th May 2018 14:30', 'Monday 14th May 2018 09:45', 4.75],
            ['Monday 14th May 2018 15:15', 'Monday 14th May 2018 09:00', 6.25],
            ['Monday 14th May 2018 16:45', 'Monday 14th May 2018 09:30', 7.25],
            ['Monday 14th May 2018 17:00', 'Monday 14th May 2018 09:15', 7.75],
            ['Monday 14th May 2018 18:30', 'Monday 14th May 2018 09:45', 7.25],
            ['Monday 14th May 2018 23:15', 'Monday 14th May 2018 09:30', 7.5],
            ['Tuesday 15th May 2018 00:30', 'Monday 14th May 2018 09:45', 7.25],
            ['Tuesday 15th May 2018 03:45', 'Monday 14th May 2018 09:30', 7.5],
            ['Tuesday 15th May 2018 09:30', 'Monday 14th May 2018 09:45', 7.75],
            ['Tuesday 15th May 2018 10:15', 'Monday 14th May 2018 09:30', 8.75],
            [
                'Saturday 19th May 2018 13:45',
                'Friday 18th May 2018 09:15',
                7.75,
            ],
            [
                'Saturday 19th May 2018 10:30',
                'Friday 18th May 2018 09:45',
                7.25,
            ],
            ['Sunday 20th May 2018 19:15', 'Friday 18th May 2018 03:15', 8.0],
            ['Monday 20th May 2018 08:15', 'Friday 18th May 2018 07:30', 8.0],
            ['Monday 20th May 2018 09:45', 'Friday 18th May 2018 06:00', 8.75],
            ['Monday 20th May 2018 10:00', 'Friday 18th May 2018 05:45', 9.0],
            ['Monday 20th May 2018 10:45', 'Friday 18th May 2018 09:15', 9.5],
            ['Monday 20th May 2018 10:45', 'Friday 18th May 2018 10:30', 8.25],
            ['Monday 20th May 2018 11:15', 'Friday 18th May 2018 09:45', 9.5],
            ['Monday 20th May 2018 17:00', 'Friday 18th May 2018 09:15', 15.75],
        ];
    }
}

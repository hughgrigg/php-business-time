<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\Interval;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::subBusinessHour() and BusinessTime::subBusinessHours()
 * methods.
 */
class SubBusinessHoursTest extends TestCase
{
    /**
     * Test the subBusinessHour method with default behaviour.
     *
     * @dataProvider subBusinessHourProvider
     *
     * @param string $time
     * @param string $expectedNewTime
     */
    public function testSubBusinessHourDefault(
        string $time,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // When we subtract a business hour from it;
        $previousBusinessHour = $businessTime->subBusinessHour();

        // Then we should get the expected new time.
        self::assertSame(
            $expectedNewTime,
            $previousBusinessHour->format('l Y-m-d H:i')
        );
    }

    /**
     * Provides times with the expected new time after subtracting one business
     * hour with default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00 and the precision is 1 hour.
     *
     * @return array[]
     */
    public function subBusinessHourProvider(): array
    {
        return [
            ['Wednesday 2018-05-23 10:00', 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 10:15', 'Wednesday 2018-05-23 09:15'],
            ['Wednesday 2018-05-23 10:30', 'Wednesday 2018-05-23 09:30'],
            ['Wednesday 2018-05-23 10:45', 'Wednesday 2018-05-23 09:45'],
            ['Wednesday 2018-05-23 11:00', 'Wednesday 2018-05-23 10:00'],
            ['Wednesday 2018-05-23 12:00', 'Wednesday 2018-05-23 11:00'],
            ['Wednesday 2018-05-23 13:00', 'Wednesday 2018-05-23 12:00'],
            ['Wednesday 2018-05-23 14:00', 'Wednesday 2018-05-23 13:00'],
            ['Wednesday 2018-05-23 15:00', 'Wednesday 2018-05-23 14:00'],
            ['Wednesday 2018-05-23 16:00', 'Wednesday 2018-05-23 15:00'],
            ['Wednesday 2018-05-23 17:00', 'Wednesday 2018-05-23 16:00'],
            ['Wednesday 2018-05-23 18:00', 'Wednesday 2018-05-23 16:00'],
            ['Wednesday 2018-05-23 23:00', 'Wednesday 2018-05-23 16:00'],
            ['Monday 2018-05-21 00:00', 'Friday 2018-05-18 16:00'],
            ['Monday 2018-05-21 09:00', 'Friday 2018-05-18 16:00'],
            ['Monday 2018-05-21 09:30', 'Friday 2018-05-18 16:30'],
            ['Sunday 2018-05-20 02:00', 'Friday 2018-05-18 16:00'],
            ['Saturday 2018-05-19 03:00', 'Friday 2018-05-18 16:00'],
        ];
    }

    /**
     * Test subtracting various amounts of whole and partial business hours with
     * the default behaviour.
     *
     * @dataProvider subBusinessHoursProvider
     *
     * @param string $time
     * @param float  $businessHoursToSub
     * @param string $expectedNewTime
     */
    public function testSubBusinessHoursDefault(
        string $time,
        float $businessHoursToSub,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // And we have 15-minute precision;
        $businessTime->setPrecision(Interval::minutes(15));

        // When we subtract an amount of business hours from it;
        $subtracted = $businessTime->subBusinessHours($businessHoursToSub);

        // Then we should get the expected new time.
        self::assertSame($expectedNewTime, $subtracted->format('l Y-m-d H:i'));
    }

    /**
     * Provides times and the expected new times after subtracting various
     * amounts of business hours with the default behaviour.
     *
     * @return array[]
     */
    public function subBusinessHoursProvider(): array
    {
        return [
            // Subtracting less than a day.
            ['Wednesday 2018-05-23 17:00', 0, 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 17:00', 0.25, 'Wednesday 2018-05-23 16:45'],
            ['Wednesday 2018-05-23 17:00', 0.5, 'Wednesday 2018-05-23 16:30'],
            ['Wednesday 2018-05-23 17:00', 0.75, 'Wednesday 2018-05-23 16:15'],
            ['Wednesday 2018-05-23 17:00', 1, 'Wednesday 2018-05-23 16:00'],
            ['Wednesday 2018-05-23 17:00', 1.25, 'Wednesday 2018-05-23 15:45'],
            ['Wednesday 2018-05-23 17:00', 1.5, 'Wednesday 2018-05-23 15:30'],
            ['Wednesday 2018-05-23 17:00', 1.75, 'Wednesday 2018-05-23 15:15'],
            ['Wednesday 2018-05-23 17:00', 2, 'Wednesday 2018-05-23 15:00'],
            // Subtracting a whole business day or more.
            ['Wednesday 2018-05-23 17:00', 8, 'Wednesday 2018-05-23 09:00'],
            ['Wednesday 2018-05-23 17:00', 8.25, 'Tuesday 2018-05-22 16:45'],
            ['Wednesday 2018-05-23 17:00', 8.5, 'Tuesday 2018-05-22 16:30'],
            ['Wednesday 2018-05-23 17:00', 8.75, 'Tuesday 2018-05-22 16:15'],
            ['Wednesday 2018-05-23 17:00', 9, 'Tuesday 2018-05-22 16:00'],
            ['Wednesday 2018-05-23 17:00', 16, 'Tuesday 2018-05-22 09:00'],
            ['Wednesday 2018-05-23 17:00', 23, 'Monday 2018-05-21 10:00'],
            ['Wednesday 2018-05-23 17:00', 24, 'Monday 2018-05-21 09:00'],
            // Negative values.
            ['Wednesday 2018-05-23 17:00', -0, 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 17:00', -0.25, 'Thursday 2018-05-24 09:15'],
            ['Wednesday 2018-05-23 17:00', -0.5, 'Thursday 2018-05-24 09:30'],
            ['Wednesday 2018-05-23 17:00', -0.75, 'Thursday 2018-05-24 09:45'],
            ['Wednesday 2018-05-23 17:00', -1, 'Thursday 2018-05-24 10:00'],
            ['Wednesday 2018-05-23 17:00', -1.25, 'Thursday 2018-05-24 10:15'],
            ['Wednesday 2018-05-23 17:00', -1.5, 'Thursday 2018-05-24 10:30'],
            ['Wednesday 2018-05-23 17:00', -1.75, 'Thursday 2018-05-24 10:45'],
            ['Wednesday 2018-05-23 17:00', -2, 'Thursday 2018-05-24 11:00'],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::subBusinessDay() and BusinessTime::subBusinessDays()
 * methods.
 */
class SubBusinessDaysTest extends TestCase
{
    /**
     * Test the subBusinessDay method with default behaviour.
     *
     * @dataProvider subBusinessDayProvider
     *
     * @param string $time
     * @param string $expectedNewTime
     */
    public function testSubBusinessDayDefault(
        string $time,
        string $expectedNewTime
    ) {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // When we sub a business day to it;
        $nextBusinessDay = $businessTime->subBusinessDay();

        // Then we should get the expected new time.
        self::assertSame(
            $expectedNewTime,
            $nextBusinessDay->format('l Y-m-d H:i')
        );
    }

    /**
     * Provides times with the expected new time after subtracting one business
     * day with default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00 and the precision is 1 day.
     *
     * @return array[]
     */
    public function subBusinessDayProvider(): array
    {
        return [
            ['Wednesday 2018-05-23 00:00', 'Tuesday 2018-05-22 00:00'],
            ['Wednesday 2018-05-23 08:30', 'Tuesday 2018-05-22 08:30'],
            ['Wednesday 2018-05-23 09:00', 'Tuesday 2018-05-22 09:00'],
            ['Wednesday 2018-05-23 09:30', 'Tuesday 2018-05-22 09:30'],
            ['Wednesday 2018-05-23 13:00', 'Tuesday 2018-05-22 13:00'],
            ['Wednesday 2018-05-23 17:00', 'Tuesday 2018-05-22 17:00'],
            ['Wednesday 2018-05-23 17:30', 'Tuesday 2018-05-22 17:30'],
            ['Wednesday 2018-05-23 18:00', 'Tuesday 2018-05-22 18:00'],
            ['Wednesday 2018-05-23 18:30', 'Tuesday 2018-05-22 18:30'],
            ['Monday 2018-05-21 13:00', 'Friday 2018-05-18 13:00'],
            ['Sunday 2018-05-20 11:00', 'Friday 2018-05-18 09:00'],
            ['Saturday 2018-05-19 10:00', 'Friday 2018-05-18 09:00'],
        ];
    }

    /**
     * Test subtracting various amounts of whole and partial business days with
     * the default behaviour.
     *
     * @dataProvider subBusinessDaysProvider
     *
     * @param string $time
     * @param float  $businessDaysToSub
     * @param string $expectedNewTime
     */
    public function testSubBusinessDaysDefault(
        string $time,
        float $businessDaysToSub,
        string $expectedNewTime
    ) {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // When we subtract an amount of business days from it;
        $subtracted = $businessTime->subBusinessDays($businessDaysToSub);

        // Then we should get the expected new time.
        self::assertSame($expectedNewTime, $subtracted->format('l Y-m-d H:i'));
    }

    /**
     * @return array[]
     */
    public function subBusinessDaysProvider(): array
    {
        return [
            // From Friday evening.
            ['Friday 2018-05-18 18:00', 0, 'Friday 2018-05-18 18:00'],
            ['Friday 2018-05-18 17:00', 0, 'Friday 2018-05-18 17:00'],
            ['Friday 2018-05-18 17:00', 0.25, 'Friday 2018-05-18 15:00'],
            ['Friday 2018-05-18 17:00', 0.5, 'Friday 2018-05-18 13:00'],
            ['Friday 2018-05-18 17:00', 0.75, 'Friday 2018-05-18 11:00'],
            ['Friday 2018-05-18 17:00', 1, 'Thursday 2018-05-17 17:00'],
            ['Friday 2018-05-18 17:00', 1.25, 'Thursday 2018-05-17 15:00'],
            ['Friday 2018-05-18 17:00', 1.5, 'Thursday 2018-05-17 13:00'],
            ['Friday 2018-05-18 17:00', 1.75, 'Thursday 2018-05-17 11:00'],
            ['Friday 2018-05-18 17:00', 2, 'Wednesday 2018-05-16 17:00'],
            // From Monday morning.
            ['Monday 2018-05-21 08:00', 0, 'Monday 2018-05-21 08:00'],
            ['Monday 2018-05-21 09:00', 0, 'Monday 2018-05-21 09:00'],
            ['Monday 2018-05-21 09:00', 0.25, 'Friday 2018-05-18 15:00'],
            ['Monday 2018-05-21 09:00', 0.5, 'Friday 2018-05-18 13:00'],
            ['Monday 2018-05-21 09:00', 0.75, 'Friday 2018-05-18 11:00'],
            ['Monday 2018-05-21 09:00', 1, 'Friday 2018-05-18 09:00'],
            ['Monday 2018-05-21 09:00', 1.25, 'Thursday 2018-05-17 15:00'],
            ['Monday 2018-05-21 09:00', 1.5, 'Thursday 2018-05-17 13:00'],
            ['Monday 2018-05-21 09:00', 1.75, 'Thursday 2018-05-17 11:00'],
            ['Monday 2018-05-21 09:00', 2, 'Thursday 2018-05-17 09:00'],
            // Negative values.
            ['Wednesday 2018-05-23 13:00', -0, 'Wednesday 2018-05-23 13:00'],
            ['Wednesday 2018-05-23 13:00', -0.25, 'Wednesday 2018-05-23 15:00'],
            ['Wednesday 2018-05-23 13:00', -0.5, 'Wednesday 2018-05-23 17:00'],
            ['Wednesday 2018-05-23 13:00', -0.75, 'Thursday 2018-05-24 11:00'],
            ['Wednesday 2018-05-23 13:00', -1, 'Thursday 2018-05-24 13:00'],
            ['Wednesday 2018-05-23 13:00', -1.25, 'Thursday 2018-05-24 15:00'],
            ['Wednesday 2018-05-23 13:00', -1.5, 'Thursday 2018-05-24 17:00'],
            ['Wednesday 2018-05-23 13:00', -1.75, 'Friday 2018-05-25 11:00'],
            ['Wednesday 2018-05-23 13:00', -2, 'Friday 2018-05-25 13:00'],
        ];
    }
}

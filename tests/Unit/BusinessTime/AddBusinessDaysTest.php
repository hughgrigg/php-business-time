<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::addBusinessDay() and BusinessTime::addBusinessDays()
 * methods.
 */
class AddBusinessDaysTest extends TestCase
{
    /**
     * Test the addBusinessDay method with default behaviour.
     *
     * @dataProvider addBusinessDayProvider
     *
     * @param string $time
     * @param string $expectedNewTime
     */
    public function testAddBusinessDayDefault(
        string $time,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // When we add a business day to it;
        $nextBusinessDay = $businessTime->addBusinessDay();

        // Then we should get the expected new time.
        self::assertSame(
            $expectedNewTime,
            $nextBusinessDay->format('l jS F Y H:i')
        );
    }

    /**
     * Provides times with the expected new time after adding one business day
     * with default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00 and the precision is 1 hour.
     *
     * @return array[]
     */
    public function addBusinessDayProvider(): array
    {
        return [
            ['Monday 14th May 2018 00:00', 'Tuesday 15th May 2018 00:00'],
            ['Monday 14th May 2018 08:00', 'Tuesday 15th May 2018 08:00'],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 09:00'],
            ['Monday 14th May 2018 10:00', 'Tuesday 15th May 2018 10:00'],
            ['Monday 14th May 2018 17:00', 'Tuesday 15th May 2018 17:00'],
            ['Tuesday 15th May 2018 11:00', 'Wednesday 16th May 2018 11:00'],
            ['Wednesday 16th May 2018 12:00', 'Thursday 17th May 2018 12:00'],
            ['Thursday 17th May 2018 13:00', 'Friday 18th May 2018 13:00'],
            ['Friday 18th May 2018 14:00', 'Monday 21st May 2018 14:00'],
            ['Saturday 19th May 2018 15:00', 'Monday 21st May 2018 17:00'],
            ['Sunday 20th May 2018 16:00', 'Monday 21st May 2018 17:00'],
        ];
    }

    /**
     * Test adding various amounts of whole and partial business days with the
     * default behaviour.
     *
     * @dataProvider addBusinessDaysProvider
     *
     * @param string $time
     * @param float  $businessDaysToAdd
     * @param string $expectedNewTime
     */
    public function testAddBusinessDaysDefault(
        string $time,
        float $businessDaysToAdd,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // When we add an amount of business days to it;
        $added = $businessTime->addBusinessDays($businessDaysToAdd);

        // Then we should get the expected new time.
        self::assertSame($expectedNewTime, $added->format('l jS F Y H:i'));
    }

    /**
     * Provides times with the expected new time after adding an amount of
     * whole or partial business days with default behaviour, i.e. that one
     * business day is 8-hours from 09:00 to 17:00 Monday to Friday and the
     * precision is 1 hour.
     *
     * @return array[]
     */
    public function addBusinessDaysProvider(): array
    {
        return [
            ['Monday 14th May 2018 00:00', 0, 'Monday 14th May 2018 00:00'],
            ['Monday 14th May 2018 09:00', 0, 'Monday 14th May 2018 09:00'],
            ['Monday 14th May 2018 09:00', 0.25, 'Monday 14th May 2018 11:00'],
            ['Monday 14th May 2018 09:00', 0.5, 'Monday 14th May 2018 13:00'],
            ['Monday 14th May 2018 09:00', 0.75, 'Monday 14th May 2018 15:00'],
            ['Monday 14th May 2018 09:00', 1, 'Tuesday 15th May 2018 09:00'],
            ['Monday 14th May 2018 00:00', 1, 'Tuesday 15th May 2018 00:00'],
            ['Monday 14th May 2018 09:00', 1.25, 'Tuesday 15th May 2018 11:00'],
            ['Monday 14th May 2018 09:00', 1.5, 'Tuesday 15th May 2018 13:00'],
            ['Monday 14th May 2018 09:00', 1.75, 'Tuesday 15th May 2018 15:00'],
            ['Monday 14th May 2018 09:00', 2, 'Wednesday 16th May 2018 09:00'],
        ];
    }
}

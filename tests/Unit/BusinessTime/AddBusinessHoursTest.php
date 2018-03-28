<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenHoursOfDay;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Interval;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::addBusinessHour() and BusinessTime::addBusinessHours()
 * methods.
 */
class AddBusinessHoursTest extends TestCase
{
    /**
     * Test the addBusinessHour method with default behaviour.
     *
     * @dataProvider addBusinessHourProvider
     *
     * @param string $time
     * @param string $expectedNewTime
     */
    public function testAddBusinessHourDefault(
        string $time,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // When we add a business hour to it;
        $nextBusinessHour = $businessTime->addBusinessHour();

        // Then we should get the expected new time.
        self::assertSame(
            $expectedNewTime,
            $nextBusinessHour->format('l jS F Y H:i')
        );
    }

    /**
     * Provides times with the expected new time after adding one business hour
     * with default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00 and the precision is 1 hour.
     *
     * @return array[]
     */
    public function addBusinessHourProvider(): array
    {
        return [
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 10:00'],
            ['Monday 14th May 2018 08:00', 'Monday 14th May 2018 10:00'],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 10:00'],
            ['Monday 14th May 2018 09:15', 'Monday 14th May 2018 10:15'],
            ['Monday 14th May 2018 09:30', 'Monday 14th May 2018 10:30'],
            ['Monday 14th May 2018 09:45', 'Monday 14th May 2018 10:45'],
            ['Monday 14th May 2018 10:00', 'Monday 14th May 2018 11:00'],
            ['Monday 14th May 2018 11:00', 'Monday 14th May 2018 12:00'],
            ['Monday 14th May 2018 12:00', 'Monday 14th May 2018 13:00'],
            ['Monday 14th May 2018 13:00', 'Monday 14th May 2018 14:00'],
            ['Monday 14th May 2018 14:00', 'Monday 14th May 2018 15:00'],
            ['Monday 14th May 2018 15:00', 'Monday 14th May 2018 16:00'],
            ['Monday 14th May 2018 16:00', 'Monday 14th May 2018 17:00'],
            ['Monday 14th May 2018 17:00', 'Tuesday 15th May 2018 10:00'],
            ['Monday 14th May 2018 18:00', 'Tuesday 15th May 2018 10:00'],
            ['Monday 14th May 2018 23:00', 'Tuesday 15th May 2018 10:00'],
            ['Friday 18th May 2018 16:00', 'Friday 18th May 2018 17:00'],
            ['Friday 18th May 2018 17:00', 'Monday 21st May 2018 10:00'],
            ['Saturday 19th May 2018 08:00', 'Monday 21st May 2018 10:00'],
            ['Saturday 19th May 2018 18:00', 'Monday 21st May 2018 10:00'],
            ['Sunday 19th May 2018 07:00', 'Monday 21st May 2018 10:00'],
            ['Sunday 19th May 2018 19:00', 'Monday 21st May 2018 10:00'],
        ];
    }

    /**
     * Test adding various amounts of whole and partial business hours with the
     * default behaviour.
     *
     * @dataProvider addBusinessHoursProvider
     *
     * @param string $time
     * @param float  $businessHoursToAdd
     * @param string $expectedNewTime
     */
    public function testAddBusinessHoursDefault(
        string $time,
        float $businessHoursToAdd,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // And we have 15-minute precision;
        $businessTime->setPrecision(Interval::minutes(15));

        // When we add an amount of business hours to it;
        $added = $businessTime->addBusinessHours($businessHoursToAdd);

        // Then we should get the expected new time.
        self::assertSame($expectedNewTime, $added->format('l jS F Y H:i'));
    }

    /**
     * Provides times with the expected new time after adding an amount of
     * whole or partial business hours with default behaviour, i.e. that one
     * business day is 8-hours from 09:00 to 17:00 Monday to Friday, but with
     * 15-minute precision.
     *
     * @return array[]
     */
    public function addBusinessHoursProvider(): array
    {
        return [
            // Adding less than a day.
            ['Monday 14th May 2018 09:00', 0, 'Monday 14th May 2018 09:00'],
            ['Monday 14th May 2018 09:00', 0.25, 'Monday 14th May 2018 09:15'],
            ['Monday 14th May 2018 09:00', 0.5, 'Monday 14th May 2018 09:30'],
            ['Monday 14th May 2018 09:00', 0.75, 'Monday 14th May 2018 09:45'],
            ['Monday 14th May 2018 09:00', 1, 'Monday 14th May 2018 10:00'],
            ['Monday 14th May 2018 09:00', 1.25, 'Monday 14th May 2018 10:15'],
            ['Monday 14th May 2018 09:00', 1.5, 'Monday 14th May 2018 10:30'],
            ['Monday 14th May 2018 09:00', 1.75, 'Monday 14th May 2018 10:45'],
            ['Monday 14th May 2018 09:00', 2, 'Monday 14th May 2018 11:00'],
            ['Monday 14th May 2018 09:00', 7.75, 'Monday 14th May 2018 16:45'],
            // Adding a whole business day or more.
            ['Monday 14th May 2018 09:00', 8, 'Monday 14th May 2018 17:00'],
            ['Monday 14th May 2018 09:00', 8.25, 'Tuesday 15th May 2018 09:15'],
            ['Monday 14th May 2018 09:00', 8.5, 'Tuesday 15th May 2018 09:30'],
            ['Monday 14th May 2018 09:00', 8.75, 'Tuesday 15th May 2018 09:45'],
            ['Monday 14th May 2018 09:00', 9, 'Tuesday 15th May 2018 10:00'],
            ['Monday 14th May 2018 09:00', 16, 'Tuesday 15th May 2018 17:00'],
            ['Monday 14th May 2018 09:00', 23, 'Wednesday 16th May 2018 16:00'],
            ['Monday 14th May 2018 09:00', 24, 'Wednesday 16th May 2018 17:00'],
            // Negative values.
            ['Monday 14th May 2018 09:00', -0, 'Monday 14th May 2018 09:00'],
            ['Monday 14th May 2018 09:00', -0.25, 'Friday 11th May 2018 16:45'],
            ['Monday 14th May 2018 09:00', -0.5, 'Friday 11th May 2018 16:30'],
            ['Monday 14th May 2018 09:00', -0.75, 'Friday 11th May 2018 16:15'],
            ['Monday 14th May 2018 09:00', -1, 'Friday 11th May 2018 16:00'],
            ['Monday 14th May 2018 09:00', -1.25, 'Friday 11th May 2018 15:45'],
            ['Monday 14th May 2018 09:00', -1.5, 'Friday 11th May 2018 15:30'],
            ['Monday 14th May 2018 09:00', -1.75, 'Friday 11th May 2018 15:15'],
            ['Monday 14th May 2018 09:00', -2, 'Friday 11th May 2018 15:00'],
        ];
    }

    /**
     * Test adding various amounts of whole and partial business hours with
     * various business time constraints.
     *
     * @dataProvider addBusinessHoursConstraintProvider
     *
     * @param string                 $time
     * @param BusinessTimeConstraint $constraint
     * @param float                  $businessHoursToAdd
     * @param string                 $expectedNewTime
     */
    public function testAddBusinessHoursConstraint(
        string $time,
        BusinessTimeConstraint $constraint,
        float $businessHoursToAdd,
        string $expectedNewTime
    ): void {
        // Given we have a business time for a specific time;
        $businessTime = new BusinessTime($time);

        // And we set specific business time constraints;
        $businessTime->setBusinessTimeConstraints($constraint);

        // And we have 15-minute precision;
        $businessTime->setPrecision(Interval::minutes(15));

        // When we add an amount of business hours to it;
        $added = $businessTime->addBusinessHours($businessHoursToAdd);

        // Then we should get the expected new time.
        self::assertSame($expectedNewTime, $added->format('l jS F Y H:i'));
    }

    /**
     * Provides times with the expected new time after adding an amount of
     * whole or partial business hours with specific business time constraints.
     *
     * @return array[]
     */
    public function addBusinessHoursConstraintProvider(): array
    {
        return [
            [
                'Monday 14th May 2018 09:00',
                // Exclude lunch time.
                (new BetweenHoursOfDay(9, 17))->except(
                    new BetweenHoursOfDay(13, 14)
                ),
                4,
                'Monday 14th May 2018 13:00',
            ],
            [
                'Monday 14th May 2018 09:00',
                // Exclude lunch time.
                (new BetweenHoursOfDay(9, 17))->except(
                    new BetweenHoursOfDay(13, 14)
                ),
                5, // Would be 14:00, but we're not counting lunch time.
                'Monday 14th May 2018 15:00',
            ],
            [
                'Monday 14th May 2018 09:00',
                // Exclude lunch time.
                (new BetweenHoursOfDay(9, 17))->except(
                    new BetweenHoursOfDay(13, 14)
                ),
                7 + 5, // 1 full day, plus 5 hours.
                'Tuesday 15th May 2018 15:00',
            ],
        ];
    }
}

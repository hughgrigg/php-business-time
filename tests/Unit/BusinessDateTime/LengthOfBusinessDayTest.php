<?php

namespace BusinessTime\Tests\Unit\BusinessDateTime;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\All;
use BusinessTime\Constraint\BetweenHoursOfDay;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\WeekDays;
use BusinessTime\Interval;
use PHPUnit\Framework\TestCase;

/**
 * Test functionality around the length of business days.
 */
class LengthOfBusinessDayTest extends TestCase
{
    /**
     * Test that the length of a business day is 8 hours by default.
     */
    public function testLengthOfBusinessDayDefault(): void
    {
        // Given we have a business time with the default behaviour;
        $time = new BusinessTime();

        // Then the length of a business day should be 8 hours.
        self::assertSame(
            '8 hours',
            $time->lengthOfBusinessDay()->forHumans()
        );
        self::assertEquals(8, $time->lengthOfBusinessDay()->inHours());
        self::assertEquals(480, $time->lengthOfBusinessDay()->inMinutes());
    }

    /**
     * Should be able to set the length of a business day.
     *
     * @dataProvider lengthOfBusinessDayProvider
     *
     * @param Interval $length
     * @param string   $expectedDescription
     */
    public function testSetLengthOfBusinessDay(
        Interval $length,
        string $expectedDescription
    ): void {
        // Given we have a business time;
        $time = new BusinessTime();

        // And we set the length of the business day;
        $time->setLengthOfBusinessDay($length);

        // Then the length of the business day should be adjusted.
        self::assertSame(
            $expectedDescription,
            $time->lengthOfBusinessDay()->forHumans()
        );
    }

    /**
     * Provides lengths of business day and their expected descriptions.
     *
     * @return array[]
     */
    public function lengthOfBusinessDayProvider(): array
    {
        return [
            [Interval::hour(), '1 hour'],
            [Interval::hours(2), '2 hours'],
            [Interval::hours(6), '6 hours'],
            [Interval::hours(18), '18 hours'],
            [Interval::minutes((8 * 60) + 30), '8 hours 30 minutes'],
            [Interval::minutes((6 * 60) + 59), '6 hours 59 minutes'],
        ];
    }

    /**
     * Should be able to determine the length of a business day based on
     * constraints.
     *
     * @dataProvider determineLengthOfBusinessDayProvider
     *
     * @param BusinessTimeConstraint $constraint
     * @param string                 $expectedDescription
     */
    public function testDetermineLengthOfBusinessDay(
        BusinessTimeConstraint $constraint,
        string $expectedDescription
    ): void {
        // Given we have a business time with certain constraints;
        $time = new BusinessTime();
        $time->setBusinessTimeConstraints($constraint);

        // When we determine the length of a business day;
        $time->determineLengthOfBusinessDay();

        // Then the determined length of a business day should be as expected.
        self::assertSame(
            $expectedDescription,
            $time->lengthOfBusinessDay()->forHumans()
        );
    }

    /**
     * Provides business time constraints and the expected resulting length of
     * a business day based on them.
     *
     * @return array[]
     */
    public function determineLengthOfBusinessDayProvider(): array
    {
        return [
            [new BetweenHoursOfDay(9, 17), '8 hours'],
            [new BetweenHoursOfDay(9, 12), '3 hours'],
            [new BetweenHoursOfDay(8, 18), '10 hours'],
            [new BetweenHoursOfDay(0, 23), '23 hours'],
            [new BetweenHoursOfDay(0, 24), '1 day'],
            [new WeekDays(), '1 day'],
            [
                new All(
                    new WeekDays(),
                    new BetweenHoursOfDay(9, 17)
                ),
                '8 hours',
            ],
            [
                // Exclude lunch time.
                (new BetweenHoursOfDay(9, 17))->except(
                    new BetweenHoursOfDay(13, 14)
                ),
                '7 hours',
            ],
            [
                // Multiple periods.
                (new BetweenHoursOfDay(8, 10))
                    ->orAlternatively(new BetweenHoursOfDay(12, 14))
                    ->orAlternatively(new BetweenHoursOfDay(16, 18)),
                '6 hours',
            ],
        ];
    }
}

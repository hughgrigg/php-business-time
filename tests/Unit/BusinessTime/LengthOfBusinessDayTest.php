<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenHoursOfDay;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\Composite\All;
use BusinessTime\Constraint\WeekDays;
use BusinessTime\Interval;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test functionality around the length of business days.
 */
class LengthOfBusinessDayTest extends TestCase
{
    /**
     * Test that the length of a business day is 8 hours by default.
     */
    public function testLengthOfBusinessDayDefault()
    {
        // Given we have a business time with the default behaviour;
        $time = new BusinessTime();

        // Then the length of a business day should be 8 hours.
        self::assertEquals(
            8,
            $time->lengthOfBusinessDay()->inHours(),
            'Should be 8 hours',
            2
        );
        self::assertEquals(
            480,
            $time->lengthOfBusinessDay()->inMinutes(),
            'Should be 480 minutes',
            2
        );
    }

    /**
     * Should be able to set the length of a business day.
     *
     * @dataProvider lengthOfBusinessDayProvider
     *
     * @param Interval $length
     * @param int      $expectedSeconds
     */
    public function testSetLengthOfBusinessDay(
        Interval $length,
        int $expectedSeconds
    ) {
        // Given we have a business time;
        $time = new BusinessTime();

        // And we set the length of the business day;
        $time->setLengthOfBusinessDay($length);

        // Then the length of the business day should be adjusted.
        self::assertEquals(
            $expectedSeconds,
            $time->lengthOfBusinessDay()->inSeconds(),
            "Should be ${expectedSeconds}",
            2
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
            [Interval::hour(), 60 * 60],
            [Interval::hours(2), 2 * 60 * 60],
            [Interval::hours(6), 6 * 60 * 60],
            [Interval::hours(18), 18 * 60 * 60],
            [Interval::minutes((8 * 60) + 30), 8.5 * 60 * 60],
            [Interval::minutes((6 * 60) + 59), (6 * 60 * 60) + (59 * 60)],
        ];
    }

    /**
     * Should be able to determine the length of a business day based on
     * constraints.
     *
     * @dataProvider determineLengthOfBusinessDayProvider
     *
     * @param BusinessTimeConstraint $constraint
     * @param int                    $expectedSeconds
     */
    public function testDetermineLengthOfBusinessDay(
        BusinessTimeConstraint $constraint,
        int $expectedSeconds
    ) {
        // Given we have a business time with certain constraints;
        $time = new BusinessTime();
        $time->setBusinessTimeConstraints($constraint);

        // When we determine the length of a business day;
        $time->determineLengthOfBusinessDay();

        // Then the determined length of a business day should be as expected.
        self::assertEquals(
            $expectedSeconds,
            $time->lengthOfBusinessDay()->inSeconds(),
            "Should be {$expectedSeconds}",
            2
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
            [new BetweenHoursOfDay(9, 17), 8 * 60 * 60],
            [new BetweenHoursOfDay(9, 12), 3 * 60 * 60],
            [new BetweenHoursOfDay(8, 18), 10 * 60 * 60],
            [new BetweenHoursOfDay(0, 23), 23 * 60 * 60],
            [new BetweenHoursOfDay(0, 24), 24 * 60 * 60],
            [new WeekDays(), 24 * 60 * 60],
            [
                new All(
                    new WeekDays(),
                    new BetweenHoursOfDay(9, 17)
                ),
                8 * 60 * 60,
            ],
            [
                // Exclude lunch time.
                (new BetweenHoursOfDay(9, 17))->except(
                    new BetweenHoursOfDay(13, 14)
                ),
                7 * 60 * 60,
            ],
            [
                // Multiple periods.
                (new BetweenHoursOfDay(8, 10))
                    ->orAlternatively(new BetweenHoursOfDay(12, 14))
                    ->orAlternatively(new BetweenHoursOfDay(16, 18)),
                6 * 60 * 60,
            ],
        ];
    }

    /**
     * Should not be able to set a business day to zero length.
     */
    public function testCantSetZeroLengthBusinessDay()
    {
        // We should get an error;
        $this->expectException(InvalidArgumentException::class);

        // When we try to set a zero-length business day.
        $time = new BusinessTime();
        $time->setLengthOfBusinessDay(Interval::hours(0));
    }

    /**
     * Should not be able to set the length of a business day over one day.
     */
    public function testCantSetBusinessDayLengthOverOneDay()
    {
        // We should get an error;
        $this->expectException(InvalidArgumentException::class);

        // When we try to set a business day length over one day.
        $time = new BusinessTime();
        $time->setLengthOfBusinessDay(Interval::hours(25));
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BetweenDaysOfWeek;
use PHPUnit\Framework\TestCase;

/**
 * Test the BetweenDaysOfWeek business time constraint.
 */
class BetweenDaysOfWeekTest extends TestCase
{
    /**
     * @dataProvider betweenDaysOfWeekProvider
     *
     * @param string $time
     * @param string $minDayOfWeek
     * @param string $maxDayOfWeek
     * @param bool   $shouldMatch
     */
    public function testBetweenDaysOfWeek(
        string $time,
        string $minDayOfWeek,
        string $maxDayOfWeek,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint matching times between days of the week;
        $constraint = new BetweenDaysOfWeek($minDayOfWeek, $maxDayOfWeek);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with min and max days of the week, and whether that should
     * match the given time.
     *
     * @return array[]
     */
    public function betweenDaysOfWeekProvider(): array
    {
        return [
            // Day of week    Min       Max      Match?
            ['Wednesday', 'Tuesday', 'Wednesday', true],
            ['Wednesday', 'Tuesday', 'Thursday', true],
            ['Wednesday', 'Thursday', 'Tuesday', true],
            ['Thursday', 'Tuesday', 'Thursday', true],
            ['Wednesday', 'Wednesday', 'Wednesday', true],
            ['Wednesday', 'Monday', 'Friday', true],
            ['Saturday', 'Monday', 'Friday', false],
            ['Saturday', 'Sunday', 'Saturday', true],
            ['Saturday', 'Monday', 'Monday', true],
            ['Saturday', 'Monday', 'Sunday', true],
            ['Thursday', 'Friday', 'Saturday', false],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\Weekends;
use PHPUnit\Framework\TestCase;

/**
 * Test the Weekends business time constraint.
 */
class WeekendsTest extends TestCase
{
    /**
     * @dataProvider weekendsProvider
     *
     * @param string $time
     * @param bool   $shouldMatch
     */
    public function testWeekends(string $time, bool $shouldMatch)
    {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for weekends;
        $constraint = new Weekends();

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * @return array[]
     */
    public function weekendsProvider(): array
    {
        return [
            ['Monday', false],
            ['Tuesday', false],
            ['Wednesday', false],
            ['Thursday', false],
            ['Friday', false],
            ['Saturday', true],
            ['Sunday', true],
            ['2018-05-23', false],
            ['2018-05-26', true],
            ['2018-05-27', true],
        ];
    }

    /**
     * @dataProvider weekendsNarrationProvider
     *
     * @param string $time
     * @param string $expectedNarration
     */
    public function testWeekendsNarration(
        string $time,
        string $expectedNarration
    ) {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for weekends;
        $constraint = new Weekends();

        // Then the constraint should narrate the time as expected.
        self::assertSame(
            $expectedNarration,
            $constraint->narrate($businessTime)
        );
    }

    /**
     * Provides times and the expected business time narration for them from a
     * Weekends constraint.
     *
     * @return array[]
     */
    public function weekendsNarrationProvider(): array
    {
        return [
            // Time     Narration
            ['Monday', 'outside business hours'],
            ['Tuesday', 'outside business hours'],
            ['Wednesday', 'outside business hours'],
            ['Thursday', 'outside business hours'],
            ['Friday', 'outside business hours'],
            ['Saturday', 'the weekend'],
            ['Sunday', 'the weekend'],
            ['2018-05-23', 'outside business hours'],
            ['2018-05-26', 'the weekend'],
        ];
    }
}

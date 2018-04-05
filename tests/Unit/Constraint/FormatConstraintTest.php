<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\FormatConstraint;
use PHPUnit\Framework\TestCase;

/**
 * Test the FormatConstraint business time constraint.
 */
class FormatConstraintTest extends TestCase
{
    /**
     * @dataProvider formatConstraintProvider
     *
     * @param string   $time
     * @param string   $format
     * @param string[] $patterns
     * @param bool     $shouldMatch
     */
    public function testFormatConstraint(
        string $time,
        string $format,
        array $patterns,
        bool $shouldMatch
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for a certain date format with patterns;
        $constraint = new FormatConstraint($format, ...$patterns);

        // Then the constraint should match the time as expected.
        self::assertSame(
            $shouldMatch,
            $constraint->isBusinessTime($businessTime)
        );
    }

    /**
     * Provides times with a format and corresponding patterns, and whether the
     * time should be matched as business time accordingly.
     *
     * @return array[]
     */
    public function formatConstraintProvider(): array
    {
        return [
            // Time    Format Patterns Match?
            ['Monday', 'l', ['Monday'], true],
            ['Tuesday', 'l', ['Monday'], false],
            ['Monday', 'l', ['Tuesday'], false],
            ['2018-05-23', 'l', ['Wednesday'], true],
            ['Tuesday', 'l', ['Monday', 'Tuesday'], true],
            ['January', 'F', ['January', 'February'], true],
            ['March', 'F', ['January', 'February'], false],
            ['2018-05-23', 'F', ['January'], false],
            ['2018-05-23', 'F', ['May'], true],
            ['09:00', 'a', ['am'], true],
            ['09:00', 'a', ['pm'], false],
            ['13:00', 'a', ['pm'], true],
            ['09:00', 'a', ['am', 'pm'], true],
            ['13:00', 'a', ['am', 'pm'], true],
            ['2018-05-23 03:00', 'a', ['am'], true],
            ['2018-05-23 17:00', 'a', ['am'], false],
            ['2018-05-23 17:00', 'a', ['am', 'pm'], true],
        ];
    }

    /**
     * @dataProvider formatConstraintNarrationProvider
     *
     * @param string $time
     * @param string $format
     * @param string $expectedNarration
     */
    public function testFormatConstraintNarration(
        string $time,
        string $format,
        string $expectedNarration
    ): void {
        // Given we have a business time;
        $businessTime = new BusinessTime($time);

        // And a constraint for a certain date format;
        $constraint = new FormatConstraint($format);

        // Then the constraint should narrate the time as expected.
        self::assertSame(
            $expectedNarration,
            $constraint->narrate($businessTime)
        );
    }

    /**
     * Provides times with a format how the time should be described as business
     * time accordingly.
     *
     * @return array[]
     */
    public function formatConstraintNarrationProvider(): array
    {
        return [
            // Time    Format Narration
            ['Monday', 'l', 'Monday'],
            ['Tuesday', 'l', 'Tuesday'],
            ['2018-05-23', 'l', 'Wednesday'],
            ['2018-01-05 09:00', 'l', 'Friday'],
            ['January', 'F', 'January'],
            ['March', 'F', 'March'],
            ['2018-01-05', 'F', 'January'],
            ['2018-05-23', 'F', 'May'],
            ['09:00', 'a', 'am'],
            ['13:00', 'a', 'pm'],
            ['2018-01-05 09:00', 'a', 'am'],
            ['2018-01-05 13:00', 'a', 'pm'],
        ];
    }
}

<?php

namespace BusinessTime\Tests\Unit\Constraint;

use BusinessTime\BusinessTime;
use BusinessTime\Constraint\BusinessTimeConstraint;
use BusinessTime\Constraint\FormatConstraint;
use BusinessTime\Constraint\Narration\DefaultNarrator;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test the DefaultNarrator decorator class.
 */
class DefaultNarratorTest extends TestCase
{
    /**
     * When the decorated constraint implements the narrator interface, then
     * that should be used.
     */
    public function testUsesNarrator()
    {
        // Given we have a constraint with narration;
        $constraint = new FormatConstraint('l');

        // And we decorate it with a default narrator;
        $decorated = new DefaultNarrator($constraint);

        // When we ask it to narrate a business time;
        $narration = $decorated->narrate(new BusinessTime('2018-05-23'));

        // Then the constraint's narration should be used.
        self::assertSame('Wednesday', $narration);
    }

    /**
     * When the decorated constraint does not implement the narrator interface,
     * then the decorator should use a default.
     *
     * @dataProvider defaultNarrationProvider
     *
     * @param string $time
     * @param string $expectedNarration
     */
    public function testOffersDefault(
        string $time,
        string $expectedNarration
    ) {
        // Given we have a constraint without narration;
        $constraint = new class() implements BusinessTimeConstraint {
            public function isBusinessTime(DateTimeInterface $time): bool
            {
                return true;
            }
        };

        // And we decorate it with a default narrator;
        $decorated = new DefaultNarrator($constraint);

        // When we ask it to narrate a business time;
        $narration = $decorated->narrate(new BusinessTime($time));

        // Then default narration should be used.
        self::assertSame($expectedNarration, $narration);
    }

    /**
     * Provides times with their expected default narration.
     *
     * @return array[]
     */
    public function defaultNarrationProvider(): array
    {
        return [
            // Time          Narration
            ['2018-05-23', 'Wednesday 23rd May 2018'],
            ['2018-05-23 13:00', 'Wednesday 23rd May 2018 13:00'],
        ];
    }
}

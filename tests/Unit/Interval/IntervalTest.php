<?php

namespace BusinessTime\Tests\Unit\Interval;

use BusinessTime\Interval;
use PHPUnit\Framework\TestCase;

class IntervalTest extends TestCase
{
    public function testPositiveInterval()
    {
        // Given we have a positive number of seconds;
        $seconds = 3600;

        // When we construct and interval with it;
        /** @var Interval $interval */
        $interval = Interval::seconds($seconds);

        // Then it should be correct.
        self::assertEquals(60, $interval->inMinutes());
    }

    public function testNegativeInterval()
    {
        // Given we have a negative number of seconds;
        $seconds = -3600;

        // When we construct and interval with it;
        /** @var Interval $interval */
        $interval = Interval::seconds(0);
        $interval->seconds = $seconds;
        $interval->invert = 1;

        // Then it should be correct.
        self::assertEquals(-3600, $interval->seconds);
        self::assertEquals(60, $interval->inMinutes());
    }
}

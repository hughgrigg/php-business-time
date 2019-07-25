<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::diffBusiness() method.
 */
class DiffBusinessTest extends TestCase
{
    /**
     * Should be able to handle start date being after end date.
     */
    public function testStartAfterEndAbsolute()
    {
        // Given we have a business time as a start;
        $start = new BusinessTime('Monday 11am');

        // And an end time that is before that;
        $end = new BusinessTime('Monday 10am');

        // When we get the absolute difference in business time;
        $diff = $start->diffBusiness($end);

        // Then it should still be correct.
        self::assertEquals(60, $diff->inMinutes());
    }

    /**
     * Should be able to handle start date being after end date.
     */
    public function testStartAfterEndNonAbsolute()
    {
        // Given we have a business time as a start;
        $start = new BusinessTime('Monday 11am');

        // And an end time that is before that;
        $end = new BusinessTime('Monday 10am');

        // When we get the non-absolute difference in business time;
        $diff = $start->diffBusiness($end, false);

        // Then it should still be correct (note weirdness with how DateInterval
        // handles negatives and units).
        self::assertEquals(-3600, $diff->seconds);
        self::assertEquals(60, $diff->inMinutes());
    }
}

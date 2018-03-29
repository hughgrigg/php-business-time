<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::diffInBusinessDays() method.
 */
class DiffInBusinessDaysTest extends TestCase
{
    /**
     * Test the diffInBusinessDays method with default behaviour.
     *
     * @dataProvider diffInBusinessDaysDefaultProvider
     *
     * @param string $day
     * @param string $otherDay
     * @param int    $expectedDiff
     */
    public function testDiffInBusinessDaysDefault(
        string $day,
        string $otherDay,
        int $expectedDiff
    ): void {
        // Given we have a business time for a particular day;
        $businessTime = new BusinessTime($day);

        // When we get the diff in business days from another day;
        $diff = $businessTime->diffInBusinessDays(new BusinessTime($otherDay));

        // Then we should get the expected diff.
        self::assertSame($expectedDiff, $diff);
    }

    /**
     * Return pairs of days with their expected diff in business days with the
     * default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00 and the precision is 1 hour.
     *
     * @return array[]
     */
    public function diffInBusinessDaysDefaultProvider(): array
    {
        // TODO: use short date format.
        return [
            // Going forward in time midnight to midnight.
            ['Monday 14th May 2018', 'Monday 14th May 2018', 0],
            ['Monday 14th May 2018', 'Tuesday 15th May 2018', 1],
            ['Monday 14th May 2018', 'Wednesday 16th May 2018', 2],
            ['Monday 14th May 2018', 'Thursday 17th May 2018', 3],
            ['Monday 14th May 2018', 'Friday 18th May 2018 ', 4],
            ['Monday 14th May 2018', 'Saturday 19th May 2018', 5],
            ['Monday 14th May 2018', 'Sunday 20th May 2018', 5],
            // Going forward in time with specific hours.
            ['Friday 18th May 2018 17:00', 'Saturday 19th May 2018 17:00', 0],
            ['Friday 18th May 2018 15:00', 'Saturday 19th May 2018 17:00', 0],
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 13:00', 0],
            ['Monday 14th May 2018 08:00', 'Monday 14th May 2018 13:00', 0],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 13:00', 0],
            ['Monday 14th May 2018 13:00', 'Monday 14th May 2018 17:00', 0],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 13:00', 1],
            ['Monday 14th May 2018 09:00', 'Friday 18th May 2018 13:00', 4],
            ['Monday 14th May 2018 09:00', 'Friday 18th May 2018 17:00', 5],
            ['Monday 14th May 2018 09:00', 'Friday 18th May 2018 19:00', 5],
            ['Monday 14th May 2018 09:00', 'Saturday 19th May 2018 07:00', 5],
            ['Monday 14th May 2018 09:00', 'Sunday 20th May 2018 13:00', 5],
            ['Monday 14th May 2018 09:00', 'Monday 30th May 2018 16:00', 15],
            // Going back in time midnight to midnight.
            ['Monday 14th May 2018', 'Monday 7th May 2018', 5],
            ['Monday 14th May 2018', 'Tuesday 8th May 2018', 4],
            ['Monday 14th May 2018', 'Wednesday 9th May 2018', 3],
            ['Monday 14th May 2018', 'Thursday 10th May 2018', 2],
            ['Monday 14th May 2018', 'Friday 11th May 2018', 1],
            ['Monday 14th May 2018', 'Saturday 12th May 2018', 0],
            ['Monday 14th May 2018', 'Sunday 13th May 2018', 0],
            // Going back in time with specific hours.
            ['Monday 14th May 2018 09:00', 'Monday 7th May 2018 09:00', 5],
            ['Monday 14th May 2018 17:00', 'Monday 7th May 2018 09:00', 6],
            ['Monday 14th May 2018 17:00', 'Monday 7th May 2018 13:00', 5],
            ['Monday 14th May 2018 10:00', 'Tuesday 8th May 2018 12:00', 3],
            ['Monday 14th May 2018 11:00', 'Wednesday 9th May 2018 13:00', 2],
            ['Monday 14th May 2018 17:00', 'Thursday 10th May 2018 13:00', 2],
            ['Monday 14th May 2018 15:00', 'Friday 11th May 2018 09:00', 1],
            ['Monday 14th May 2018 13:00', 'Saturday 12th May 2018 13:00', 0],
            ['Monday 14th May 2018 18:00', 'Sunday 13th May 2018 01:00', 1],
        ];
    }

    /**
     * Test the diffInPartialBusinessDays method with default behaviour.
     *
     * @dataProvider diffInPartialBusinessDaysDefaultProvider
     *
     * @param string $time
     * @param string $otherTime
     * @param float  $expectedDiff
     */
    public function testDiffInPartialBusinessDaysDefault(
        string $time,
        string $otherTime,
        float $expectedDiff
    ): void {
        // Given we have a business time for a particular time;
        $businessTime = new BusinessTime($time);

        // When we get the diff in partial business days from another time;
        $diff = $businessTime->diffInPartialBusinessDays(
            new BusinessTime($otherTime)
        );

        // Then we should get the expected diff.
        self::assertEquals(
            $expectedDiff,
            $diff,
            sprintf(
                'Expected business diff between %s and %s to be %.5f;'
                . ' got %.5f (off by %.6f)',
                $time,
                $otherTime,
                $expectedDiff,
                $diff,
                abs($expectedDiff - $diff)
            ),
            0.00001
        );
    }

    /**
     * Return pairs of days with their expected diff in partial business days
     * with the default behaviour, i.e. that working time is Monday to Friday
     * 09:00 to 17:00.
     *
     * @return array[]
     */
    public function diffInPartialBusinessDaysDefaultProvider(): array
    {
        // TODO: use short date format.
        return [
            // Going forward in time midnight to midnight.
            ['Monday', 'Monday', 0.0],
            ['Monday 14th May 2018', 'Tuesday 15th May 2018', 1.0],
            ['Monday 14th May 2018', 'Wednesday 16th May 2018', 2.0],
            ['Monday 14th May 2018', 'Thursday 17th May 2018', 3.0],
            ['Monday 14th May 2018', 'Friday 18th May 2018', 4.0],
            ['Monday 14th May 2018', 'Saturday 19th May 2018', 5.0],
            ['Monday 14th May 2018', 'Sunday 20th May 2018', 5.0],
            ['Friday 18th May 2018', 'Saturday 19th May 2018', 1.0],
            // Going forward in time with specific hours.
            ['Friday 18th May 2018 17:00', 'Saturday 19th May 2018 17:00', 0.0],
            [
                'Friday 18th May 2018 15:00',
                'Saturday 19th May 2018 17:00',
                0.25,
            ],
            ['Monday 14th May 2018 00:00', 'Monday 14th May 2018 13:00', 0.5],
            ['Monday 14th May 2018 08:00', 'Monday 14th May 2018 13:00', 0.5],
            ['Monday 14th May 2018 09:00', 'Monday 14th May 2018 13:00', 0.5],
            ['Monday 14th May 2018 13:00', 'Monday 14th May 2018 17:00', 0.5],
            ['Monday 14th May 2018 09:00', 'Tuesday 15th May 2018 13:00', 1.5],
            ['Monday 14th May 2018 09:00', 'Friday 18th May 2018 13:00', 4.5],
            ['Monday 14th May 2018 09:00', 'Friday 18th May 2018 17:00', 5.0],
            ['Monday 14th May 2018 09:00', 'Friday 18th May 2018 18:00', 5.0],
            ['Monday 14th May 2018 09:00', 'Saturday 19th May 2018 13:00', 5.0],
            ['Monday 14th May 2018 09:00', 'Sunday 20th May 2018 13:00', 5.0],
            ['Monday 14th May 2018 09:00', 'Monday 30th May 2018 17:00', 16.0],
            // Going back in time midnight to midnight.
            ['Monday 14th May 2018', 'Monday 7th May 2018', 5.0],
            ['Monday 14th May 2018', 'Tuesday 8th May 2018', 4.0],
            ['Monday 14th May 2018', 'Wednesday 9th May 2018', 3.0],
            ['Monday 14th May 2018', 'Thursday 10th May 2018', 2.0],
            ['Monday 14th May 2018', 'Friday 11th May 2018', 1.0],
            ['Monday 14th May 2018', 'Saturday 12th May 2018', 0.0],
            ['Monday 14th May 2018', 'Sunday 13th May 2018', 0.0],
            // Going back in time with specific hours.
            ['Monday 14th May 2018 09:00', 'Monday 7th May 2018 09:00', 5.0],
            ['Monday 14th May 2018 17:00', 'Monday 7th May 2018 09:00', 6.0],
            ['Monday 14th May 2018 17:00', 'Monday 7th May 2018 13:00', 5.5],
            ['Monday 14th May 2018 10:00', 'Tuesday 8th May 2018 12:00', 3.75],
            [
                'Monday 14th May 2018 11:00',
                'Wednesday 9th May 2018 13:00',
                2.75,
            ],
            [
                'Monday 14th May 2018 17:00',
                'Thursday 10th May 2018 13:00',
                2.5,
            ],
            ['Monday 14th May 2018 15:00', 'Friday 11th May 2018 09:00', 1.75],
            ['Monday 14th May 2018 13:00', 'Saturday 12th May 2018 13:00', 0.5],
            ['Monday 14th May 2018 18:00', 'Sunday 13th May 2018 01:00', 1.0],
        ];
    }
}

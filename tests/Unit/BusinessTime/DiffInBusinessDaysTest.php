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
        return [
            // Going forward in time midnight to midnight.
            ['Monday 2018-05-14', 'Monday 2018-05-14', 0],
            ['Monday 2018-05-14', 'Tuesday 2018-05-15', 1],
            ['Monday 2018-05-14', 'Wednesday 2018-05-16', 2],
            ['Monday 2018-05-14', 'Thursday 17th May 2018', 3],
            ['Monday 2018-05-14', 'Friday 2018-05-18 ', 4],
            ['Monday 2018-05-14', 'Saturday 2018-05-19', 5],
            ['Monday 2018-05-14', 'Sunday 20th May 2018', 5],
            // Going forward in time with specific hours.
            ['Friday 2018-05-18 17:00', 'Saturday 2018-05-19 17:00', 0],
            ['Friday 2018-05-18 15:00', 'Saturday 2018-05-19 17:00', 0],
            ['Monday 2018-05-14 00:00', 'Monday 2018-05-14 13:00', 0],
            ['Monday 2018-05-14 08:00', 'Monday 2018-05-14 13:00', 0],
            ['Monday 2018-05-14 09:00', 'Monday 2018-05-14 13:00', 0],
            ['Monday 2018-05-14 13:00', 'Monday 2018-05-14 17:00', 0],
            ['Monday 2018-05-14 09:00', 'Tuesday 2018-05-15 13:00', 1],
            ['Monday 2018-05-14 09:00', 'Friday 2018-05-18 13:00', 4],
            ['Monday 2018-05-14 09:00', 'Friday 2018-05-18 17:00', 5],
            ['Monday 2018-05-14 09:00', 'Friday 2018-05-18 19:00', 5],
            ['Monday 2018-05-14 09:00', 'Saturday 2018-05-19 07:00', 5],
            ['Monday 2018-05-14 09:00', 'Sunday 20th May 2018 13:00', 5],
            ['Monday 2018-05-14 09:00', 'Monday 30th May 2018 16:00', 15],
            // Going back in time midnight to midnight.
            ['Monday 2018-05-14', 'Monday 7th May 2018', 5],
            ['Monday 2018-05-14', 'Tuesday 8th May 2018', 4],
            ['Monday 2018-05-14', 'Wednesday 9th May 2018', 3],
            ['Monday 2018-05-14', 'Thursday 10th May 2018', 2],
            ['Monday 2018-05-14', 'Friday 2018-05-11', 1],
            ['Monday 2018-05-14', 'Saturday 12th May 2018', 0],
            ['Monday 2018-05-14', 'Sunday 13th May 2018', 0],
            // Going back in time with specific hours.
            ['Monday 2018-05-14 09:00', 'Monday 7th May 2018 09:00', 5],
            ['Monday 2018-05-14 17:00', 'Monday 7th May 2018 09:00', 6],
            ['Monday 2018-05-14 17:00', 'Monday 7th May 2018 13:00', 5],
            ['Monday 2018-05-14 10:00', 'Tuesday 8th May 2018 12:00', 3],
            ['Monday 2018-05-14 11:00', 'Wednesday 9th May 2018 13:00', 2],
            ['Monday 2018-05-14 17:00', 'Thursday 10th May 2018 13:00', 2],
            ['Monday 2018-05-14 15:00', 'Friday 2018-05-11 09:00', 1],
            ['Monday 2018-05-14 13:00', 'Saturday 12th May 2018 13:00', 0],
            ['Monday 2018-05-14 18:00', 'Sunday 13th May 2018 01:00', 1],
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
        return [
            // Going forward in time midnight to midnight.
            ['Monday', 'Monday', 0.0],
            ['Monday 2018-05-14', 'Tuesday 2018-05-15', 1.0],
            ['Monday 2018-05-14', 'Wednesday 2018-05-16', 2.0],
            ['Monday 2018-05-14', 'Thursday 17th May 2018', 3.0],
            ['Monday 2018-05-14', 'Friday 2018-05-18', 4.0],
            ['Monday 2018-05-14', 'Saturday 2018-05-19', 5.0],
            ['Monday 2018-05-14', 'Sunday 20th May 2018', 5.0],
            ['Friday 2018-05-18', 'Saturday 2018-05-19', 1.0],
            // Going forward in time with specific hours.
            ['Friday 2018-05-18 17:00', 'Saturday 2018-05-19 17:00', 0.0],
            ['Friday 2018-05-18 15:00', 'Saturday 2018-05-19 17:00', 0.25],
            ['Monday 2018-05-14 00:00', 'Monday 2018-05-14 13:00', 0.5],
            ['Monday 2018-05-14 08:00', 'Monday 2018-05-14 13:00', 0.5],
            ['Monday 2018-05-14 09:00', 'Monday 2018-05-14 13:00', 0.5],
            ['Monday 2018-05-14 13:00', 'Monday 2018-05-14 17:00', 0.5],
            ['Monday 2018-05-14 09:00', 'Tuesday 2018-05-15 13:00', 1.5],
            ['Monday 2018-05-14 09:00', 'Friday 2018-05-18 13:00', 4.5],
            ['Monday 2018-05-14 09:00', 'Friday 2018-05-18 17:00', 5.0],
            ['Monday 2018-05-14 09:00', 'Friday 2018-05-18 18:00', 5.0],
            ['Monday 2018-05-14 09:00', 'Saturday 2018-05-19 13:00', 5.0],
            ['Monday 2018-05-14 09:00', 'Sunday 20th May 2018 13:00', 5.0],
            ['Monday 2018-05-14 09:00', 'Monday 30th May 2018 17:00', 16.0],
            // Going back in time midnight to midnight.
            ['Monday 2018-05-14', 'Monday 7th May 2018', 5.0],
            ['Monday 2018-05-14', 'Tuesday 8th May 2018', 4.0],
            ['Monday 2018-05-14', 'Wednesday 9th May 2018', 3.0],
            ['Monday 2018-05-14', 'Thursday 10th May 2018', 2.0],
            ['Monday 2018-05-14', 'Friday 2018-05-11', 1.0],
            ['Monday 2018-05-14', 'Saturday 12th May 2018', 0.0],
            ['Monday 2018-05-14', 'Sunday 13th May 2018', 0.0],
            // Going back in time with specific hours.
            ['Monday 2018-05-14 09:00', 'Monday 7th May 2018 09:00', 5.0],
            ['Monday 2018-05-14 17:00', 'Monday 7th May 2018 09:00', 6.0],
            ['Monday 2018-05-14 17:00', 'Monday 7th May 2018 13:00', 5.5],
            ['Monday 2018-05-14 10:00', 'Tuesday 8th May 2018 12:00', 3.75],
            ['Monday 2018-05-14 11:00', 'Wednesday 9th May 2018 13:00', 2.75],
            ['Monday 2018-05-14 17:00', 'Thursday 10th May 2018 13:00', 2.5],
            ['Monday 2018-05-14 15:00', 'Friday 2018-05-11 09:00', 1.75],
            ['Monday 2018-05-14 13:00', 'Saturday 12th May 2018 13:00', 0.5],
            ['Monday 2018-05-14 18:00', 'Sunday 13th May 2018 01:00', 1.0],
        ];
    }
}

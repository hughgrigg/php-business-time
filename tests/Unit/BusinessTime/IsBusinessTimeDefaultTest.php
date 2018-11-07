<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

class IsBusinessTimeDefaultTest extends TestCase
{
    /**
     * Test the BusinessDateTime::isBusinessTime() method with default
     * behaviour, i.e. that business hours are Monday to Friday 09:00 to 17:00.
     *
     * @dataProvider isBusinessTimeDefaultProvider
     *
     * @param string $time
     * @param bool   $expectedToBeBusinessTime
     */
    public function testIsBusinessTimeDefault(
        string $time,
        bool $expectedToBeBusinessTime
    ) {
        // Given we have a time with default behaviour;
        $businessTime = new BusinessTime($time);

        // Then it should know whether or not it is business time.
        self::assertSame(
            $expectedToBeBusinessTime,
            $businessTime->isBusinessTime()
        );
    }

    /**
     * Provides times and whether they are expected to be business time with the
     * default behaviour.
     *
     * @return array[]
     */
    public function isBusinessTimeDefaultProvider(): array
    {
        return [
            ['Monday 00:00', false],
            ['Monday 08:59', false],
            ['Monday 09:00', true],
            ['Monday 12:00', true],
            ['Monday 16:59', true],
            ['Monday 17:00', false],
            ['Monday 23:59', false],
            ['Tuesday 10:00', true],
            ['Wednesday 11:00', true],
            ['Thursday 12:00', true],
            ['Friday 13:00', true],
            ['Saturday', false],
            ['Sunday', false],
            ['Saturday 10:00', false],
            ['Sunday 16:00', false],
        ];
    }
}

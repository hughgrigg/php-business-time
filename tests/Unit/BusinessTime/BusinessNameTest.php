<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use PHPUnit\Framework\TestCase;

/**
 * Test the BusinessTime::businessName() method.
 */
class BusinessNameTest extends TestCase
{
    /**
     * Test the BusinessTime::businessName() method with default behaviour.
     *
     * @dataProvider businessNameDefaultProvider
     *
     * @param string $time
     * @param string $expectedBusinessName
     */
    public function testBusinessNameDefault(
        string $time,
        string $expectedBusinessName
    ): void {
        // Given we have a business time for a particular time;
        $businessTime = new BusinessTime($time);

        // Then the business name should be as expected.
        self::assertSame($expectedBusinessName, $businessTime->businessName());
    }

    /**
     * Provides times and their expected business name with default behaviour,
     * i.e. that business hours are Monday to Friday 9:00 to 17:00.
     *
     * @return array[]
     */
    public function businessNameDefaultProvider(): array
    {
        return [
            // Time Expected business name
            ['Monday 00:00', 'outside business hours'],
            ['Monday 08:00', 'outside business hours'],
            ['Monday 08:59', 'outside business hours'],
            ['Monday 9:00', 'business hours'],
            ['Friday 16:00', 'business hours'],
            ['Friday 16:59', 'business hours'],
            ['Friday 17:00', 'outside business hours'],
            ['Friday 23:59', 'outside business hours'],
            ['Saturday 00:00', 'the weekend'],
            ['Saturday 10:00', 'the weekend'],
            ['Sunday 13:00', 'the weekend'],
            ['Sunday 23:59', 'the weekend'],
        ];
    }

    /**
     * A fall-back business name should be used if there are no business time
     * constraints.
     */
    public function testFallBackBusinessName(): void
    {
        // Given we have a business time for a particular time;
        $businessTime = new BusinessTime('2018-05-23 13:00');

        // And it has no business time constraints;
        $businessTime->setBusinessTimeConstraints();

        // Then a fall-back business time name should be used.
        self::assertSame(
            'Wednesday 23rd May 2018 13:00',
            $businessTime->businessName()
        );
    }
}

<?php

namespace BusinessTime\Tests\Unit\BusinessTimeFactory;

use BusinessTime\BusinessTimeFactory;
use BusinessTime\Constraint\DaysOfWeek;
use BusinessTime\Interval;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Unit test the BusinessTimeFactory class.
 */
class BusinessTimeFactoryTest extends TestCase
{
    /**
     * Should be able to make a business time instance.
     */
    public function testMake()
    {
        // Given we have a business time factory;
        $factory = new BusinessTimeFactory();
        $factory->setConstraints(new DaysOfWeek('Tuesday'));
        $factory->setPrecision(Interval::minutes(30));
        $factory->setIterationLimit(42);

        // When we use it to make a business time instance;
        $businessTime = $factory->make('Wednesday 13:30');

        // Then the instance should be set up correctly.
        self::assertSame('30 minutes', $businessTime->precision()->forHumans());
        self::assertCount(1, $businessTime->businessTimeConstraints());
        self::assertSame('Wednesday 13:30', $businessTime->format('l H:i'));
    }

    /**
     * Should be able to make a business time instance for the current time.
     */
    public function testNow()
    {
        // Given we have a business time factory;
        $factory = new BusinessTimeFactory();
        $factory->setConstraints(new DaysOfWeek('Tuesday'));
        $factory->setPrecision(Interval::minutes(15));

        // When we use it to make a business time instance for the current time;
        $now = Carbon::now();
        $businessTime = $factory->now();

        // Then the instance should be set up correctly.
        self::assertSame('15 minutes', $businessTime->precision()->forHumans());
        self::assertCount(1, $businessTime->businessTimeConstraints());
        self::assertSame($now->format('l'), $businessTime->format('l'));
    }

    /**
     * Should be able to serialize and deserialize the factory.
     */
    public function testSerialization()
    {
        // Given we have a business time factory;
        $factory = new BusinessTimeFactory();
        $factory->setConstraints(new DaysOfWeek('Tuesday'));
        $factory->setPrecision(Interval::minutes(15));

        // And we serialize it;
        $serialized = serialize($factory);

        // And then we deserialize it;
        $unSerialized = unserialize($serialized);
        \assert($unSerialized instanceof BusinessTimeFactory);

        // When we use it to make a business time instance;
        $businessTime = $unSerialized->make('Friday 15:30');

        // Then the instance should be set up correctly.
        self::assertSame('15 minutes', $businessTime->precision()->forHumans());
        self::assertCount(1, $businessTime->businessTimeConstraints());
        self::assertSame('Friday 15:30', $businessTime->format('l H:i'));
    }
}

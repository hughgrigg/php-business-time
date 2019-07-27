<?php

namespace BusinessTime\Tests\Unit\BusinessTime;

use BusinessTime\BusinessTime;
use BusinessTime\LimitedIterator;
use LengthException;
use PHPUnit\Framework\TestCase;
use Throwable;

class IterationLimitTest extends TestCase
{
    public function testThrowsOnIterationLimit()
    {
        // Given we have a business time;
        $businessTime = new BusinessTime();

        // And we set the iteration limit to be very low;
        $businessTime->setIterationLimit(3);

        // When we try to add several business hours;
        $error = null;

        try {
            $businessTime->addBusinessHours(36);
        } catch (Throwable $e) {
            $error = $e;
        }

        // Then an error should be thrown due to the iteration limit.
        self::assertInstanceOf(LengthException::class, $error);
        self::assertEquals($error->getMessage(), 'Iteration limit of 3 reached.');
    }

    public function testLimitedIterator()
    {
        // Given we have a limited iterator;
        $limit = new LimitedIterator(5);

        // When we iterate within the limit;
        while ($limit->current() < 5) {
            $limit->next();
        }

        // Then the iterator should not throw;
        self::assertEquals(5, $limit->key());
        self::assertTrue($limit->valid());

        // But when we exceed the limit;
        $limit->rewind();
        $error = null;

        try {
            for ($i = 0; $i < 6; $i++) {
                $limit->next();
            }
        } catch (Throwable $e) {
            $error = $e;
        }

        // Then an exception should be thrown.
        self::assertInstanceOf(LengthException::class, $error);
        self::assertEquals($error->getMessage(), 'Iteration limit of 5 reached.');
        self::assertFalse($limit->valid());
    }
}

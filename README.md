# Business Time in PHP

[![Build Status](https://travis-ci.org/hughgrigg/php-business-time.svg?branch=master)](https://travis-ci.org/hughgrigg/php-business-time)
[![Coverage Status](https://coveralls.io/repos/github/hughgrigg/php-business-time/badge.svg)](https://coveralls.io/github/hughgrigg/php-business-time)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ef5b774bce624ab2b1f3632e9307a909)](https://app.codacy.com/app/hugh_2/php-business-time?utm_source=github.com&utm_medium=referral&utm_content=hughgrigg/php-business-time&utm_campaign=badger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hughgrigg/php-business-time/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hughgrigg/php-business-time/?branch=master)
[![StyleCI](https://styleci.io/repos/126614310/shield?branch=master)](https://styleci.io/repos/126614310)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

"Business time" logic in PHP (aka "business hours", "working days" etc). This
can be useful for calculating shipping dates, for example.

This library provides an extension for the `Carbon` class in the
[Carbon](http://carbon.nesbot.com/docs/) date time library.

While Carbon already has methods like `addWorkingDays()` and
`diffInWeekendDays()`, this extension lets you handle business time more
precisely and flexibly. It can consider public holidays from WebCal.fi, as well
as your own customised times which can be specified directly or with constraint-
matching.

[Official music video for this library](https://www.youtube.com/watch?v=WGOohBytKTU)

## Installation

Install via Composer:

```bash
composer require hughgrigg/business-time
```

## Usage

The `BusinessTime` class in this package extends `Carbon`. This means that you
can use all of the methods from `Carbon` and the native `DateTime`, as well as
the ones described here.

### Business days

You'll probably be dealing with business days most often.

#### Adding or subtracting business days

You can add or subtract business days from a given starting date:

```php
$friday = new BusinessTime\BusinessTime('Friday');
$nextBusinessDay = $friday->addBusinessDay();
// = Monday
$threeBusinessTime = $friday->addBusinessDays(3);
// = Wednesday
```

```php
$monday = new BusinessTime\BusinessTime('Monday');
$previousBusinessDay = $now->subBusinessDay();
// = Friday
$threeBusinessTimeAgo = $now->subBusinessDays(3);
// = Wednesday
```

#### Diff in business days

Besides adding or subtracting business days, you can also calculate the number
of business days between two given dates.

```php
$now = BusinessTime\BusinessTime::now();
$nextWeek = $now->addWeek(); // a full 7-day week.
$diff = $now->diffInBusinessDays($nextWeek);
// = 5
```

#### Whole vs partial business days

The examples above deal with *whole* business days. You could also describe this
as *integer days*. This means that any fractional part of a day is not
considered to be a business day and is not counted.

For example, if we ask how many business days there are between 10am Friday and
10am Saturday, the answer is zero:

```php
$fridayTenAm = new BusinessTime\BusinessTime('Friday 10am');
$saturdayTenAm = $fridayTenAm->addDay(); // Add a full day.
$fridayTenAm->diffInBusinessDays($saturdayTenAm);
// = 0
```

This may be surprising if you were expecting the business hours on Friday to be
included. The reason the result is zero is because no *whole* business day has
passed in that time; even most of a business day is not enough to be counted.

If you do want to consider partial days, you can use the equivalent partial
methods to get a float value.

```php
$fridayTenAm = new BusinessTime\BusinessTime('Friday 10am');
$fridayTenAm->diffInPartialBusinessDays('Saturday 10am');
// = 0.875
```

These are kept separate because usually people do not want to deal with the
concept of fractional business time: either a business day has passed or it has
not. The `partial` methods let you access the precise floating point numbers
when you want to.

#### Length of a business day

To calculate a partial business day, we need to know the total length of time of
a business day. For example, 09:00 to 17:00 could be 100% of a business day if
those are the business hours, but only 80% of a business day if the hours are
09:00 to 19:00.

Out of the box, BusinessTime treats a business day as being 8 hours long (09:00
to 17:00). You can adjust this to suit your needs, though.

The simplest way to configure this is to directly set the length of a business
day:

```php
$businessTime = new BusinessTime\BusinessTime();
$businessTime->setLengthOfBusinessDay(BusinessTime\Interval::hours(6));
```

See https://secure.php.net/manual/en/class.dateinterval.php

If you have complicated business time constraints (see below), it might be
helpful to let BusinessTime calculate the length of a business day for you. You
can do that by passing in a `DateTime` representing your standard business day
to the `determineLengthOfBusinessDay()` method. BusinessTime will then calculate
the length of the business day based on that using its constraints.

```php
$businessTime = new BusinessTime\BusinessTime();
$businessTime->determineLengthOfBusinessDay(new DateTime('Monday'));
```

### Business hours

You can also make business time calculations in hours:

```php
$now = new BusinessTime\BusinessTime();
$now->addBusinessHour();
$now->addBusinessHours(3);
```

```php
$now = new BusinessTime\BusinessTime();
$now->diffInBusinessHours();
$now->diffInPartialBusinessHours();
```

The reason a day is the largest unit included out-of-the-box is because people
and organisations have different understandings of what is meant by larger units
of time. Not having built-in methods for those prevents assumptions being made
and forces explicitness, e.g. with `$now->addBusinessTime(30)`.

Similarly, no unit smaller than an hour is included out-of-the-box because the
concept of a "business minute" is questionable for most use cases. You can
easily calculate minutes by multiplying by 60. Note that because the default
precision is one hour, you may well need to adjust the precision to e.g 15
minutes to get accurate calculations (see the note on precision and
performance).

## Describing business times

In some situations it's useful to have meaningful descriptions for business and
non-business times. For example, you might want to tell your customer that you
won't deliver their order until next week because the weekend is in between.

You can use the `BusinessTimePeriod` class for this. You can make an instance
with start and end times like this:

```php
$start = new BusinessTime\BusinessTime('today');
$end = $start->addBusinessDays(3);
$timePeriod = BusinessTime\BusinessTimePeriod::fromBusinessTimes($start, $end);
```

You can then use the `businessDaysTo()` and `nonBusinessDaysTo()` methods on the
time period to get that information. For example:

```php
$businessTime = new BusinessTime\BusinessTime();
$nonBusinessTimes = $businessTime->nonBusinessDays();
```

This returns an array of `BusinessTime` objects for each non-business day, which
can tell you their description:

```php
$nonBusinessTimes[0]->businessName();
// = e.g. "the weekend"
```

What intervals and descriptions you get depends on which business time
constraints have been used.

You can also ask a `BusinessTimePeriod` for its business and non-business sub-
periods, for example:

```php
$start = new BusinessTime\BusinessTime('today');
$end = new BusinessTime\BusinessTime('tomorrow');
$timePeriod = BusinessTime\BusinessTimePeriod::fromBusinessTimes($start, $end);

$businessPeriods = $timePeriod->businessPeriods();
// = array of BusinessTimePeriod instances for each period of business time.
```

This lets you see the precise business timings that make up the whole time
period. You can ask each sub-period for its business-relevant name with the
`businessName()` method.

## Start and end of business day

You can get the start or end of the business day based on the business time
constraints like this:

```php
$businessTime = new BusinessTime\BusinessTime();
$businessTime->startOfBusinessDay();
// = BusinessTime instance for e.g. 09:00
$businessTime->endOfBusinessDay();
// = BusinessTime instance for e.g. 17:00
```

## Determining business time

By default, this library considers Monday to Friday, 9am to 5pm to be business
time. You can configure this to suit your needs, though.

### Business time constraints

You can set the constraints to determine business time on the `BusinessTime`
class like this:

```php
$businessTime = new BusinessTime\BusinessTime();
$businessTime->setBusinessTimeConstraints(
    new BusinessTime\Constraint\WeekDays(),
    new BusinessTime\Constraint\BetweenHoursOfDay(9, 17),
);
```

You can pass as many constraints as you need; *all* of the constraints must be
satisfied for a given time to be considered business time.

Calling `setBusinessTimeConstraints()` replaces any existing constraints on the
`BusinessTime` instance.

The following constraints are available out-of-the-box, some of which can be
customised via their constructors:

```php
new BusinessTime\Constraint\HoursOfDay(10, 13, 17);
new BusinessTime\Constraint\BetweenHoursOfDay(9, 17);
new BusinessTime\Constraint\BetweenTimesOfDay('08:45', '17:30');
new BusinessTime\Constraint\WeekDays();
new BusinessTime\Constraint\Weekends();
new BusinessTime\Constraint\DaysOfWeek('Monday', 'Wednesday', 'Friday');
new BusinessTime\Constraint\BetweenDaysOfWeek('Monday', 'Friday');
new BusinessTime\Constraint\DaysOfMonth(1, 8, 23);
new BusinessTime\Constraint\BetweenDaysOfMonth(1, 20);
new BusinessTime\Constraint\MonthsOfYear('January', 'March', 'July');
new BusinessTime\Constraint\BetweenMonthsOfYear('January', 'November');
new BusinessTime\Constraint\DaysOfYear('January 8th', 'March 16th', 'July 4th');
new BusinessTime\Constraint\BetweenDaysOfYear('January 1st', 'December 5th');
new BusinessTime\Constraint\Dates('2019-01-17', '2019-09-23', '2020-05-11');
new BusinessTime\Constraint\BetweenDates('2018-01-11', '2018-12-31');
new BusinessTime\Constraint\AnyTime(); // Oh dear.
```

#### Inversion of business time constraints

You can wrap any business time constraint in a `Not` constraint to invert it.

For example:

```php
$decemberOff = new BusinessTime\Constraint\Composite\Not(
    BusinessTime\Constraint\MonthsOfYear('December')
);
```

This constraint now matches any time that is *not* in the month of December. You
can pass as many other constraints as you need into the `Not` constructor.

#### Exceptions to business time constraints

The constraints above have an `except()` method that takes one or more other
constraints. This creates a composite constraint that lets you add exceptions to
your business time rules.

For example:

```php
$lunchTimeOff = (new BusinessTime\Constraint\BetweenHoursOfDay(9, 17))->except(
    new BusinessTime\Constraint\HoursOfDay(13)
);
```

That constraint now matches any time between 9am and 5pm *except* for the hour
between 1pm and 2pm. You can pass as many exceptional constraints as you need
into the `except()` method.

*Note*: You can use the `except()` method on the `AnyTime` constraint as an
alternative way to define your constraints:

```php
(new BusinessTime\Constraint\AnyTime())->except(
    new BusinessTime\Constraint\DaysOfWeek('Friday')
);
// All times except Fridays are considered business time.
```

If `except()` is not enough for your needs, you can also use the `andAlso()` and
`orAlternatively()` methods to build different types of composite constraints.

#### Custom business time constraints

You can implement your own custom constraints by implementing the
`BusinessTime\Constraint\Constraint` interface:

```php
interface BusinessTimeConstraint
{
    public function isBusinessTime(DateTimeInterface $time): bool;
}
```

The constraint must take an instance of `DateTimeInterface` and return whether
or not it should be considered business time.

If you want to enable combinatorial logic for your custom constraint, use the
`BusinessTime\Constraint\Composite\Combinations` trait.

*Tip*: It's usually better to use multiple simple constraints together than to
make one big, complex one.

#### Business time constraints example

Here's a somewhat complicated example of using business time constraints:

```php
$businessTime = new BusinessTime\BusinessTime();
$businessTime->setBusinessTimeConstraints(
    (new BusinessTime\Constraint\BetweenHoursOfDay(10, 18))->except(
        new BusinessTime\Constraint\BetweenTimesOfDay('13:00', '14:00')
    ), // 9-6 every day, with an hour for lunch.
    (new BusinessTime\Constraint\WeekDays())->except(
        new BusinessTime\Constraint\WeekDays('Thursday')
    ), // Week days, but let's take Thursdays off.
    new BusinessTime\Constraint\BetweenMonthsOfYear('January', 'November'),
    // No-one does any work in December anyway.
    new BusinessTime\Constraint\Composite\Not(
        new BusinessTime\Constraint\DaysOfYear('August 23rd', 'October 20th')
    ) // Why not take off your birthday and wedding anniversary?
);
```

## Incorporating business time data from a remote source (WIP)

Whilst you could try to set up constraints covering all the public holidays in
your country, it's probably easier to just retrieve them from a remote source.

BusinessTime supports this for WebCal.fi out of the box.

### WebCal.fi

WebCal.fi is easy to use as its data is provided publicly without any
authentication. You  do need to include the Guzzle library in your project to
use this, though.

```php
$factory = new BusinessTime\Remote\WebCalFiFactory(
    'https://www.webcal.fi/cal.php?id=83&format=json'
);
$dates = $factory->getDates();
// = array of dates from the specified calendar.
$webCalFiConstraint = $factory->getDatesConstraint();
// = a Dates constraint containing the retrieved dates and their descriptions.
```

You can find WebCal.fi calendars to suit your needs at e.g.
https://www.webcal.fi/en-GB/other_file_formats.php

Note that you can also use the `except()` method on the WebCalFi constraint to
add customised exceptions to the dates it provides.

### Custom remote sources

You can add any other source you like by implementing the `Constraint` interface
described above.

## Recurring business deadlines (WIP)

As well as calculating business time, it's often useful to make calculations
about deadlines or "cut-off" times. For example, the cut-off time for
dispatching orders might be 11am on week days. BusinessTime provides logic for
dealing with this.

You can create deadlines using the same time constraints described above:

```php
$deadline = new BusinessTime\Deadlines\RecurringDeadline(
    new BusinessTime\Constraint\Weekdays(),
    new BusinessTime\Constraint\TimesOfDay('11:00')
);
```

Any time matching all the constraints is considered an occurrence of the
deadline. This means the deadline recurs on a regular basis (it is not a single
moment in time).

To find out when the deadline next occurs, you can use the
`nextOccurrenceFrom()` method:

```php
$businessTime = new BusinessTime\BusinessTime();
$deadline->nextOccurrenceFrom($businessTime);
// = a new business time instance for the time the deadline next occurs.
```

In this example, this might give you 11am today, or 11am next Monday if it's now
already later than 11am on a Friday.

There is a `previousOccurrenceFrom()` that does the equivalent going back from
the given time.

You can also see if a deadline has passed in a given time period:

```php
$deadline->hasPassedToday();
// = true if the deadline has been passed today.
$deadline->hasPassedBetween(
    BusinessTime\BusinessTime::now->subWeek(),
    BusinessTime\BusinessTime::now->addWeek()
);
// = true if the deadline is ever passed in the given time period.
```

*Important*: the deadlines described above are designed to handle recurring
deadlines. They not appropriate for determining singular moments in time. To
make comparisons against a single moment, you should simply use the comparison
methods provided by Carbon:

```php
$time = new BusinessTime\BusinessTime();
$deadline = new BusinessTime\BusinessTime('2018-12-08 17:00');
$time->gt($deadline);
// = true if the moment has passed.
```

## Business time factory (WIP)

You probably don't want to have to set up an instance of
`BusinessTime\BusinessTime` in every place you want to use one in your code.

To avoid that, you can set up a `BusinessTime\Factory` with the constraints you
need once and then use that everywhere.

For example:

```php
$factory = new BusinessTime\Factory();
$factory->setBusinessTimeConstraints(
    new BusinessTime\Constraint\DaysOfWeek('Saturday', 'Sunday'),
    new BusinessTime\Constraint\Dates('2018-12-25'),
);
```

Once the factory is set up, you can share it in whatever way you usually share
dependencies. For example, you might add it to the container in a framework like
Laravel or Symfony.

When you've got the instance of the factory, you can get a ready-made instance
of `BusinessTime\BusinessTime` from it:

```php
$date = $factory->make('2018-03-21');
$now = $factory->now();
```

## Loading from a cache, database, file system etc (WIP)

If you're loading dates from remote sources, you most likely want to cache them
to keep your application fast.

The `BusinessTime\Factory` class implements the `__sleep()` and `__wakeup()`
methods so it is easy to serialize and deserialize in whatever storage system
you want to use.

For example, if you've got a cache instance, you can do something like this:

```php
$factory = new BusinessTime\Factory();
// Set up your constraints.
$cache->put('business-days', serialize($factory));

// Some time later...

$factory = deserialize($cache->get('business-days'));
$now = $factory->now();
// ... off you go.
```

All you need is a storage system that can store a string and return it later.
Some cache systems, such as the one provided by Laravel, can handle the
serialization and deserialization for you, so you only need to pass in the
BusinessTime factory instance directly.

## Precision

By default, BusinessTime uses hour precision. This means that it calculates
business time roughly accurate to an hour.

If you need better precision than this, you can set it to what you want:

```php
$businessTime = new BusinessTime\BusinessTime();
$businessTime->setPrecision(BusinessTime\Interval::minutes(30)); // Half-hour precision.
$businessTime->setPrecision(BusinessTime\Interval::minutes(15)); // Quarter-hour precision.
```

You can also set precision on the business time factory in the same way.

Note that the higher the precision, the lower the performance is. This is
because BusinessTime must check each interval of the size you specify. For
example, at hour precision, dealing with one week requires `7 * 24 = 168`
iterations. At minute precision, this becomes `7 * 24 * 60 = 10080`
iterations, which is 60× slower.

Always try to set the largest precision interval that covers your needs.

## Testing

You can use the testing facilities from Carbon described here:

http://carbon.nesbot.com/docs/#api-testing

For example, you can mock the current time like this:

```php
Carbon::setTestNow($knownDate);
```

And proceed from there in your testing.

To run the tests for the BusinessTime package itself, you can run the tests in
this directory:

```bash
make test
```

You can also read the tests for more detailed usage examples of the library.

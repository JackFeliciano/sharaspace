<?php

declare(strict_types=1);

use Sharaspace\Home\Sample;
use Sharaspace\Tests\Assert;

return [
    ['the sample holds three spaces', static function (): void {
        Assert::same(3, (new Sample())->count());
    }],
    ['the sample holds three leases', static function (): void {
        Assert::same(3, (new Sample())->taken());
    }],
    ['the sample billing adds the three leases', static function (): void {
        // The floor holds two weeks at 240000 and three months at 1440000, and the
        // back room holds twelve months at 14400000.
        Assert::same(16080000, (new Sample())->billing());
    }],
    ['the third space stands closed', static function (): void {
        $calendar = (new Sample())->calendars()[2];
        Assert::same(false, $calendar->space->isOpen());
        Assert::same([], $calendar->leases());
    }],
];

<?php

declare(strict_types=1);

use Sharaspace\Home\Sample;
use Sharaspace\Tests\Assert;

return [
    ['the sample holds three rooms', static function (): void {
        Assert::same(3, (new Sample())->count());
    }],
    ['the sample holds three bookings', static function (): void {
        Assert::same(3, (new Sample())->taken());
    }],
    ['the sample takings add the two rooms', static function (): void {
        // The Loft holds 4 hours at 24000 and the Hall 4 hours at 60000. Both run
        // a third of a day, so a day rate of 12 hours prices them at 8000 and 20000
        // against a full day of 12 hours, and the two bookings come to 28000 plus
        // the Loft's second booking of 8000, which no confirm touched.
        Assert::same(36000, (new Sample())->takings());
    }],
    ['the third room stands closed', static function (): void {
        $diary = (new Sample())->diaries()[2];
        Assert::same(false, $diary->room->isOpen());
        Assert::same([], $diary->bookings());
    }],
];

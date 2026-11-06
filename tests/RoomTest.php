<?php

declare(strict_types=1);

use Sharaspace\Home\Room;
use Sharaspace\Tests\Assert;

return [
    ['a room seats at least one person', static function (): void {
        Assert::throws(static fn () => new Room('r1', 'Loft', 'Porto', 0, '24000'), 'at least one person');
    }],
    ['a room carries a day rate above zero', static function (): void {
        Assert::throws(static fn () => new Room('r1', 'Loft', 'Porto', 10, '0'), 'day rate above zero');
    }],
    ['half a day costs half the day rate', static function (): void {
        $room = new Room('r1', 'Loft', 'Porto', 10, '24000');
        Assert::same(12000, $room->rateFor(6));
        Assert::same(24000, $room->rateFor(12));
    }],
    ['a booking runs between one hour and twelve', static function (): void {
        $room = new Room('r1', 'Loft', 'Porto', 10, '24000');
        Assert::throws(static fn () => $room->rateFor(0), 'at least one hour');
        Assert::throws(static fn () => $room->rateFor(13), 'twelve hours');
    }],
    ['a closed room says so', static function (): void {
        $room = new Room('r1', 'Loft', 'Porto', 10, '24000');
        Assert::same(true, $room->isOpen());
        $room->close();
        Assert::same(false, $room->isOpen());
    }],
];

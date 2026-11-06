<?php

declare(strict_types=1);

use Sharaspace\Home\Diary;
use Sharaspace\Home\Room;
use Sharaspace\Home\Slot;
use Sharaspace\Tests\Assert;

function ssp_loft(): Diary
{
    return new Diary(new Room('ss-1', 'The Loft', 'Porto', 40, '24000'));
}

return [
    ['a slot ends after it starts', static function (): void {
        Assert::throws(static fn () => new Slot('2026-12-05', 13, 13), 'ends after it starts');
    }],
    ['a slot stays inside one day', static function (): void {
        Assert::throws(static fn () => new Slot('2026-12-05', -1, 4), 'between hour 0 and hour 24');
        Assert::throws(static fn () => new Slot('2026-12-05', 20, 25), 'between hour 0 and hour 24');
    }],
    ['a slot counts its hours', static function (): void {
        Assert::same(4, (new Slot('2026-12-05', 9, 13))->hours());
        Assert::same('2026-12-05 09 to 13', (new Slot('2026-12-05', 9, 13))->label());
    }],
    ['overlapping hours clash and touching hours do not', static function (): void {
        $a = new Slot('2026-12-05', 9, 13);
        Assert::same(true, $a->clashesWith(new Slot('2026-12-05', 12, 18)));
        Assert::same(false, $a->clashesWith(new Slot('2026-12-05', 13, 18)));
        Assert::same(false, $a->clashesWith(new Slot('2026-12-06', 9, 13)));
    }],
    ['a second booking on taken hours is refused', static function (): void {
        $diary = ssp_loft();
        $diary->book('bk-1', 'Mara Lind', new Slot('2026-12-05', 9, 13));
        Assert::throws(static fn () => $diary->book('bk-2', 'Bright Field', new Slot('2026-12-05', 12, 18)), 'already taken');
        Assert::same(1, $diary->taken());
    }],
    ['a cancelled booking frees the slot', static function (): void {
        $diary = ssp_loft();
        $first = $diary->book('bk-1', 'Mara Lind', new Slot('2026-12-05', 9, 13));
        Assert::same(false, $diary->free(new Slot('2026-12-05', 9, 13)));
        $first->cancel();
        Assert::same(true, $diary->free(new Slot('2026-12-05', 9, 13)));
        Assert::same(0, $diary->taken());
        Assert::same(0, $diary->takings());
    }],
    ['a booking keeps the rate the room carried', static function (): void {
        $diary = ssp_loft();
        $booking = $diary->book('bk-1', 'Mara Lind', new Slot('2026-12-05', 9, 13));
        Assert::same(8000, $booking->cents());
        Assert::same('held', $booking->state());
        $booking->confirm();
        Assert::same('confirmed', $booking->state());
        Assert::same(8000, $diary->takings());
    }],
    ['only a held booking confirms', static function (): void {
        $diary = ssp_loft();
        $booking = $diary->book('bk-1', 'Mara Lind', new Slot('2026-12-05', 9, 13));
        $booking->cancel();
        Assert::throws(static fn () => $booking->confirm(), 'only a held booking');
        Assert::throws(static fn () => $booking->cancel(), 'stays cancelled');
    }],
    ['a closed room takes no booking', static function (): void {
        $diary = ssp_loft();
        $diary->room->close();
        Assert::throws(static fn () => $diary->book('bk-1', 'Mara Lind', new Slot('2026-12-05', 9, 13)), 'takes no booking');
    }],
];

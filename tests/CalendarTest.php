<?php

declare(strict_types=1);

use Sharaspace\Home\Calendar;
use Sharaspace\Home\Space;
use Sharaspace\Home\Term;
use Sharaspace\Tests\Assert;

function ssp_floor(): Calendar
{
    return new Calendar(new Space('ss-1', 'Rua das Flores 2nd floor', 'Porto', 'office', '24000'));
}

return [
    ['a lease from a free day signs', static function (): void {
        $calendar = ssp_floor();
        $lease = $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        Assert::same('signed', $lease->state());
        Assert::same('2026-12-21', $lease->endsOn());
        Assert::same(1, $calendar->taken());
    }],
    ['a lease keeps the price the space carried', static function (): void {
        $calendar = ssp_floor();
        $lease = $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        Assert::same(240000, $lease->priceCents());
        Assert::same(240000, $calendar->billing());
    }],
    ['a term that crosses a lease is refused', static function (): void {
        $calendar = ssp_floor();
        $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        Assert::throws(static fn () => $calendar->let('ls-2', 'Bright Field', new Term(Term::DAY, 3), '2026-12-18'), 'crosses a lease');
        Assert::same(1, $calendar->taken());
    }],
    ['a term that starts the day the last one ends is free', static function (): void {
        $calendar = ssp_floor();
        $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        Assert::same(true, $calendar->free(new Term(Term::WEEK, 1), '2026-12-21'));
        Assert::same(false, $calendar->free(new Term(Term::WEEK, 1), '2026-12-20'));
    }],
    ['a short lease can sit before a long one', static function (): void {
        $calendar = ssp_floor();
        $calendar->let('ls-2', 'Bright Field', new Term(Term::MONTH, 3), '2026-12-21');
        $lease = $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        Assert::same('2026-12-21', $lease->endsOn());
        Assert::same(2, $calendar->taken());
        Assert::same(1680000, $calendar->billing());
    }],
    ['an ended lease frees its days', static function (): void {
        $calendar = ssp_floor();
        $lease = $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        $lease->end();
        Assert::same(true, $calendar->free(new Term(Term::DAY, 1), '2026-12-10'));
        Assert::same(0, $calendar->taken());
        Assert::same(0, $calendar->billing());
    }],
    ['an ended lease stays ended', static function (): void {
        $lease = ssp_floor()->let('ls-1', 'Mara Lind', new Term(Term::DAY, 1), '2026-12-07');
        $lease->end();
        Assert::throws(static fn () => $lease->end(), 'stays ended');
    }],
    ['a closed space takes no lease', static function (): void {
        $calendar = ssp_floor();
        $calendar->space->close();
        Assert::throws(static fn () => $calendar->let('ls-1', 'Mara Lind', new Term(Term::DAY, 1), '2026-12-07'), 'takes no lease');
    }],
    ['the calendar names the day the space opens again', static function (): void {
        $calendar = ssp_floor();
        Assert::same(null, $calendar->nextOpening());
        $calendar->let('ls-1', 'Mara Lind', new Term(Term::WEEK, 2), '2026-12-07');
        $calendar->let('ls-2', 'Bright Field', new Term(Term::MONTH, 1), '2026-12-21');
        Assert::same('2027-01-20', $calendar->nextOpening());
    }],
];

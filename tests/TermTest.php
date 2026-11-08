<?php

declare(strict_types=1);

use Sharaspace\Home\Term;
use Sharaspace\Tests\Assert;

return [
    ['a term counts its days', static function (): void {
        Assert::same(1, (new Term(Term::DAY, 1))->days());
        Assert::same(21, (new Term(Term::WEEK, 3))->days());
        Assert::same(90, (new Term(Term::MONTH, 3))->days());
    }],
    ['a term names itself in the plural', static function (): void {
        Assert::same('1 week', (new Term(Term::WEEK, 1))->label());
        Assert::same('3 weeks', (new Term(Term::WEEK, 3))->label());
        Assert::same('12 months', (new Term(Term::MONTH, 12))->label());
    }],
    ['an unknown unit is refused', static function (): void {
        Assert::throws(static fn () => new Term('year', 1), 'unknown unit: year');
    }],
    ['a term runs at least one unit', static function (): void {
        Assert::throws(static fn () => new Term(Term::DAY, 0), 'at least one unit');
    }],
    ['a daily term stops at ninety days', static function (): void {
        Assert::throws(static fn () => new Term(Term::DAY, 91), 'ninety days');
    }],
    ['a weekly term stops at twenty six weeks', static function (): void {
        Assert::throws(static fn () => new Term(Term::WEEK, 27), 'twenty six weeks');
    }],
    ['a monthly term stops at twenty four months', static function (): void {
        Assert::throws(static fn () => new Term(Term::MONTH, 25), 'twenty four months');
    }],
];

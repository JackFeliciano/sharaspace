<?php

declare(strict_types=1);

use Sharaspace\Home\Space;
use Sharaspace\Home\Term;
use Sharaspace\Tests\Assert;

return [
    ['a space carries a known kind', static function (): void {
        Assert::throws(static fn () => new Space('s1', 'Bay', 'Porto', 'garden', '9000'), 'unknown kind: garden');
    }],
    ['a space carries a day rate above zero', static function (): void {
        Assert::throws(static fn () => new Space('s1', 'Bay', 'Porto', 'office', '0'), 'day rate above zero');
    }],
    ['a day costs one day rate', static function (): void {
        $space = new Space('s1', 'Bay', 'Porto', 'office', '24000');
        Assert::same(24000, $space->rateFor(new Term(Term::DAY, 1)));
        Assert::same(72000, $space->rateFor(new Term(Term::DAY, 3)));
    }],
    ['a week costs five day rates', static function (): void {
        $space = new Space('s1', 'Bay', 'Porto', 'office', '24000');
        Assert::same(120000, $space->rateFor(new Term(Term::WEEK, 1)));
        Assert::same(240000, $space->rateFor(new Term(Term::WEEK, 2)));
    }],
    ['a month costs twenty day rates', static function (): void {
        $space = new Space('s1', 'Bay', 'Porto', 'office', '24000');
        Assert::same(480000, $space->rateFor(new Term(Term::MONTH, 1)));
        Assert::same(960000, $space->rateFor(new Term(Term::MONTH, 2)));
    }],
    ['a closed space says so', static function (): void {
        $space = new Space('s1', 'Bay', 'Porto', 'office', '24000');
        Assert::same(true, $space->isOpen());
        $space->close();
        Assert::same(false, $space->isOpen());
    }],
];

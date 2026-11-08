<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * A term is how long a tenant takes a space: a number of days, of weeks or of
 * months. The space it runs from is the first day of the booking.
 */
final class Term
{
    public const DAY = 'day';
    public const WEEK = 'week';
    public const MONTH = 'month';

    public function __construct(
        public readonly string $unit,
        public readonly int $count,
    ) {
        if (!in_array($unit, [self::DAY, self::WEEK, self::MONTH], true)) {
            throw new \RuntimeException('unknown unit: ' . $unit);
        }
        if ($count < 1) {
            throw new \RuntimeException('a term runs at least one unit');
        }
        if ($unit === self::DAY && $count > 90) {
            throw new \RuntimeException('a daily term runs no longer than ninety days');
        }
        if ($unit === self::WEEK && $count > 26) {
            throw new \RuntimeException('a weekly term runs no longer than twenty six weeks');
        }
        if ($unit === self::MONTH && $count > 24) {
            throw new \RuntimeException('a monthly term runs no longer than twenty four months');
        }
    }

    public function days(): int
    {
        return match ($this->unit) {
            self::DAY => $this->count,
            self::WEEK => $this->count * 7,
            self::MONTH => $this->count * 30,
        };
    }

    public function label(): string
    {
        return $this->count . ' ' . $this->unit . ($this->count === 1 ? '' : 's');
    }
}

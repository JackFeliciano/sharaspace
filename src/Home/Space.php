<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * A space is an idle professional or industrial floor a host lends out: an office
 * floor, a restaurant room, a workshop, a warehouse bay. It holds the things a
 * tenant asks before signing, and the price it carries per letting unit.
 */
final class Space
{
    public const KINDS = ['office', 'restaurant', 'workshop', 'warehouse', 'studio'];

    /** @param list<string> $features */
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly string $town,
        public readonly string $kind,
        public readonly string $dayRateCents,
        private array $features = [],
        private bool $open = true,
    ) {
        if (!in_array($kind, self::KINDS, true)) {
            throw new \RuntimeException('unknown kind: ' . $kind);
        }
        if ((int) $dayRateCents < 1) {
            throw new \RuntimeException('a space carries a day rate above zero');
        }
    }

    /** @return list<string> */
    public function features(): array
    {
        return $this->features;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    /** A whole week costs five day rates, and a whole month twenty. */
    public function rateFor(Term $term): int
    {
        $days = match ($term->unit) {
            Term::DAY => 1,
            Term::WEEK => 5,
            Term::MONTH => 20,
        };

        return ((int) $this->dayRateCents) * $days * $term->count;
    }
}

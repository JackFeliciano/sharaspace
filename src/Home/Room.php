<?php

declare(strict_types=1);

namespace Sharaspace\Home;

/**
 * A room is a space a host lends out. It holds a name, a town, the number it
 * seats and the things it offers, so a booker can tell one room from another.
 */
final class Room
{
    /** @param list<string> $features */
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly string $town,
        public readonly int $seats,
        public readonly string $dayRateCents,
        private array $features = [],
        private bool $open = true,
    ) {
        if ($seats < 1) {
            throw new \RuntimeException('a room seats at least one person');
        }
        if ((int) $dayRateCents < 1) {
            throw new \RuntimeException('a room carries a day rate above zero');
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

    public function rateFor(int $hours): int
    {
        if ($hours < 1) {
            throw new \RuntimeException('a booking runs at least one hour');
        }
        if ($hours > 12) {
            throw new \RuntimeException('a booking runs no longer than twelve hours in a day');
        }

        return (int) round(((int) $this->dayRateCents) * $hours / 12);
    }
}

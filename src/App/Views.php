<?php

declare(strict_types=1);

namespace Sharaspace\App;

use Sharaspace\Home\Diary;
use Sharaspace\Home\Sample;

/**
 * The HTML. Every string that reaches the page goes through h(), so a room name
 * with an ampersand in it cannot break the markup.
 */
final class Views
{
    public function __construct(private readonly string $base = '/')
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', ',');
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);
        $t = $this->h($title);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$t}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function page(Sample $sample): string
    {
        $TITLE = 'SharaSpace, rooms booked by the hour';
        $LEDE = 'SharaSpace lets people book rooms by the hour, with hosts listing spaces and bookers choosing available time slots.';
        $WHAT = 'A host lists a room with its name, town, seat count, day rate and facilities. A booker chooses a day and sets a start and end hour on a 24-hour clock. The diary checks for overlapping bookings on the same day and refuses a clash. Each booking keeps the room\'s rate at the time of booking. A held booking can confirm or be cancelled. Cancellation frees the slot. A closed room takes no bookings.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Rooms: ' . $sample->count() . '. Bookings held: ' . $sample->taken()
            . '. Takings on the day rate: ' . $this->money($sample->takings()) . '.</p>' . "\n";
        $out .= '<div class="rooms">' . "\n";
        foreach ($sample->diaries() as $diary) {
            $out .= $this->card($diary);
        }
        $out .= '</div>' . "\n";
        $out .= '<h2>How a booking works</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Host lists the room', 'The host adds the room details, day rate and facilities.'],
            ['Booker picks a slot', 'The booker chooses a day, start hour and end hour.'],
            ['Diary checks the hours', 'The diary refuses a booking if another booking overlaps on the same day.'],
            ['Booking holds the rate', 'The booking keeps the room\'s rate from the time the booker made it.'],
            ['Payment and release', 'A confirmed booking keeps its slot, while a cancelled booking frees it.'],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function card(Diary $diary): string
    {
        $room = $diary->room;
        $out = '<article class="room">' . "\n";
        $out .= '<h2>' . $this->h($room->name) . '</h2>' . "\n";
        $out .= '<p class="where">' . $this->h($room->town) . ', seats ' . $room->seats . '. Day rate ' . $this->money((int) $room->dayRateCents) . '.</p>' . "\n";
        $out .= '<p class="host">Offers: ' . $this->h(implode(', ', $room->features())) . '. '
            . ($room->isOpen() ? 'Open for bookings.' : 'Closed, so it takes no booking.') . '</p>' . "\n";
        $bookings = $diary->bookings();
        if ($bookings === []) {
            $out .= '<p class="wait">No booking sits in the diary.</p>' . "\n";
        } else {
            $out .= '<table class="diary"><thead><tr><th>Slot</th><th>Booker</th><th>State</th><th>Cost</th></tr></thead><tbody>' . "\n";
            foreach ($bookings as $booking) {
                $out .= '<tr><td>' . $this->h($booking->slot->label()) . '</td><td>' . $this->h($booking->booker)
                    . '</td><td>' . $this->h($booking->state()) . '</td><td>' . $this->money($booking->cents()) . '</td></tr>' . "\n";
            }
            $out .= '</tbody></table>' . "\n";
        }
        $out .= '<p class="wait">Bookings held: ' . $diary->taken() . '. Takings: ' . $this->money($diary->takings()) . '.</p>' . "\n";
        $out .= '</article>' . "\n";

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That page does not exist here.</p>\n" . $this->footer();
    }
}

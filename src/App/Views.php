<?php

declare(strict_types=1);

namespace Sharaspace\App;

use Sharaspace\Home\Calendar;
use Sharaspace\Home\Sample;

/**
 * The HTML. Every string that reaches the page goes through h(), so a space name
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
        $TITLE = 'SharaSpace, a booking engine for idle space';
        $LEDE = 'SharaSpace lets hosts rent out shared offices, restaurants and other professional or industrial spaces by the day, week or month.';
        $WHAT = 'SharaSpace is a booking engine for hosts who want to rent out idle spaces and tenants who need them for a defined period. Hosts list offices, restaurants, workshops, warehouses or studios with their features and day rates. Tenants choose their dates and rental terms, while the calendar prevents overlapping leases.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Spaces: ' . $sample->count() . '. Leases signed: ' . $sample->taken()
            . '. Billing under lease: ' . $this->money($sample->billing()) . '.</p>' . "\n";
        $out .= '<div class="spaces">' . "\n";
        foreach ($sample->calendars() as $calendar) {
            $out .= $this->card($calendar);
        }
        $out .= '</div>' . "\n";
        $out .= '<h2>What a host and a tenant do</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ["List the space", "List a space with its type, town, features and daily rate, then choose whether to offer it by the day, week or month."],
            ["Choose the dates", "Select an available space, choose the starting date and set the length of the rental term."],
            ["Sign the lease", "Sign the lease at the displayed price, which remains fixed for the agreed term even if the host later changes the daily rate."],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function card(Calendar $calendar): string
    {
        $space = $calendar->space;
        $out = '<article class="space">' . "\n";
        $out .= '<h2>' . $this->h($space->name) . '</h2>' . "\n";
        $out .= '<p class="where">' . $this->h(ucfirst($space->kind)) . ' in ' . $this->h($space->town)
            . '. Day rate ' . $this->money((int) $space->dayRateCents) . '.</p>' . "\n";
        $out .= '<p class="host">Offers: ' . $this->h(implode(', ', $space->features())) . '. '
            . ($space->isOpen() ? 'Open for letting.' : 'Closed, so it takes no lease.') . '</p>' . "\n";
        $leases = $calendar->leases();
        if ($leases === []) {
            $out .= '<p class="wait">No lease sits on the calendar.</p>' . "\n";
        } else {
            $out .= '<table class="calendar"><thead><tr><th>Term</th><th>From</th><th>To</th><th>Tenant</th><th>State</th><th>Price</th></tr></thead><tbody>' . "\n";
            foreach ($leases as $lease) {
                $out .= '<tr><td>' . $this->h($lease->term->label()) . '</td><td>' . $this->h($lease->from)
                    . '</td><td>' . $this->h($lease->endsOn()) . '</td><td>' . $this->h($lease->tenant)
                    . '</td><td>' . $this->h($lease->state()) . '</td><td>' . $this->money($lease->priceCents()) . '</td></tr>' . "\n";
            }
            $out .= '</tbody></table>' . "\n";
        }
        $out .= '<p class="wait">Leases: ' . $calendar->taken() . '. Billing: ' . $this->money($calendar->billing())
            . '. Opens again: ' . $this->h($calendar->nextOpening() ?? 'any day') . '.</p>' . "\n";
        $out .= '</article>' . "\n";

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That page does not exist here.</p>\n" . $this->footer();
    }
}

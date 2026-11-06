# SharaSpace

## What the project is

SharaSpace is a PHP 8.3 programme for booking rooms by the hour. A host lists a room with its name, town, seat count, day rate and facilities. A booker selects a slot by choosing a day, start hour and end hour on a 24-hour clock.

The diary checks each booking against existing bookings for the same room. Two slots clash when their hours overlap on the same day. The diary refuses a second booking that clashes with an existing one.

Each booking keeps the rate that the room carried when the booker took the slot. A later change to the room's rate does not change the booking's recorded rate. A held booking can confirm or be cancelled. Cancellation frees the slot for another booking. A host can close a room, and a closed room takes no bookings.

The demo contains 3 rooms and 3 bookings. The third room is closed.

The programme uses plain PHP 8.3. It requires no Composer and no packages.

## How to run it

Run the test suite from the repository root:

```bash
php tests/run.php
```


Start the demo with PHP's built-in server:

```bash
php -S 127.0.0.1:8103 -t showroom showroom/index.php
```


The application uses / as its default URL prefix. When a proxy passes a different prefix, set SHARASPACE_BASE to that value.

The application uses this setting to account for the URL prefix passed by the proxy.

## What the routes answer

The demo exposes 2 routes.

/ renders the 3 sample rooms, their booking diaries and the 5 steps a booking takes.
/health answers with the room count, the number of bookings held and the takings in cents.

The home route shows the sample data and the booking process. The health route provides counts and takings from the current application data.

A slot identifies a day and 2 hours. The diary checks for overlapping hours on that day before it accepts a booking.

## How the layout is arranged

The repository separates room and booking logic from application handling, presentation, tests and deployment files.

src/Home/ holds Room, Slot, Booking, Diary and Sample.
src/App/ holds the router, front controller and HTML.
showroom/ holds the entry file and stylesheet.
tests/ holds the test suite.
deploy/ holds the systemd unit and nginx location block.

The src/Home/ directory contains the room, slot and booking code. The src/App/ directory handles requests and renders the HTML. The remaining directories hold the demo entry point, checks and deployment configuration.

## The state of the suite

The repository includes a hand-written test runner with 18 checks.

Run the suite with:

```bash
php tests/run.php
```


The runner requires no package installation or external test framework. It gives the repository a direct way to check its behaviour from the command line.

## Licence

The repository uses the Mozilla Public License 2.0 (MPL 2.0). You may use, modify and distribute the code. You must publish changes to files covered by the licence.


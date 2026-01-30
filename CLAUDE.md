# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A single-page printable calendar written in PHP. Displays a full year on one page, designed for printing at any paper size (best in landscape). Originally by Adam Newbold (Neatnik), with modifications by Jason Larsen.

## Running Locally

Requires PHP. Start a local server:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000` in a browser.

## URL Parameters

- `year` - Override the year (default: current year). Example: `?year=2027`
- `startmonth` - Start the calendar on a specific month (jan-dec). Example: `?startmonth=jul` displays Jul-Jun
- `layout=aligned-weekdays` - Align weekdays vertically across months (42-row layout vs default 31-row)
- `sofshavua` - Use Friday/Saturday as weekend days instead of Saturday/Sunday

## Architecture

Single-file PHP application (`index.php`) containing:
- Embedded CSS with print-specific styles (`@media print`)
- PHP logic that generates an HTML table with 12 month columns
- Two layout modes: default (31 rows, date + day abbreviation) and aligned-weekdays (42 rows, weekday-aligned)

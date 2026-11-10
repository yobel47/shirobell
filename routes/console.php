<?php

use Illuminate\Support\Facades\Schedule;

/*
| Requires one cron entry on the server:
| * * * * * cd /path/to/apps && php artisan schedule:run >> /dev/null 2>&1
| See apps/docs/operations/scheduler-and-queue.md.
*/
Schedule::command('invitations:expire')->hourly()->withoutOverlapping();
Schedule::command('orders:expire')->hourly()->withoutOverlapping();
Schedule::command('invitations:remind')->dailyAt('09:00')->withoutOverlapping();

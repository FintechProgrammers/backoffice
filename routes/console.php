<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:release-commissions')->everyMinute();

Schedule::command('ranks:assign')->everyMinute();

Schedule::command('subscriptions:deactivate')->daily();

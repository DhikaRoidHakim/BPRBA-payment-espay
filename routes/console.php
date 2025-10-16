<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule VA Expire Check
Schedule::command('va:expire')
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/va-expire.log'));

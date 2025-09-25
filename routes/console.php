<?php

use App\Console\Commands\GenerateRecurringCharges;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Planification
 */
Schedule::command(GenerateRecurringCharges::class)
    ->monthlyOn(1, '00:00');

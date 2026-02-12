<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monthly bill generation (runs on the 1st of every month at 9:00 AM)
Schedule::command('bills:generate-monthly')
    ->monthlyOn(1, '09:00')
    ->timezone('Asia/Manila')
    ->description('Generate monthly bills for all active contracts');

// Data retention: purge archived items older than 5 years (runs daily at 2:00 AM)
Schedule::command('archive:purge-old')
    ->dailyAt('02:00')
    ->timezone('Asia/Manila')
    ->description('Permanently delete archived items older than 5 years');

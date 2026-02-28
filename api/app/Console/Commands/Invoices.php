<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Invoices extends Command
{
    protected $signature = 'invoices:run';

    protected $description = 'Cron for Booking Update';

    public function handle()
    {
        $this->info('Invoices command executed (no-op).');
    }
}

<?php

namespace App\Console\Commands;

use Helpers;
use Illuminate\Console\Command;
use App\Sync;

class Invoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoices:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron for Booking Update';

    public static $process_busy = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        sync();

    }
}

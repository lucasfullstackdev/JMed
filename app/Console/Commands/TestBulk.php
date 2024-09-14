<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestBulk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-bulk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        (new \App\Services\BulkMedicineLeafletService())->load();
    }
}

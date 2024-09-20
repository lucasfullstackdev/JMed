<?php

namespace App\Console\Commands;

use App\Jobs\DownloadPdfJob;
use App\Models\MedicineLeaflet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StoragePdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:storage-pdf';

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
        $medicineLeaflets = MedicineLeaflet::where('downloaded', false)
            ->limit(5000)
            ->get();

        $delay = 0;
        $medicineLeaflets->each(function ($medicineLeaflet) use (&$delay) {
            DownloadPdfJob::dispatch($medicineLeaflet)->delay($delay += 5)
            ->onQueue('queue_download_pdf');
        });
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\DownloadPdfJob;
use App\Models\MedicineLeaflet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DownloadPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download-pdf';

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
        
        // $medicineLeaflet = MedicineLeaflet::where('registration_number', '183260400')->first();
        // DownloadPdfJob::dispatch($medicineLeaflet)->onQueue('queue_download_pdf');

        // Get pdf in base64, decode it and save it to the storage
        // $pdf = base64_decode($medicineLeaflet->pdf);
        // $path = storage_path('app/public/medicine-leaflets/' . $medicineLeaflet->registration_number . '.pdf');
        // file_put_contents($path, $pdf);

        // Update the downloaded column
        // $medicineLeaflet->update(['downloaded' => true]);

        // $path = storage_path('app/public/medicine-leaflets/' . $medicineLeaflet->registration_number . '.pdf');
    }
}

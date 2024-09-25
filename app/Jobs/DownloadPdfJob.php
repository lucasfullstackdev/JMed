<?php

namespace App\Jobs;

use App\Models\MedicineLeaflet;
use App\Services\AnvisaService;
use App\Services\Storages\GoogleDrive;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadPdfJob implements ShouldQueue
{
    use Queueable;

    private GoogleDrive $storageService;

    /**
     * Create a new job instance.
     */
    public function __construct(public MedicineLeaflet $medicineLeaflet)
    {
        $this->storageService = new GoogleDrive();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new AnvisaService())->download($this->medicineLeaflet);
    }
}

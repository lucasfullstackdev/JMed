<?php

namespace App\Jobs;

use App\Models\MedicineLeaflet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePdfDownloaded implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public MedicineLeaflet $medicineLeaflet)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->medicineLeaflet->update(['downloaded' => true]);
    }
}

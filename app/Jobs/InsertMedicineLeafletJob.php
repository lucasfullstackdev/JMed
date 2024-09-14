<?php

namespace App\Jobs;

use App\Dtos\MedicineLeafletDto;
use App\Services\BulkMedicineLeafletService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InsertMedicineLeafletJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Collection $medicineLeaflets)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new BulkMedicineLeafletService())->insert($this->medicineLeaflets->toArray());
    }
}

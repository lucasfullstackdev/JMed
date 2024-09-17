<?php

namespace App\Jobs;

use App\Models\MedicineLeaflet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SavePdfOfTheMedicineLeafletJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $registryNumber, public string $hashMedicineLeaflet) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SavePdfOfTheMedicineLeafletJob.....');
        MedicineLeaflet::where('registration_number', $this->registryNumber)
            ->update(['pdf' => $this->hashMedicineLeaflet,]);

        Log::info('PDF salvo com sucesso');
    }
}

<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GetPdfOfTheMedicineLeafletJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $registryNumber, public string $hashMedicineLeaflet)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $delay = (new \App\Services\AnvisaService())->getPdf($this->registryNumber, $this->hashMedicineLeaflet);

        Log::info('Atrasando a execução do Job por ' . $delay . ' segundos');
        if (!empty($delay)) {
            $this->release($delay);
        }
    }
}

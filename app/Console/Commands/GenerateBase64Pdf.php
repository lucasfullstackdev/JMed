<?php

namespace App\Console\Commands;

use App\Jobs\GenerateTokenJob;
use App\Models\MedicineLeaflet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateBase64Pdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-base64-pdf';

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
        $medicineLeaflets = MedicineLeaflet::whereNull('pdf')->limit(5000)->get();

        $delayInSeconds = 0;
        $medicineLeaflets->each(function ($medicineLeaflet) use (&$delayInSeconds) {
            Log::info('Registrando o Job para o registro: ' . $medicineLeaflet->registration_number);

            if (empty($medicineLeaflet->registration_number)) {
                return;
            }

            GenerateTokenJob::dispatch($medicineLeaflet->registration_number)
                ->delay(now()->addSeconds($delayInSeconds))
                ->onQueue('queue_token');

            $delayInSeconds += 7; // Incrementa o delay em 7 segundos para cada job
        });
    }
}

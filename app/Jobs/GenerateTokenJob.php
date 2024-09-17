<?php

namespace App\Jobs;

use App\Services\AnvisaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTokenJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $registryNumber)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new \App\Services\AnvisaService())->generateToken($this->registryNumber);
    }
}

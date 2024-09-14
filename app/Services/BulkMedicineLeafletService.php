<?php

namespace App\Services;

use App\Dtos\MedicineLeafletDto;
use App\Jobs\InsertMedicineLeafletJob;
use App\Models\MedicineLeaflet;

class BulkMedicineLeafletService extends BaseService
{
    // private string $filePath = 'app/public/medicine-leaflets-temp.json';
    private string $filePath = 'app/public/medicine-leaflets.json';
    private int $chunkSize = 1000;

    public function load()
    {
        $this->getData()
            ->chunk($this->chunkSize)
            ->each(fn($chunk) => InsertMedicineLeafletJob::dispatch($chunk));
    }

    public function insert(array $medicineLeaflets)
    {
        MedicineLeaflet::insert($medicineLeaflets);
    }

    private function getData()
    {
        $filePath = storage_path($this->filePath);

        if (!file_exists($filePath)) {
            return;
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        return collect($data)->map(function ($item) {
            return (array) new MedicineLeafletDto((object) $item);
        });
    }
}

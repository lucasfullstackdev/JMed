<?php

namespace App\Services;

use App\Dtos\MedicineLeafletDto;
use App\Jobs\InsertMedicineLeafletJob;
use App\Models\MedicineLeaflet;

class BulkMedicineLeafletService extends BaseService
{
    private string $filePath = 'app/public/medicine-leaflets-temp-categoria.json';
    private int $chunkSize = 1000;

    public function load()
    {
        $this->getData()->each(function ($category) {
            if ($category->count() > $this->chunkSize) {
                return $category->chunk($this->chunkSize)
                    ->each(fn($chunk) => InsertMedicineLeafletJob::dispatch($chunk));
            }

            InsertMedicineLeafletJob::dispatch($category);
        });
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

        $result = [];
        foreach ($data as $category) {
            $result[] = collect(
                array_map(function ($medicineLeaflet) use ($category) {
                    return (array) new MedicineLeafletDto((object) array_merge($medicineLeaflet, ['category_id' => $category['id']]));
                }, $category['medicalLeaflets'])
            );
        }

        return collect($result);
    }
}

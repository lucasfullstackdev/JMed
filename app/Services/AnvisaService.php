<?php

namespace App\Services;

use App\Console\Commands\SavePdfOfTheMedicineLeaflet;
use App\Jobs\GetPdfOfTheMedicineLeafletJob;
use App\Jobs\SavePdfOfTheMedicineLeafletJob;
use App\Jobs\UpdatePdfDownloaded;
use App\Models\MedicineLeaflet;
use App\Services\Storages\IStorage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AnvisaService extends BaseService
{
    private int $delayToUseToken = 0;

    public function generateToken(string $registryNumber)
    {
        try {
            $response = $this->client->get("https://consultas.anvisa.gov.br/api/consulta/bulario?count=10&filter%5BnumeroRegistro%5D={$registryNumber}&page=1");

            $data = $response->json();

            if (empty($data)) {
                return null;
            }

            if (isset($data['error']) && !empty($data['error'])) {
                return null;
            }

            if ($response->successful()) {
                if (!isset($data['content']) || empty($data['content'])) {
                    return null;
                }

                if (!isset($data['content'][0]) || empty($data['content'][0])) {
                    return null;
                }

                if (!isset($data['content'][0]['idBulaPacienteProtegido']) || empty($data['content'][0]['idBulaPacienteProtegido'])) {
                    return null;
                }

                $hashMedicineLeaflet = $data['content'][0]['idBulaPacienteProtegido'];

                Log::info('HashMedicineLeaflet: ' . $hashMedicineLeaflet);

                // Jogando para fila para que seja baixado o PDF após 3 minutos (pois a API da ANVISA é uma porcaria)
                GetPdfOfTheMedicineLeafletJob::dispatch($registryNumber, $hashMedicineLeaflet)
                    ->delay(now()->addMinutes($this->delayToUseToken))
                    ->onQueue('queue_pdf');
            }
        } catch (\Throwable $th) {
            // dd($th->getMessage());
        }
    }

    public function getPdf(string $registryNumber, string $hashMedicineLeaflet)
    {
        try {
            $url = 'https://consultas.anvisa.gov.br/api/consulta/medicamentos/arquivo/bula/parecer/' . $hashMedicineLeaflet . '/?Authorization=';
            $response = $this->client->get($url);
            $data = $response->getBody()->getContents();

            if (json_decode($data)) {
                $data = json_decode($data, true);
                if (isset($data['error']) && !empty($data['error'])) {
                    Log::info('Error: ' . $data['error']);
                    preg_match('/before ([0-9-]+T[0-9:]+-0300)/', $data['error'], $matchesBefore);
                    preg_match('/Current time: ([0-9-]+T[0-9:]+-0300)/', $data['error'], $matchesCurrent);

                    if (!empty($matchesBefore[1]) && !empty($matchesCurrent[1])) {
                        $before = Carbon::parse($matchesBefore[1])->format('Y-m-d H:i:s');
                        $current = Carbon::parse($matchesCurrent[1])->format('Y-m-d H:i:s');

                        // Calcular a diferença entre as datas
                        $diff = Carbon::parse($current)->diffInSeconds(Carbon::parse($before)); # 11
                        $diff = $diff <= 60 ? 60 : $diff;

                        Log::info(json_encode([
                            'before' => $before,
                            'current' => $current,
                            'diff' => $diff,
                        ]));

                        return $diff;
                    }
                }
            }

            if ($response->successful() && !empty($data)) {
                SavePdfOfTheMedicineLeafletJob::dispatch($registryNumber, base64_encode($data))->onQueue('queue_save_pdf');
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function drive(MedicineLeaflet $medicineLeaflet, IStorage $storage)
    {
        // dd($medicineLeaflet, $storage);
    }

    public function download(MedicineLeaflet $medicineLeaflet)
    {
        $path = storage_path('app/public/pdfs/' . $medicineLeaflet->registration_number . '.pdf');

        $pdf = base64_decode($medicineLeaflet->pdf);
        file_put_contents($path, $pdf);

        UpdatePdfDownloaded::dispatch($medicineLeaflet)->onQueue('queue_update_downloaded');
    }
}

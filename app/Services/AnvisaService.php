<?php

namespace App\Services;

use App\Console\Commands\SavePdfOfTheMedicineLeaflet;
use App\Jobs\GetPdfOfTheMedicineLeafletJob;
use App\Jobs\SavePdfOfTheMedicineLeafletJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AnvisaService extends BaseService
{
    // public function getMedicines()
    // {
    //     $response = $this->client->get('https://consultas.anvisa.gov.br/api/consulta/medicamento');
    //     return $response->getBody()->getContents();
    // }

    // private int $delayToUseToken = 5;
    private int $delayToUseToken = 0;

    public function generateToken(string $registryNumber)
    {
        # curl 'https://consultas.anvisa.gov.br/api/consulta/bulario?count=10&filter%5BnumeroRegistro%5D=102350700&page=1' \
        # -H 'Accept: application/json, text/plain, */*' \
        # -H 'Accept-Language: en-US,en;q=0.9,pt;q=0.8' \
        # -H 'Authorization: Guest' \
        # -H 'Cache-Control: no-cache' \
        # -H 'Connection: keep-alive' \
        # -H 'Cookie: FGTServer=77E1DC77AE2F953D7ED796A08A630A01A53CF6FE5FD0E106412591871F9A9BBCFBDEA0AD564FD89D3BDE827B200B; FGTServer=77E1DC77AE2F953D7ED796A08A630A01A53CF6FE5FD0E106412591871F9A9BBCFBDEA0AD564FD89D3BDE8278200B; _pk_id.42.210e=cf48da0b9b89542c.1725665068.; _ga=GA1.3.1385345585.1725669018; _ga_VPYWRPYSDM=GS1.3.1725669018.1.0.1725669018.0.0.0; _cfuvid=OuwBz0PO17hddS9ygTqSXW5mu1gIMVAZQvjyFRvAn1U-1726330270011-0.0.1.1-604800000; dtCookie=v_4_srv_-2D85_sn_0O452CKC0A7EI2PSQS6EPOGAPBB9JJO7; rxVisitor=1726330922852G2TSLJUPERVOSSPSKGLDRDOI4LQGP7N9; dtLatC=1; rxvt=1726333152785|1726330922854; dtPC=-85$131344790_681h-vVAEKLPLVORNLHOCSFDTGOCDBASGKAPMI-0e0; dtSa=true%7CC%7C-1%7CConsultas%7C-%7C1726331371409%7C131344790_681%7Chttps%3A%2F%2Fconsultas.anvisa.gov.br%2F%7C%7C%7C%2F%7C; FGTServer=77E1DC77AE2F953D7ED796A08A630A01A53CF6FE5FD0E106412591871F9A9BBCFBDEA0AD564FD89D3BDE8278200B; _pk_ref.42.210e=%5B%22%22%2C%22%22%2C1726351251%2C%22https%3A%2F%2Fwww.google.com%2F%22%5D; _pk_ses.42.210e=1' \
        # -H 'If-Modified-Since: Mon, 26 Jul 1997 05:00:00 GMT' \
        # -H 'Pragma: no-cache' \
        # -H 'Referer: https://consultas.anvisa.gov.br/' \
        # -H 'Sec-Fetch-Dest: empty' \
        # -H 'Sec-Fetch-Mode: cors' \
        # -H 'Sec-Fetch-Site: same-origin' \
        # -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36' \
        # -H 'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"' \
        # -H 'sec-ch-ua-mobile: ?0' \
        # -H 'sec-ch-ua-platform: "Windows"'

        try {
            $response = $this->client->get("https://consultas.anvisa.gov.br/api/consulta/bulario?count=10&filter%5BnumeroRegistro%5D={$registryNumber}&page=1");

            $data = $response->json();

            if (empty($data)) {
                // echo "Entrou aqui - 0 \n";
                return null;
            }

            if (isset($data['error']) && !empty($data['error'])) {
                // echo "Entrou aqui - 18 \n";
                return null;
            }

            if ($response->successful()) {
                // echo "Entrou aqui \n";
                if (!isset($data['content']) || empty($data['content'])) {
                    return null;
                }

                if (!isset($data['content'][0]) || empty($data['content'][0])) {
                    return null;
                }

                if (!isset($data['content'][0]['idBulaPacienteProtegido']) || empty($data['content'][0]['idBulaPacienteProtegido'])) {
                    return null;
                }

                // echo "Entrou aqui - 1 \n";

                $hashMedicineLeaflet = $data['content'][0]['idBulaPacienteProtegido'];

                Log::info('HashMedicineLeaflet: ' . $hashMedicineLeaflet);

                // Jogando para fila para que seja baixado o PDF após 3 minutos (pois a API da ANVISA é uma porcaria)
                GetPdfOfTheMedicineLeafletJob::dispatch($registryNumber, $hashMedicineLeaflet)
                    ->delay(now()->addMinutes($this->delayToUseToken))
                    ->onQueue('queue_pdf');

                // echo "Entrou aqui - 2 \n";
            }

            // dd($data, $response->successful(), $pdf ?? null);
            // dd('Erro ao buscar medicamento por número do registro');
        } catch (\Throwable $th) {
            dd($th->getMessage());
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
}

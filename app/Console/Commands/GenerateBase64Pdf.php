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
        /**
         * Regras:
         * - Pegar todos os registros da tabela medicine_leaflets
         *  * que não tenham o campo pdf preenchido
         * - Para cada registro, jogar um Job na fila para baixar o PDF (todos os processos)
         * 
         * Para baixar o PDF(bula), será preciso seguir as etapas:
         * - Fazer uma requisição HTTP (buscar medicamento por  número do registro) para a API da ANVISA
         *  * Assim o token é atualizado
         * - Depois fazer uma requisição HTTP para pegar o PDF
         * - Depois salvar o PDF no banco de dados
         * 
         * 
         */

        ##### Buscar por número do registro #####
        #  curl 'https://consultas.anvisa.gov.br/api/consulta/bulario?count=10&filter%5BnumeroRegistro%5D=117170015&page=1' \
        # -H 'Accept: application/json, text/plain, */*' \
        # -H 'Accept-Language: en-US,en;q=0.9,pt;q=0.8' \
        # -H 'Authorization: Guest' \
        # -H 'Cache-Control: no-cache' \
        # -H 'Connection: keep-alive' \
        # -H 'Cookie: FGTServer=77E1DC77AE2F953D7ED796A08A630A01A53CF6FE5FD0E106412591871F9A9BBCFBDEA0AD564FD89D3BDE8278200B; FGTServer=77E1DC77AE2F953D7ED796A08A630A01A53CF6FE5FD0E106412591871F9A9BBCFBDEA0AD564FD89D3BDE8278200B; _pk_id.42.210e=cf48da0b9b89542c.1725665068.; _ga=GA1.3.1385345585.1725669018; _ga_VPYWRPYSDM=GS1.3.1725669018.1.0.1725669018.0.0.0; _cfuvid=OuwBz0PO17hddS9ygTqSXW5mu1gIMVAZQvjyFRvAn1U-1726330270011-0.0.1.1-604800000; dtCookie=v_4_srv_-2D85_sn_0O452CKC0A7EI2PSQS6EPOGAPBB9JJO7; rxVisitor=1726330922852G2TSLJUPERVOSSPSKGLDRDOI4LQGP7N9; dtLatC=1; rxvt=1726333152785|1726330922854; dtPC=-85$131344790_681h-vVAEKLPLVORNLHOCSFDTGOCDBASGKAPMI-0e0; dtSa=true%7CC%7C-1%7CConsultas%7C-%7C1726331371409%7C131344790_681%7Chttps%3A%2F%2Fconsultas.anvisa.gov.br%2F%7C%7C%7C%2F%7C; FGTServer=77E1DC77AE2F953D7ED796A08A630A01A53CF6FE5FD0E106412591871F9A9BBCFBDEA0AD564FD89D3BDE8278200B; _pk_ref.42.210e=%5B%22%22%2C%22%22%2C1726342977%2C%22https%3A%2F%2Fwww.google.com%2F%22%5D; _pk_ses.42.210e=1' \
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


        ##### Pegar o PDF #####
        ## https://consultas.anvisa.gov.br/api/consulta/medicamentos/arquivo/bula/parecer/eyJhbGciOiJIUzUxMiJ9.eyJqdGkiOiIxOTkwNDMzOSIsIm5iZiI6MTcyNjM0Mzg1MCwiZXhwIjoxNzI2MzQ0MTUwfQ.beHiyAW1lKwKJa8V1EDAHz5VY9_5ne2L6pZOHnlTYQTip3a2bEk8LrhJbLLZIkCgaxGIu-0ZKX85o0Kmc_3DGw/?Authorization=

        // $url = 'https://consultas.anvisa.gov.br/api/consulta/medicamentos/arquivo/bula/parecer/eyJhbGciOiJIUzUxMiJ9.eyJqdGkiOiIxOTkwNDMzOSIsIm5iZiI6MTcyNjM0Mzg1MCwiZXhwIjoxNzI2MzQ0MTUwfQ.beHiyAW1lKwKJa8V1EDAHz5VY9_5ne2L6pZOHnlTYQTip3a2bEk8LrhJbLLZIkCgaxGIu-0ZKX85o0Kmc_3DGw/?Authorization=';

        // // Make a http request to this url. Use Guzzle
        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('GET', $url, [
        //     'headers' => []
        // ]);

        // $pdf = $response->getBody()->getContents();

        // // Save the pdf to the database (base64 encoded)
        // $medicineLeaflet = MedicineLeaflet::find(517);
        // $medicineLeaflet->pdf = base64_encode($pdf);
        // $medicineLeaflet->save();


        // Get the pdf from the database and save it to a file using laravel storage
        // $pdf = base64_decode($medicineLeaflet->pdf);
        // $path = storage_path('app/public/medicine-leaflets/' . $medicineLeaflet->id . '.pdf');
        // file_put_contents($path, $pdf);

        // GenerateTokenJob::dispatch(102350700)->onQueue('queue_token');

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

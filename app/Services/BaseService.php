<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

abstract class BaseService
{
    // protected Client $client;
    protected $client;
    private int $timeout = 60;

    public function __construct()
    {
        // $this->client = new Client([
        //     'headers' => [
        //         'Accept' => 'application/json, text/plain, */*',
        //         'Accept-Language' => 'en-US,en;q=0.9,pt;q=0.8',
        //         'Authorization' => 'Guest',
        //         'Cache-Control' => 'no-cache',
        //         'Connection' => 'keep-alive',
        //     ]
        // ]);

        $this->client = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'en-US,en;q=0.9,pt;q=0.8',
            'Authorization' => 'Guest',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ])->timeout($this->timeout);

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

    }
}

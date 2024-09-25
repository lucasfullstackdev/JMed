<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateMedicineLeaflets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-duplicate-medicine-leaflets';

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
        DB::statement("
        DELETE ml1 
        FROM medicine_leaflets ml1
        JOIN medicine_leaflets ml2 
        ON ml1.product_id = ml2.product_id 
        AND ml1.id > ml2.id
    ");
    }
}

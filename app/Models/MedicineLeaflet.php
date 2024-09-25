<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineLeaflet extends Model
{
    use HasFactory;

    protected $table = 'medicine_leaflets';
    protected $fillable = [
        'product_id',
        'category_id',
        'registration_number',
        'product_name',
        'expedient',
        'company_name',
        'company_document',
        'transaction_number',
        'date',
        'process_number',
        'updated_at',
        'pdf',
        'downloaded',
    ];
}

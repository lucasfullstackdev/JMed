<?php

namespace App\Dtos;

use Carbon\Carbon;

class MedicineLeafletDto
{
    public ?string $product_id;
    public ?string $category_id;
    public ?string $registration_number;
    public ?string $product_name;
    public ?string $expedient;
    public ?string $company_name;
    public ?string $company_document;
    public ?string $transaction_number;
    public ?string $date;
    public ?string $process_number;
    public ?string $updated_at;

    public function __construct(object $medicineLeafletData)
    {
        $this->product_id = $medicineLeafletData->idProduto ?? null;
        $this->category_id = $medicineLeafletData->category_id ?? null;
        $this->registration_number = $medicineLeafletData->numeroRegistro ?? null;
        $this->product_name = $medicineLeafletData->nomeProduto ?? null;
        $this->expedient = $medicineLeafletData->expediente ?? null;
        $this->company_name = $medicineLeafletData->razaoSocial ?? null;
        $this->company_document = $medicineLeafletData->cnpj ?? null;
        $this->transaction_number = $medicineLeafletData->numeroTransacao ?? null;
        $this->date = $medicineLeafletData->data ? Carbon::createFromDate($medicineLeafletData->data)->toDateString() : null;
        $this->process_number = $medicineLeafletData->numProcesso ?? null;
        $this->updated_at = $medicineLeafletData->dataAtualizacao ? Carbon::createFromDate($medicineLeafletData->dataAtualizacao)->toDateTimeString() : null;
    }
}

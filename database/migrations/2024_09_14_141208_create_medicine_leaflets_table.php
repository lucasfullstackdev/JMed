<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicine_leaflets', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('product_name')->nullable();
            $table->string('expedient')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_document')->nullable();
            $table->string('transaction_number')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('process_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_leaflets');
    }
};

<?php

use App\Enums\StatusType;
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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->string('qtd');
            $table->enum('in_stock', ['yes', 'no', 'partial'])->default('yes');
            $table->enum('was_bought', ['Y', 'N'])->nullable()->default('N');
            $table->string('supplier')->nullable();
            $table->string('link')->nullable();
            $table->string('obs')->nullable();
            $table->date('arrival_date')->nullable();
            $table->dateTime('purchase_date')->nullable();
            $table->dateTime('delivered_date')->nullable();
            $table->enum('arrived', ['Y', 'N', 'P'])->nullable()->default(null);
            $table->enum('status', StatusType::getValues())->default(StatusType::WaitingApproval);
            $table->string('preview')->nullable();
            $table->string('design_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};

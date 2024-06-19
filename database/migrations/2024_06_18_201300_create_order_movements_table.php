<?php

use App\Enums\MovementType;
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
        Schema::create('order_movements', function (Blueprint $table) {
            $table->id();
            $table->dateTime('action_date')->nullable();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('responsable_id')->nullable()->constrained('users');
            $table->dateTime('accepted_date')->nullable();
            $table->enum('movement_type', MovementType::getValues())->default(MovementType::Created);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_movements');
    }
};

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
            $table->char('action_type', 1)->nullable()->comment('R - registro, C - ciencia');
            $table->foreignId('action_user_id')->nullable()->constrained('users');
            $table->foreignId('order_id')->constrained();
            $table->char('origin', 1)->nullable()->comment('P - purchase, O - order');
            $table->enum('movement_type', MovementType::getValues())->nullable()->default(null);
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

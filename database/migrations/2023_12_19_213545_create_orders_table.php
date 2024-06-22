<?php

use App\Enums\MovementType;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('employee_id')->nullable()->constrained('users');
            $table->foreignId('designer_id')->nullable()->constrained('users');
            $table->date('date');
            $table->string('number');
            $table->enum('step', MovementType::getValues())->default(MovementType::Created);
            $table->enum('status', StatusType::getValues())->default(StatusType::WaitingApproval);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

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
        Schema::create('item_distributions', function (Blueprint $table) {
            $table->id();

            // $table->foreignId('staff_id')->nullable()->constrained('staff');
            // $table->foreignId('ddd_id')->nullable()->constrained('ddds');
            // $table->string('office_no')->nullable();
            $table->foreignId('distribution_item_id')->constrained('distribution_items');
            $table->longText('remark')->nullable();
            $table->timestamp('time')->useCurrent();

            $table->foreignId('authorize_staff_id')->constrained('staff');

            $table->string('status')->default('Pending');
            $table->foreignId('alter_staff_id')->nullable()->constrained('staff');
            $table->timestamp('valid_from')->useCurrent();
            $table->timestamp('valid_until')->nullable();

            $table->unsignedBigInteger('distributionable_id');
            $table->string('distributionable_type');

            $table->timestamps();

            $table->index(['distributionable_id', 'distributionable_type'], 'distributionable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_distributions');
    }
};

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
        Schema::create('helpdesk_supports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('staff_id')->constrained('staff');
            $table->longText('remark')->nullable();
            $table->timestamp('time')->useCurrent();

            $table->foreignId('helpdesk_request_id')->nullable()->constrained('helpdesk_requests');

            $table->string('status')->default('Pending');
            $table->foreignId('alter_staff_id')->nullable()->constrained('staff');
            $table->timestamp('valid_from')->useCurrent();
            $table->timestamp('valid_until')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_supports');
    }
};

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
        Schema::create('wastes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('harvest_id');
            $table->foreign('harvest_id')
                  ->references('id')
                  ->on('harvests')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->integer('waste_quantity')->nullable(false);
            $table->string('reason')->nullable(false);

            // Timestamps
            $table->timestamp('reported_at')->useCurrent(); // default now()
            $table->timestamp('created_at')->useCurrent(); // default now()
            $table->timestamp('updated_at')->useCurrent(); // default now()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wastes');
    }
};

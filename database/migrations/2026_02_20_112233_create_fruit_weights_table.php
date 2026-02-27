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
        Schema::create('fruit_weights', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('harvest_id');
            $table->foreign('harvest_id')
                  ->references('id')
                  ->on('harvests')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            
            $table->decimal('weight', 4, 2);            
            $table->string('status')->default('local');

              // Timestamps
            $table->timestamp('created_at')->useCurrent(); // default now()
            $table->timestamp('updated_at')->useCurrent(); // default now()

            // Indexes
            $table->index('harvest_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fruit_weights');
    }
};

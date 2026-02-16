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
        Schema::create('harvests', function (Blueprint $table) {
            // Primary key - string UUID
            $table->string('id')->primary();
            
            // Foreign key to fruits table
            $table->string('fruit_id');
            $table->foreign('fruit_id')
                  ->references('id')
                  ->on('fruits')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            // Other columns
            $table->integer('ripe_quantity')->nullable(false);
            $table->date('harvest_date')->nullable(false);
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent(); // default now()
            $table->timestamp('updated_at')->useCurrent(); // timestamp without timezone
            
            
            // Indexes
            $table->index('fruit_id');
            $table->index('harvest_date');
            $table->index(['fruit_id', 'harvest_date']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
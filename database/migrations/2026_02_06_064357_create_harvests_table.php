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
            $table->string('fruit_id')->nullable();  // Fixed: -> not =>, and no (true)
            $table->foreign('fruit_id')
                  ->references('id')
                  ->on('fruits')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
                  
            // Foreign key to users table (assignee/harvester)
            $table->string('user_id')->nullable();   // Added nullable() so it can be null during creation
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            // Other columns
            $table->integer('ripe_quantity')->nullable();  // Fixed: removed (true)
            $table->date('harvest_at')->nullable();        // Fixed: removed (true)
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent(); 
            $table->timestamp('updated_at')->useCurrent(); 
            
            // Indexes
            $table->index('fruit_id');
            $table->index('user_id');           // Added index for user_id
            $table->index('harvest_at');
            $table->index(['fruit_id', 'harvest_at']);
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
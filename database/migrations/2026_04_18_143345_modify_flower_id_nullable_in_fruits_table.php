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
        Schema::table('fruits', function (Blueprint $table) {
            // Step 1: Drop the foreign key constraint
            $table->dropForeign(['flower_id']);
            
            // Step 2: Make the column nullable
            $table->string('flower_id')->nullable()->change();
            
            // Step 3: Re-add foreign key with ON DELETE SET NULL
            $table->foreign('flower_id')
                  ->references('id')
                  ->on('flowers')
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // Changed from cascade to set null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fruits', function (Blueprint $table) {
            // Drop the modified foreign key
            $table->dropForeign(['flower_id']);
            
            // Revert to not nullable
            $table->string('flower_id')->nullable(false)->change();
            
            // Restore original foreign key with cascade delete
            $table->foreign('flower_id')
                  ->references('id')
                  ->on('flowers')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }
};
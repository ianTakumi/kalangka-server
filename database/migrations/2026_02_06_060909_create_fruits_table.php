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
        Schema::create('fruits', function (Blueprint $table) {
            $table->string('id')->primary();
            
            // Foreign keys
            $table->string('flower_id');
            $table->foreign('flower_id')
                  ->references('id')
                  ->on('flowers')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            $table->string('tree_id');
            $table->foreign('tree_id')
                  ->references('id')
                  ->on('trees')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            // Other columns
            $table->integer('quantity')->nullable(false);
            $table->timestamp('wrappted_at')->nullable(); // Note: double 'p' in wrappted_at
            $table->text('image_url')->nullable(false);
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent(); // default now()
            $table->timestamp('updated_at')->useCurrent(); // default now()
       
            
            // Indexes for better performance
            $table->index('flower_id');
            $table->index('tree_id');
            $table->index(['tree_id', 'flower_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fruits');
    }
};
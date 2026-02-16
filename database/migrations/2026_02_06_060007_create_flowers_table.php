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
        Schema::create('flowers', function (Blueprint $table) {
            // Primary key - string UUID from React Native
            $table->string('id')->primary();
            
            // Foreign key to trees table
            $table->string('tree_id');
            $table->foreign('tree_id')
                  ->references('id')
                  ->on('trees')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // Permanent delete
            
            // Other columns
            $table->integer('quantity')->nullable(false);
            $table->timestamp('wrapped_at')->nullable();
            $table->text('image_url')->nullable(false);
            
            // created_at and updated_at (timestamp with timezone in Laravel)
            $table->timestamps();
            
            // No soft deletes - permanent delete talaga
            // Walang deleted_at column
            
            // Indexes for better performance
            $table->index('tree_id');
            $table->index('created_at');
            $table->index(['tree_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flowers');
    }
};
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
            $table->string('id')->primary();
            $table->string('tree_id');
            $table->foreign('tree_id')
                  ->references('id')
                  ->on('trees')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); 
            $table->string('user_id');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); 
            $table->integer('quantity')->nullable(false);
            $table->timestamp('wrapped_at')->nullable();
            $table->text('image_url')->nullable(false);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('tree_id');
            $table->index('user_id');
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
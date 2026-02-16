<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trees', function (Blueprint $table) {
            // ID comes from React Native as string/UUID
            $table->string('id')->primary(); // Not auto-incrementing
            
            $table->text('description');
            $table->double('latitude', 10, 8);
            $table->double('longitude', 11, 8);
            $table->string('status')->default('active');
            $table->boolean('is_synced')->default(true); // True since coming from mobile
            $table->string('type');
            $table->text('image_url');
            $table->timestamps();
            
            
            // Indexes
            $table->index('status');
            $table->index('type');
            $table->index(['latitude', 'longitude']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
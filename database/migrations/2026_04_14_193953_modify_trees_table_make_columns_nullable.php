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
        Schema::table('trees', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->double('latitude', 10, 8)->nullable()->change();
            $table->double('longitude', 11, 8)->nullable()->change();
            $table->string('status')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->text('image_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
            $table->double('latitude', 10, 8)->nullable(false)->change();
            $table->double('longitude', 11, 8)->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->text('image_url')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excel_export_logs', function (Blueprint $table) {
            $table->id();
            $table->string('export_type', 50);
            $table->string('filename', 255);
            $table->unsignedInteger('row_count')->default(0);
            $table->json('filter_summary')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excel_export_logs');
    }
};

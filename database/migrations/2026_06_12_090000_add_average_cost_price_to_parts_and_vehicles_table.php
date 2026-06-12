<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->decimal('average_cost_price', 12, 4)->default(0)->after('last_cost_price');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('average_cost_price', 12, 4)->default(0)->after('last_cost_price');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn('average_cost_price');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('average_cost_price');
        });
    }
};

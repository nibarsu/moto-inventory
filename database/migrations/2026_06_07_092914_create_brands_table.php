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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
    
            $table->string('code', 30)->unique()->comment('品牌代碼');
            $table->string('name', 100)->comment('品牌名稱');
            $table->string('english_name', 100)->nullable()->comment('英文名稱');
    
            $table->text('remark')->nullable()->comment('備註');
    
            $table->boolean('is_active')
                  ->default(true)
                  ->comment('是否啟用');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};

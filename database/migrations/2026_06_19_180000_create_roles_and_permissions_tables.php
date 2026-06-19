<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('remark')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('name', 100);
            $table->string('group_key', 50);
            $table->text('remark')->nullable();
            $table->boolean('is_system')->default(true);
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['role_id', 'user_id']);
        });

        $now = now();

        $permissions = [
            ['code' => 'brands.manage', 'name' => '品牌管理', 'group_key' => 'master'],
            ['code' => 'categories.manage', 'name' => '商品分類管理', 'group_key' => 'master'],
            ['code' => 'warehouses.manage', 'name' => '倉庫管理', 'group_key' => 'master'],
            ['code' => 'suppliers.manage', 'name' => '供應商管理', 'group_key' => 'master'],
            ['code' => 'customers.manage', 'name' => '客戶管理', 'group_key' => 'master'],
            ['code' => 'parts.manage', 'name' => '零件商品管理', 'group_key' => 'product'],
            ['code' => 'vehicles.manage', 'name' => '整車商品管理', 'group_key' => 'product'],
            ['code' => 'stocks.manage', 'name' => '庫存與庫存報表', 'group_key' => 'stock'],
            ['code' => 'purchase.manage', 'name' => '進貨管理', 'group_key' => 'purchase'],
            ['code' => 'sales.manage', 'name' => '銷貨管理', 'group_key' => 'sales'],
            ['code' => 'repairs.manage', 'name' => '維修與保養管理', 'group_key' => 'service'],
            ['code' => 'finance.manage', 'name' => '應收應付管理', 'group_key' => 'finance'],
            ['code' => 'barcode.manage', 'name' => '條碼功能', 'group_key' => 'utility'],
            ['code' => 'import.manage', 'name' => '商品匯入', 'group_key' => 'utility'],
            ['code' => 'export.manage', 'name' => 'Excel 匯出', 'group_key' => 'utility'],
            ['code' => 'permissions.manage', 'name' => '權限管理', 'group_key' => 'system'],
        ];

        DB::table('permissions')->insert(array_map(static fn (array $permission) => $permission + [
            'remark' => null,
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], $permissions));

        $adminRoleId = DB::table('roles')->insertGetId([
            'code' => 'admin',
            'name' => '系統管理員',
            'remark' => '預設管理角色，擁有所有權限。',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $permissionIds = DB::table('permissions')->pluck('id')->all();

        DB::table('permission_role')->insert(array_map(static fn (int $permissionId) => [
            'role_id' => $adminRoleId,
            'permission_id' => $permissionId,
        ], $permissionIds));

        $userIds = DB::table('users')->pluck('id')->all();

        DB::table('role_user')->insert(array_map(static fn (int $userId) => [
            'role_id' => $adminRoleId,
            'user_id' => $userId,
        ], $userIds));
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

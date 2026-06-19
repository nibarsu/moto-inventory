<?php

namespace App\Support;

class PermissionRegistry
{
    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'brands.manage' => '品牌管理',
            'categories.manage' => '商品分類管理',
            'warehouses.manage' => '倉庫管理',
            'suppliers.manage' => '供應商管理',
            'customers.manage' => '客戶管理',
            'parts.manage' => '零件商品管理',
            'vehicles.manage' => '整車商品管理',
            'stocks.manage' => '庫存與庫存報表',
            'purchase.manage' => '進貨管理',
            'sales.manage' => '銷貨管理',
            'repairs.manage' => '維修與保養管理',
            'finance.manage' => '應收應付管理',
            'barcode.manage' => '條碼功能',
            'import.manage' => '商品匯入',
            'export.manage' => 'Excel 匯出',
            'permissions.manage' => '權限管理',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function groups(): array
    {
        return [
            'master' => '主檔管理',
            'product' => '商品管理',
            'stock' => '庫存管理',
            'purchase' => '進貨管理',
            'sales' => '銷貨管理',
            'service' => '維修保養',
            'finance' => '財務管理',
            'utility' => '工具功能',
            'system' => '系統管理',
        ];
    }
}

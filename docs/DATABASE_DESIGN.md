# Database Design

## Overview

The current database is organized around three groups:

- master data tables
- product tables
- stock tables
- purchase tables

The system also relies on Laravel default `users`, `cache`, and `jobs` tables.

## Master Data Tables

### `brands`

Purpose: stores brand master data for parts and vehicles.

Columns:

- `id`
- `code` unique, max 30
- `name` max 100
- `english_name` nullable, max 100
- `remark` nullable
- `is_active` boolean, default true
- timestamps

### `categories`

Purpose: shared category master for both parts and vehicles.

Columns:

- `id`
- `code` unique, max 30
- `name` max 100
- `type` enum: `part`, `vehicle`
- `remark` nullable
- `is_active` boolean, default true
- timestamps

### `warehouses`

Purpose: warehouse/location master used by stock modules.

Columns:

- `id`
- `code` unique, max 30
- `name` max 100
- `address` nullable
- `contact_person` nullable, max 50
- `phone` nullable, max 30
- `remark` nullable
- `is_active` boolean, default true
- timestamps

### `suppliers`

Purpose: supplier master.

Columns:

- `id`
- `code` unique, max 30
- `name` max 100
- `tax_id` nullable, max 20
- `contact_person` nullable, max 50
- `phone` nullable, max 30
- `mobile` nullable, max 30
- `email` nullable, max 100
- `address` nullable
- `remark` nullable
- `is_active` boolean, default true
- timestamps

### `customers`

Purpose: customer master.

Columns:

- `id`
- `code` unique, max 30
- `name` max 100
- `phone` nullable, max 30
- `mobile` nullable, max 30
- `email` nullable, max 100
- `address` nullable
- `tax_id` nullable, max 20
- `remark` nullable
- `is_active` boolean, default true
- timestamps

## Product Tables

### `parts`

Purpose: stores part and consumable item master data.

Columns:

- `id`
- `part_no` unique, max 50
- `barcode` nullable, max 50
- `name` max 150
- `brand_id` nullable FK -> `brands.id`, `nullOnDelete`
- `category_id` nullable FK -> `categories.id`, `nullOnDelete`
- `unit` max 20, default `個`
- `last_cost_price` decimal(12,2), default 0
- `sale_price` decimal(12,2), default 0
- `safety_stock` integer, default 0
- `remark` nullable
- `is_active` boolean, default true
- timestamps

Notes:

- `category_id` is intended to point only to categories with `type = part`.

### `vehicles`

Purpose: stores complete vehicle product master data.

Columns:

- `id`
- `model_code` unique, max 50
- `barcode` nullable, max 50
- `name` max 150
- `brand_id` nullable FK -> `brands.id`, `nullOnDelete`
- `category_id` nullable FK -> `categories.id`, `nullOnDelete`
- `year` nullable integer
- `color` nullable, max 50
- `engine_displacement` nullable, max 50
- `last_cost_price` decimal(12,2), default 0
- `sale_price` decimal(12,2), default 0
- `remark` nullable
- `is_active` boolean, default true
- timestamps

Notes:

- `category_id` is intended to point only to categories with `type = vehicle`.

## Stock Tables

### `part_stocks`

Purpose: current part stock balance by warehouse.

Columns:

- `id`
- `part_id` FK -> `parts.id`, `cascadeOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `cascadeOnDelete`
- `quantity` integer, default 0
- timestamps

Constraints:

- unique key on `part_id + warehouse_id`

### `vehicle_stocks`

Purpose: current vehicle stock balance by warehouse.

Columns:

- `id`
- `vehicle_id` FK -> `vehicles.id`, `cascadeOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `cascadeOnDelete`
- `quantity` integer, default 0
- timestamps

Constraints:

- unique key on `vehicle_id + warehouse_id`

### `stock_movements`

Purpose: stock movement history table for both parts and vehicles.

Columns:

- `id`
- `item_type` string(20): expected values `part`, `vehicle`
- `item_id` unsigned big integer
- `warehouse_id` FK -> `warehouses.id`, `cascadeOnDelete`
- `movement_type` string(20): expected values `in`, `out`, `adjust`
- `quantity` integer
- `before_quantity` integer, default 0
- `after_quantity` integer, default 0
- `reference_type` nullable string(50)
- `reference_id` nullable unsigned big integer
- `remark` nullable
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

Notes:

- `item_type + item_id` is a manual polymorphic reference.
- There is no database-level FK from `item_id` to `parts` or `vehicles`.

## Purchase Tables

### `purchase_orders`

Purpose: stores purchase order header/master data.

Columns:

- `id`
- `po_no` unique, max 30
- `order_date` date
- `expected_date` nullable date
- `supplier_id` FK -> `suppliers.id`, `restrictOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `restrictOnDelete`
- `status` string(20), default `draft`
- `total_amount` decimal(12,2), default 0
- `remark` nullable
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

### `purchase_order_items`

Purpose: stores purchase order line items for either parts or vehicles.

Columns:

- `id`
- `purchase_order_id` FK -> `purchase_orders.id`, `cascadeOnDelete`
- `item_type` string(20): expected values `part`, `vehicle`
- `item_id` unsigned big integer
- `item_code` nullable string(50)
- `item_name` string(150)
- `quantity` integer, default 1
- `unit_price` decimal(12,2), default 0
- `line_total` decimal(12,2), default 0
- `remark` nullable
- timestamps

Notes:

- `item_type + item_id` is a manual polymorphic reference.
- `item_code` and `item_name` preserve a snapshot of the selected item at the time of line creation/update.
- `line_total` is stored directly and synchronized from `quantity * unit_price` in application logic.

## Implemented Model Relationships

### `Part`

- belongs to `Brand`
- belongs to `Category`
- has many `PartStock`
- has many filtered `StockMovement` where `item_type = part`
- has many filtered `PurchaseOrderItem` where `item_type = part`

### `Vehicle`

- belongs to `Brand`
- belongs to `Category`
- has many `VehicleStock`
- has many filtered `StockMovement` where `item_type = vehicle`
- has many filtered `PurchaseOrderItem` where `item_type = vehicle`

### `Warehouse`

- has many `PartStock`
- has many `VehicleStock`
- has many `StockMovement`

### `PartStock`

- belongs to `Part`
- belongs to `Warehouse`

### `VehicleStock`

- belongs to `Vehicle`
- belongs to `Warehouse`

### `StockMovement`

- belongs to `Warehouse`
- belongs to creator `User`

### `PurchaseOrder`

- belongs to `Supplier`
- belongs to `Warehouse`
- belongs to creator `User`
- has many `PurchaseOrderItem`

### `PurchaseOrderItem`

- belongs to `PurchaseOrder`

## Relationship Diagram Summary

- `brands` -> `parts`
- `brands` -> `vehicles`
- `categories` -> `parts`
- `categories` -> `vehicles`
- `parts` -> `part_stocks`
- `vehicles` -> `vehicle_stocks`
- `warehouses` -> `part_stocks`
- `warehouses` -> `vehicle_stocks`
- `warehouses` -> `stock_movements`
- `suppliers` -> `purchase_orders`
- `warehouses` -> `purchase_orders`
- `purchase_orders` -> `purchase_order_items`
- `users` -> `stock_movements.created_by`
- `users` -> `purchase_orders.created_by`

## Known Design Gaps

- `StockMovement` does not use true polymorphic Eloquent relations yet.
- Reverse relationships from `Brand`, `Category`, `Supplier`, and `Customer` are not implemented in models.
- Purchase order receiving and inventory update flow do not exist yet.
- There are no sales order, goods receipt, or delivery transaction tables yet.

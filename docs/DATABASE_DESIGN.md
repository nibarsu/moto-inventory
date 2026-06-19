# Database Design

## Overview

The database is currently organized around:

- master data tables
- product tables
- stock tables
- purchase tables
- sales tables
- repair tables
- maintenance tables
- finance tables

Laravel default tables such as `users`, `cache`, and `jobs` are also present.

## Master Data Tables

### `brands`

- `id`
- `code` unique, max 30
- `name` max 100
- `english_name` nullable, max 100
- `remark` nullable
- `is_active` boolean, default true
- timestamps

### `categories`

- `id`
- `code` unique, max 30
- `name` max 100
- `type` enum-style value: `part`, `vehicle`
- `remark` nullable
- `is_active` boolean, default true
- timestamps

### `warehouses`

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

- `id`
- `part_no` unique, max 50
- `barcode` nullable, max 50
- `name` max 150
- `brand_id` nullable FK -> `brands.id`, `nullOnDelete`
- `category_id` nullable FK -> `categories.id`, `nullOnDelete`
- `unit` max 20, default `個`
- `last_cost_price` decimal(12,2), default 0
- `average_cost_price` decimal(12,4), default 0
- `sale_price` decimal(12,2), default 0
- `safety_stock` integer, default 0
- `remark` nullable
- `is_active` boolean, default true
- timestamps

### `vehicles`

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
- `average_cost_price` decimal(12,4), default 0
- `sale_price` decimal(12,2), default 0
- `remark` nullable
- `is_active` boolean, default true
- timestamps

## Stock Tables

### `part_stocks`

- `id`
- `part_id` FK -> `parts.id`, `cascadeOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `cascadeOnDelete`
- `quantity` integer, default 0
- timestamps

Constraint:

- unique key on `part_id + warehouse_id`

### `vehicle_stocks`

- `id`
- `vehicle_id` FK -> `vehicles.id`, `cascadeOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `cascadeOnDelete`
- `quantity` integer, default 0
- timestamps

Constraint:

- unique key on `vehicle_id + warehouse_id`

### `stock_movements`

- `id`
- `item_type` string(20): `part`, `vehicle`
- `item_id` unsigned big integer
- `warehouse_id` FK -> `warehouses.id`, `cascadeOnDelete`
- `movement_type` string(20): `in`, `out`, `adjust`
- `quantity` integer
- `before_quantity` integer, default 0
- `after_quantity` integer, default 0
- `reference_type` nullable string(50)
- `reference_id` nullable unsigned big integer
- `remark` nullable
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

Note:

- Inventory report is generated directly from `part_stocks` and `vehicle_stocks` joined with product and warehouse masters. No separate report table exists.
- Purchase report is generated directly from `purchase_receipts` and `purchase_receipt_items`. No separate report table exists.
- Sales report is generated directly from `sales_shipments` and `sales_shipment_items`. No separate report table exists.

## Purchase Tables

### `purchase_orders`

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

- `id`
- `purchase_order_id` FK -> `purchase_orders.id`, `cascadeOnDelete`
- `item_type` string(20): `part`, `vehicle`
- `item_id` unsigned big integer
- `item_code` nullable string(50)
- `item_name` string(150)
- `quantity` integer, default 1
- `received_quantity` integer, default 0
- `unit_price` decimal(12,2), default 0
- `line_total` decimal(12,2), default 0
- `remark` nullable
- timestamps

### `purchase_receipts`

- `id`
- `receipt_no` unique, max 30
- `purchase_order_id` FK -> `purchase_orders.id`, `restrictOnDelete`
- `receipt_date` date
- `supplier_id` FK -> `suppliers.id`, `restrictOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `restrictOnDelete`
- `total_amount` decimal(12,2), default 0
- `remark` nullable
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

### `purchase_receipt_items`

- `id`
- `purchase_receipt_id` FK -> `purchase_receipts.id`, `cascadeOnDelete`
- `purchase_order_item_id` nullable FK -> `purchase_order_items.id`, `nullOnDelete`
- `item_type` string(20): `part`, `vehicle`
- `item_id` unsigned big integer
- `item_code` nullable string(50)
- `item_name` string(150)
- `quantity` integer
- `unit_cost` decimal(12,2), default 0
- `line_total` decimal(12,2), default 0
- `remark` nullable
- timestamps

## Sales Tables

### `sales_orders`

- `id`
- `so_no` unique, max 30
- `order_date` date
- `delivery_date` nullable date
- `customer_id` FK -> `customers.id`, `restrictOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `restrictOnDelete`
- `status` string(20), default `draft`
- `total_amount` decimal(12,2), default 0
- `remark` nullable
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

### `sales_order_items`

- `id`
- `sales_order_id` FK -> `sales_orders.id`, `cascadeOnDelete`
- `item_type` string(20): `part`, `vehicle`
- `item_id` unsigned big integer
- `item_code` nullable string(50)
- `item_name` string(150)
- `quantity` integer, default 1
- `delivered_quantity` integer, default 0
- `unit_price` decimal(12,2), default 0
- `line_total` decimal(12,2), default 0
- `remark` nullable
- timestamps

### `sales_shipments`

- `id`
- `shipment_no` unique, max 30
- `sales_order_id` FK -> `sales_orders.id`, `restrictOnDelete`
- `shipment_date` date
- `customer_id` FK -> `customers.id`, `restrictOnDelete`
- `warehouse_id` FK -> `warehouses.id`, `restrictOnDelete`
- `total_amount` decimal(12,2), default 0
- `remark` nullable
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

### `sales_shipment_items`

- `id`
- `sales_shipment_id` FK -> `sales_shipments.id`, `cascadeOnDelete`
- `sales_order_item_id` nullable FK -> `sales_order_items.id`, `nullOnDelete`
- `item_type` string(20): `part`, `vehicle`
- `item_id` unsigned big integer
- `item_code` nullable string(50)
- `item_name` string(150)
- `quantity` integer
- `unit_price` decimal(12,2), default 0
- `line_total` decimal(12,2), default 0
- `remark` nullable
- timestamps

## Repair Tables

### `repair_orders`

- `id`
- `wo_no` unique, max 30
- `order_date` date
- `customer_id` FK -> `customers.id`, `restrictOnDelete`
- `vehicle_id` nullable FK -> `vehicles.id`, `nullOnDelete`
- `plate_no` nullable, max 20
- `mileage` nullable unsigned integer
- `status` string(20), default `open`
- `complaint` nullable text
- `diagnosis` nullable text
- `remark` nullable text
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

## Maintenance Tables

### `maintenance_records`

- `id`
- `record_no` unique, max 30
- `service_date` date
- `customer_id` FK -> `customers.id`, `restrictOnDelete`
- `vehicle_id` nullable FK -> `vehicles.id`, `nullOnDelete`
- `repair_order_id` nullable FK -> `repair_orders.id`, `nullOnDelete`
- `plate_no` nullable, max 20
- `mileage` nullable unsigned integer
- `service_type` string(50)
- `next_service_date` nullable date
- `next_service_mileage` nullable unsigned integer
- `service_content` nullable text
- `remark` nullable text
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

## Implemented Model Relationships

### `Part`

- belongs to `Brand`
- belongs to `Category`
- has many `PartStock`
- has many filtered `StockMovement` where `item_type = part`
- has many filtered `PurchaseOrderItem` where `item_type = part`
- has many filtered `PurchaseReceiptItem` where `item_type = part`
- has many filtered `SalesOrderItem` where `item_type = part`
- has many filtered `SalesShipmentItem` where `item_type = part`

### `Vehicle`

- belongs to `Brand`
- belongs to `Category`
- has many `VehicleStock`
- has many filtered `StockMovement` where `item_type = vehicle`
- has many filtered `PurchaseOrderItem` where `item_type = vehicle`
- has many filtered `PurchaseReceiptItem` where `item_type = vehicle`
- has many filtered `SalesOrderItem` where `item_type = vehicle`
- has many filtered `SalesShipmentItem` where `item_type = vehicle`

### `Warehouse`

- has many `PartStock`
- has many `VehicleStock`
- has many `StockMovement`
- has many `PurchaseReceipt`
- has many `SalesShipment`

### `PurchaseOrder`

- belongs to `Supplier`
- belongs to `Warehouse`
- belongs to creator `User`
- has many `PurchaseOrderItem`

### `PurchaseReceipt`

- belongs to `PurchaseOrder`
- belongs to `Supplier`
- belongs to `Warehouse`
- belongs to creator `User`
- has many `PurchaseReceiptItem`

### Purchase Reporting Read Model

- Source header table: `purchase_receipts`
- Source line table: `purchase_receipt_items`
- Common filters:
  - receipt date range
  - supplier
  - warehouse
  - item type (`part`, `vehicle`)
  - keyword on receipt no / purchase order no / item code / item name
- Report totals:
  - line count
  - total receipt quantity
  - total receipt amount

### `SalesOrder`

- belongs to `Customer`
- belongs to `Warehouse`
- belongs to creator `User`
- has many `SalesOrderItem`
- has many `SalesShipment`

### `SalesOrderItem`

- belongs to `SalesOrder`
- belongs to `Part` through `item_id`
- belongs to `Vehicle` through `item_id`
- has many `SalesShipmentItem`

### `SalesShipment`

- belongs to `SalesOrder`
- belongs to `Customer`
- belongs to `Warehouse`
- belongs to creator `User`
- has many `SalesShipmentItem`

### `SalesShipmentItem`

- belongs to `SalesShipment`
- belongs to `SalesOrderItem`

### `RepairOrder`

- belongs to `Customer`
- belongs to `Vehicle`
- belongs to creator `User`
- has many `MaintenanceRecord`

### `MaintenanceRecord`

- belongs to `Customer`
- belongs to `Vehicle`
- belongs to `RepairOrder`
- belongs to creator `User`

### Owner History Read Model

- No dedicated table
- Source tables:
  - `repair_orders`
  - `maintenance_records`
- Common filters:
  - customer
  - keyword
- Keyword can match:
  - customer name
  - customer phone / mobile
  - plate number
  - record number
  - complaint / diagnosis / service content

### Sales Reporting Read Model

- Source header table: `sales_shipments`
- Source line table: `sales_shipment_items`
- Common filters:
  - shipment date range
  - customer
  - warehouse
  - item type (`part`, `vehicle`)
  - keyword on shipment no / sales order no / item code / item name
- Report totals:
  - line count
  - total shipment quantity
  - total shipment amount

## Relationship Summary

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
- `purchase_orders` -> `purchase_receipts`
- `purchase_receipts` -> `purchase_receipt_items`
- `customers` -> `sales_orders`
- `sales_orders` -> `sales_order_items`
- `sales_orders` -> `sales_shipments`
- `sales_shipments` -> `sales_shipment_items`
- `customers` -> `repair_orders`
- `vehicles` -> `repair_orders`
- `customers` -> `maintenance_records`
- `vehicles` -> `maintenance_records`
- `repair_orders` -> `maintenance_records`

## Finance Tables

### `accounts_receivable`

- `id`
- `ar_no` unique, max 30
- `customer_id` FK -> `customers.id`, `restrictOnDelete`
- `source_type` nullable string(30)
- `source_id` nullable unsigned big integer
- `ar_date` date
- `due_date` nullable date
- `total_amount` decimal(12,2), default 0
- `received_amount` decimal(12,2), default 0
- `balance_amount` decimal(12,2), default 0
- `status` string(20), default `open`
- `remark` nullable text
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

### `Receivable`

- belongs to `Customer`
- belongs to creator `User`

### `accounts_payable`

- `id`
- `ap_no` unique, max 30
- `supplier_id` FK -> `suppliers.id`, `restrictOnDelete`
- `source_type` nullable string(30)
- `source_id` nullable unsigned big integer
- `ap_date` date
- `due_date` nullable date
- `total_amount` decimal(12,2), default 0
- `paid_amount` decimal(12,2), default 0
- `balance_amount` decimal(12,2), default 0
- `status` string(20), default `open`
- `remark` nullable text
- `created_by` nullable FK -> `users.id`, `nullOnDelete`
- timestamps

### `Payable`

- belongs to `Supplier`
- belongs to creator `User`

## Known Design Gaps

- Barcode printing does not introduce new tables; it uses existing `parts.barcode`, `parts.part_no`, `vehicles.barcode`, and `vehicles.model_code`.
- Barcode scanning does not introduce new tables; it looks up existing `parts` and `vehicles` by barcode or product code.
- `StockMovement` still uses manual `item_type + item_id` instead of true Eloquent morph relations.
- Reverse relationships on some master models are still intentionally minimal.

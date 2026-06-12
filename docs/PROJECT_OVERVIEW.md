# Project Overview

## Purpose

This project is a Laravel 12 motorcycle dealership inventory and operations system. It currently focuses on master data management and stock foundation management for common dealership entities.

## Current Stack

- Laravel 12
- PHP 8.3
- MySQL 8
- Laravel Breeze authentication scaffolding
- Blade views with Breeze layout components
- Eloquent ORM
- GitHub

## Documentation Map

- [PROJECT_OVERVIEW.md](/c:/laragon/www/moto-inventory/docs/PROJECT_OVERVIEW.md)
- [DATABASE_DESIGN.md](/c:/laragon/www/moto-inventory/docs/DATABASE_DESIGN.md)
- [BUSINESS_RULES.md](/c:/laragon/www/moto-inventory/docs/BUSINESS_RULES.md)
- [DEVELOPMENT_RULES.md](/c:/laragon/www/moto-inventory/docs/DEVELOPMENT_RULES.md)
- [ROADMAP.md](/c:/laragon/www/moto-inventory/docs/ROADMAP.md)

## Completed Modules

- Brand
- Category
- Warehouse
- Supplier
- Customer
- Part
- Vehicle
- Stock base module
- Purchase Order
- Purchase Order Item
- Purchase Receipt
- Average Cost Calculation

## Functional Scope

The current system provides:

- Login-protected back-office management
- CRUD management for core master data
- Product master data split into parts and vehicles
- Basic stock balance tracking per warehouse
- Stock adjustment and stock movement history
- Purchase order master management
- Purchase order line item management with mixed part/vehicle support
- Purchase receiving with stock-in posting and receipt history
- Average cost tracking for parts and vehicles

## Main Domain Objects

- `Brand`: vehicle/part brand master
- `Category`: shared category master with `part` and `vehicle` type separation
- `Warehouse`: stock location master
- `Supplier`: supplier master
- `Customer`: customer master
- `Part`: part/consumable item master
- `Vehicle`: complete vehicle master
- `PurchaseOrder`: purchase order header/master
- `PurchaseOrderItem`: purchase order line item snapshot
- `PurchaseReceipt`: purchase receiving header
- `PurchaseReceiptItem`: purchase receiving line snapshot
- `AverageCost`: derived product cost view based on stock and receipts
- `PartStock`: part stock balance by warehouse
- `VehicleStock`: vehicle stock balance by warehouse
- `StockMovement`: stock transaction log

## Route Structure

All management routes are registered in [routes/web.php](/c:/laragon/www/moto-inventory/routes/web.php) and are protected by `auth` middleware.

### Resource CRUD routes

- `brands`
- `categories`
- `customers`
- `parts`
- `purchase-orders`
- `purchase-orders.items`
- `purchase-receipts`
- `average-costs`
- `suppliers`
- `vehicles`
- `warehouses`

### Stock routes

- `GET /stocks`
- `GET /stock-movements`
- `GET /stocks/adjust`
- `POST /stocks/adjust`

## View Structure

Each CRUD module follows the same Blade structure under `resources/views/<module>`:

- `index.blade.php`
- `create.blade.php`
- `edit.blade.php`
- `show.blade.php`

Stock module views live under `resources/views/stocks`:

- `index.blade.php`
- `movements.blade.php`
- `adjust.blade.php`

All pages use the Breeze app layout and shared navigation.

## Current Architectural Notes

- Master data models mostly expose fillable fields and casts.
- Relationship-heavy models currently are `Part`, `Vehicle`, `Warehouse`, and stock models.
- Purchasing transactions now flow through `PurchaseOrder`, `PurchaseOrderItem`, `PurchaseReceipt`, and `PurchaseReceiptItem`.
- Average cost is stored on `Part` and `Vehicle` and recalculated during purchase receipt posting.
- `Brand`, `Category`, `Supplier`, and `Customer` currently do not define reverse relationships, even though database relations exist indirectly through other tables.
- Stock movement history uses a polymorphic-style structure with `item_type` + `item_id`, but it is implemented manually rather than through Laravel morph relations.
- Purchase order items also use a manual `item_type` + `item_id` pattern so line items can reference either parts or vehicles while preserving item snapshots.

## Maintenance Notes

- The project is currently optimized for internal admin use, not public-facing workflows.
- New modules should follow the same Breeze CRUD pattern unless there is a clear reason to diverge.
- After each module change, the established maintenance flow is:
  - run migrations if needed
  - run `artisan route:list`
  - commit
  - push

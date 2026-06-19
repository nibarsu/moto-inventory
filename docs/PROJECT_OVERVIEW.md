# Project Overview

## Purpose

This project is a Laravel 12 motorcycle dealership inventory and operations system. It currently covers master data, stock foundation, inventory reporting, purchasing, purchase reporting, average cost tracking, sales order entry, sales reporting, sales stock-out posting, repair work order management, and maintenance history intake.

## Current Stack

- Laravel 12
- PHP 8.3
- MySQL 8
- Laravel Breeze authentication scaffolding
- Blade views with Breeze layout components
- Tailwind CSS / Vite build pipeline
- Eloquent ORM
- Git / GitHub

## Documentation Map

- [PROJECT_OVERVIEW.md](/c:/laragon/www/moto-inventory/docs/PROJECT_OVERVIEW.md)
- [DATABASE_DESIGN.md](/c:/laragon/www/moto-inventory/docs/DATABASE_DESIGN.md)
- [BUSINESS_RULES.md](/c:/laragon/www/moto-inventory/docs/BUSINESS_RULES.md)
- [DEVELOPMENT_RULES.md](/c:/laragon/www/moto-inventory/docs/DEVELOPMENT_RULES.md)
- [ROADMAP.md](/c:/laragon/www/moto-inventory/docs/ROADMAP.md)
- [USER_MANUAL.md](/c:/laragon/www/moto-inventory/docs/USER_MANUAL.md)

## Completed Modules

- Brand
- Category
- Warehouse
- Supplier
- Customer
- Part
- Vehicle
- Stock base module
- Inventory Report
- Purchase Order
- Purchase Order Item
- Purchase Receipt
- Purchase Report
- Average Cost Calculation
- Sales Order
- Sales Order Item
- Sales Shipment
- Sales Report
- Repair Order
- Maintenance Record

## Functional Scope

The current system provides:

- Login-protected back-office management
- CRUD management for core master data
- Product master data split into parts and vehicles
- Basic stock balance tracking per warehouse
- Stock adjustment and stock movement history
- Inventory report with stock value visibility
- Purchase order header and line maintenance
- Purchase receiving with stock-in posting and receipt history
- Purchase report based on actual receipt transactions
- Average cost tracking for parts and vehicles
- Sales order header and line maintenance
- Sales stock-out posting with inventory deduction and stock movement logging
- Sales report based on actual shipment transactions
- Repair work order header management for after-sales service intake
- Maintenance record intake with optional linkage to repair work orders

## Main Domain Objects

- `Brand`: vehicle/part brand master
- `Category`: shared category master with `part` and `vehicle` type separation
- `Warehouse`: stock location master
- `Supplier`: supplier master
- `Customer`: customer master
- `Part`: part/consumable item master
- `Vehicle`: complete vehicle master
- `PartStock`: part stock balance by warehouse
- `VehicleStock`: vehicle stock balance by warehouse
- `StockMovement`: stock transaction log
- `PurchaseOrder`: purchase order header/master
- `PurchaseOrderItem`: purchase order line item snapshot
- `PurchaseReceipt`: purchase receiving header
- `PurchaseReceiptItem`: purchase receiving line snapshot
- `SalesOrder`: sales order header/master
- `SalesOrderItem`: sales order line item snapshot
- `SalesShipment`: sales stock-out header
- `SalesShipmentItem`: sales stock-out line snapshot
- `RepairOrder`: repair work order header
- `MaintenanceRecord`: maintenance history header

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
- `sales-orders`
- `sales-orders.items`
- `sales-shipments`
- `repair-orders`
- `maintenance-records`
- `suppliers`
- `vehicles`
- `warehouses`

### Utility routes

- `GET /average-costs`
- `GET /inventory-reports`
- `GET /purchase-reports`
- `GET /sales-reports`
- `GET /stocks`
- `GET /stock-movements`
- `GET /stocks/adjust`
- `POST /stocks/adjust`

## Current Architectural Notes

- Master data modules follow a consistent FormRequest + Controller + Blade CRUD pattern.
- Product selection for transactional lines uses manual `item_type` + `item_id` references so one line table can support both parts and vehicles.
- Purchase and sales line tables store `item_code` and `item_name` snapshots to reduce risk from later master-data edits.
- Average cost is recalculated during purchase receipt posting and stored on `parts.average_cost_price` and `vehicles.average_cost_price`.
- Inventory report is a read model composed from `part_stocks` / `vehicle_stocks` plus product masters.
- Purchase report is a read model composed from `purchase_receipts` / `purchase_receipt_items`.
- Sales report is a read model composed from `sales_shipments` / `sales_shipment_items`.
- Repair workflow currently covers work order header intake only.
- Maintenance workflow currently covers record header intake only.
- Sales workflow now covers order headers, order lines, and stock-out posting.

## Maintenance Notes

- New modules should follow the same Breeze CRUD pattern unless there is a clear reason to diverge.
- If a change adds new Blade Tailwind classes or touches shared layout assets, run `npm run build` before considering the UI change complete.
- Standard completion flow for a module:
  - run migrations if needed
  - run `php artisan route:list`
  - run `git add .`
  - commit
  - push

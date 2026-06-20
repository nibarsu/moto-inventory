# Project Overview

## Purpose

This project is a Laravel 12 motorcycle dealership inventory and operations system. It currently covers master data, stock foundation, inventory reporting, purchasing, purchase reporting, average cost tracking, sales order entry, sales reporting, sales stock-out posting, repair work order management, maintenance history intake, owner service history lookup, manual accounts receivable tracking, manual accounts payable tracking, barcode label printing, barcode scanning lookup, product CSV import, Excel export, and role-based permission management.

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
- [WINDOWS_PRODUCTION_INSTALL.md](/c:/laragon/www/moto-inventory/docs/WINDOWS_PRODUCTION_INSTALL.md)
- [.env.production.example](/c:/laragon/www/moto-inventory/.env.production.example)

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
- Owner History
- Accounts Receivable
- Accounts Payable
- Barcode Printing
- Barcode Scanning
- Product Import
- Excel Export
- Permission Management

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
- Owner history lookup across repair and maintenance records
- Manual accounts receivable maintenance for customer balances
- Manual accounts payable maintenance for supplier balances
- Barcode label printing for part and vehicle master data
- Barcode scanning lookup by camera or manual input
- Product CSV import with import logs
- Excel export center with export logs
- Role-based permission management for users and modules

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
- `OwnerHistory`: combined read model for owner service timeline
- `Receivable`: accounts receivable header
- `Payable`: accounts payable header
- `Role`: user role master
- `Permission`: system permission master

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
- `accounts-receivable`
- `accounts-payable`
- `vehicles`
- `warehouses`

### Utility routes

- `GET /average-costs`
- `GET /inventory-reports`
- `GET /purchase-reports`
- `GET /sales-reports`
- `GET /owner-histories`
- `GET /barcode-labels`
- `GET /barcode-scans`
- `GET /product-imports`
- `GET /excel-exports`
- `GET /roles`
- `GET /user-access`
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
- Owner history is a combined read model built from repair orders and maintenance records.
- Accounts receivable currently uses manual entry and status derived from total / received amount.
- Accounts payable currently uses manual entry and status derived from total / paid amount.
- Barcode printing uses existing product barcode values and falls back to part no / model code when barcode is blank.
- Barcode scanning uses browser-native APIs when available and falls back to manual barcode input.
- Product import uses CSV upload, master-code mapping, and import log history for auditability.
- Excel export uses SpreadsheetML `.xls` output without adding external spreadsheet packages.
- Permission management uses role-based access control with per-module route middleware.
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

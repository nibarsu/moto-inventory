# Business Rules

## Master Data

### Brand

- Brand code must be unique.
- Brand name is required.
- Brands support active/inactive status.

### Category

- Category code must be unique.
- Category name is required.
- Category type must be one of:
  - `part`
  - `vehicle`
- Category type determines where it can be used.

### Warehouse

- Warehouse code must be unique.
- Warehouse name is required.
- Warehouses support active/inactive status.

### Supplier

- Supplier code must be unique.
- Supplier name is required.
- Email is optional but must be valid if provided.
- Suppliers support active/inactive status.

### Customer

- Customer code must be unique.
- Customer name is required.
- Email is optional but must be valid if provided.
- Customers support active/inactive status.

## Product Rules

### Part

- `part_no` must be unique.
- Part name is required.
- Unit is required and defaults to `個`.
- `last_cost_price`, `sale_price`, and `safety_stock` cannot be negative.
- Brand is optional.
- Category is optional, but if selected it must be a category with `type = part`.
- Part product pages only use active brands and active part-type categories in the selection lists.

### Vehicle

- `model_code` must be unique.
- Vehicle name is required.
- `last_cost_price` and `sale_price` cannot be negative.
- `year` is optional and limited to a reasonable year range in validation.
- Brand is optional.
- Category is optional, but if selected it must be a category with `type = vehicle`.
- Vehicle product pages only use active brands and active vehicle-type categories in the selection lists.

## Stock Rules

### Stock Balance Tables

- A part can have only one stock balance row per warehouse.
- A vehicle can have only one stock balance row per warehouse.
- Stock quantity is stored as the current balance, not only as transaction history.

### Stock Movement

- Stock movement supports two item families:
  - `part`
  - `vehicle`
- Supported movement types are:
  - `in`
  - `out`
  - `adjust`

Current implemented workflow only uses:

- `adjust`

### Stock Adjustment

- User must choose item type: `part` or `vehicle`.
- User must choose one product matching the selected type.
- User must choose one warehouse.
- User enters the final target quantity, not the delta.
- System calculates:
  - `before_quantity`
  - `after_quantity`
  - `quantity` difference

Example:

- before = 10
- adjusted target = 8
- movement quantity = -2

### Stock Adjustment Persistence

- If no stock balance row exists yet for the selected item and warehouse, the system creates one with quantity 0 before adjustment.
- Stock update and movement insert are executed inside a database transaction.
- Movement record stores the acting user in `created_by` when authenticated.

## Routing and Access

- All management routes are behind `auth` middleware.
- Dashboard additionally uses `verified`.
- There is no public CRUD access for master data or stock functions.

## UI Rules

- CRUD pages follow the Breeze app layout.
- `index` pages show server-rendered paginated tables for core master modules.
- Stock query page currently aggregates part and vehicle stock into one list.
- Stock movement page is paginated and sorted newest first.

## Operational Assumptions

- Parts and vehicles are separate masters because their attributes differ.
- Categories are shared, but type-restricted.
- Inventory is warehouse-based.
- Stock movement history is intended to become the audit trail for future purchasing and sales workflows.

## Current Limitations

- Stock module currently supports manual adjustment only.
- There are no sales, transfer, or repair transaction modules yet.
- No automatic stock reservation or safety stock alert logic is implemented.

## Purchase Rules

### Purchase Order

- Purchase order number must be unique.
- Purchase order requires:
  - order date
  - supplier
  - warehouse
  - status
  - total amount
- `expected_date` is optional but cannot be earlier than `order_date`.
- Current supported statuses are:
  - `draft`
  - `confirmed`
  - `completed`
  - `cancelled`
- `total_amount` must preserve the actual purchase amount and cannot be negative.
- `total_amount` is synchronized from purchase order line totals.

### Purchase Order Item

- Purchase order items support two item families:
  - `part`
  - `vehicle`
- User must choose one concrete item matching the selected `item_type`.
- `quantity` must be at least 1.
- `unit_price` cannot be negative.
- `line_total` is calculated as `quantity * unit_price`.
- `item_code` and `item_name` are stored on the line to preserve a snapshot of the selected item.
- When a line is created, updated, or deleted, the parent purchase order `total_amount` must be recalculated from all line totals.

### Purchase Receipt

- Purchase receipt must be linked to one purchase order.
- Only purchase orders with remaining receivable quantity can be used for stock-in.
- Cancelled purchase orders cannot be received.
- Purchase receipt uses the purchase order warehouse as the stock-in warehouse.
- Receipt line quantity must be greater than zero and cannot exceed the remaining quantity of the purchase order item.
- Receipt line `unit_cost` stores the actual received cost amount.
- Purchase receipt `total_amount` is synchronized from receipt line totals.
- Each receipt line increases stock in the target warehouse and creates one `stock_movements` record with:
  - `movement_type = in`
  - `reference_type = purchase_receipt`
- `received_quantity` on the purchase order item is accumulated from posted receipts.
- After receipt posting:
  - if all purchase order lines are fully received, purchase order status becomes `completed`
  - otherwise purchase order status becomes `confirmed`
- Received `unit_cost` updates the product `last_cost_price`.

### Average Cost Calculation

- Parts and vehicles each maintain their own `average_cost_price`.
- Average cost is recalculated only from purchase receipt posting in the current phase.
- Current formula is weighted average:
  - `(existing stock quantity * existing average cost + received quantity * received unit cost) / new total quantity`
- The calculation uses total stock across all warehouses for the product before the new receipt quantity is added.
- If existing stock quantity is zero, average cost becomes the received `unit_cost`.
- `last_cost_price` preserves the most recent received unit cost.
- Average cost is stored with 4 decimal places to reduce forced rounding during cost accumulation.

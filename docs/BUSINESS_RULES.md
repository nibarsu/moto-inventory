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

### Warehouse

- Warehouse code must be unique.
- Warehouse name is required.
- Warehouses support active/inactive status.

### Supplier

- Supplier code must be unique.
- Supplier name is required.
- Email is optional but must be valid if provided.

### Customer

- Customer code must be unique.
- Customer name is required.
- Email is optional but must be valid if provided.

## Product Rules

### Part

- `part_no` must be unique.
- Part name is required.
- Unit defaults to `個`.
- `last_cost_price`, `average_cost_price`, `sale_price`, and `safety_stock` cannot be negative.
- Category must be `type = part` when selected.

### Vehicle

- `model_code` must be unique.
- Vehicle name is required.
- `last_cost_price`, `average_cost_price`, and `sale_price` cannot be negative.
- Category must be `type = vehicle` when selected.

## Stock Rules

### Stock Balance

- A part can have only one stock balance row per warehouse.
- A vehicle can have only one stock balance row per warehouse.
- Stock quantity stores the current balance.

### Stock Movement

- Supported item families:
  - `part`
  - `vehicle`
- Supported movement types:
  - `in`
  - `out`
  - `adjust`

### Stock Adjustment

- User chooses item type, product, warehouse, and target quantity.
- System calculates:
  - `before_quantity`
  - `after_quantity`
  - quantity delta
- If the balance row does not exist yet, the system creates it before adjustment.
- Stock update and movement insert must run in one database transaction.

### Inventory Report

- Inventory report is read-only.
- Inventory report can filter by:
  - item type
  - warehouse
  - active status
  - keyword
- Inventory report shows one row per item per warehouse.
- `stock_cost_amount = quantity * average_cost_price`
- `stock_sale_amount = quantity * sale_price`
- The report does not change any stock quantity.

## Purchase Rules

### Purchase Order

- Purchase order number must be unique.
- Purchase order requires order date, supplier, warehouse, and status.
- `expected_date` cannot be earlier than `order_date`.
- Supported statuses:
  - `draft`
  - `confirmed`
  - `completed`
  - `cancelled`
- `total_amount` is synchronized from line totals.

### Purchase Order Item

- Purchase order items support `part` and `vehicle`.
- `quantity` must be at least 1.
- `unit_price` cannot be negative.
- `line_total = quantity * unit_price`.
- Item code and name are stored as snapshots.

### Purchase Receipt

- Purchase receipt must link to one purchase order.
- Cancelled purchase orders cannot be received.
- Receipt quantity cannot exceed remaining quantity of the purchase order item.
- Receipt posting increases stock and creates `stock_movements` with `movement_type = in`.
- Receipt posting updates `received_quantity` on purchase order items.
- Receipt posting updates `last_cost_price`.

### Purchase Report

- Purchase report is read-only.
- Purchase report is based on posted purchase receipt data, not draft purchase orders.
- Purchase report can filter by:
  - receipt date range
  - supplier
  - warehouse
  - item type
  - keyword
- Purchase report totals are calculated from filtered receipt lines.
- Purchase report must preserve actual receipt quantity and actual receipt amount.

### Average Cost

- Parts and vehicles each maintain their own `average_cost_price`.
- Average cost is recalculated from purchase receipt posting.
- `last_cost_price` preserves the most recent received cost.
- Average cost is stored with 4 decimal places.

## Sales Rules

### Sales Order

- Sales order number must be unique.
- Sales order requires order date, customer, warehouse, and status.
- `delivery_date` cannot be earlier than `order_date`.
- Supported statuses:
  - `draft`
  - `confirmed`
  - `completed`
  - `cancelled`
- `total_amount` is synchronized from line totals.

### Sales Order Item

- Sales order items support `part` and `vehicle`.
- `quantity` must be at least 1.
- `unit_price` cannot be negative.
- `line_total = quantity * unit_price`.
- Item code and name are stored as snapshots.
- Creating, updating, or deleting a line must recalculate parent `total_amount`.
- `delivered_quantity` tracks cumulative shipped quantity.

### Sales Shipment

- Sales shipment must link to one sales order.
- Cancelled sales orders cannot be shipped.
- Shipment date cannot be earlier than sales order date.
- Shipment quantity cannot exceed:
  - remaining quantity on the sales order item
  - available stock in the sales order warehouse
- Shipment posting decreases stock and creates `stock_movements` with `movement_type = out`.
- Stock movement quantity is stored as a negative number for stock-out.
- Shipment posting updates `delivered_quantity` on sales order items.
- After shipment posting:
  - if all line items are fully shipped, sales order status becomes `completed`
  - otherwise sales order status becomes `confirmed`

### Sales Report

- Sales report is read-only.
- Sales report is based on posted shipment data, not draft sales orders.
- Sales report can filter by:
  - shipment date range
  - customer
  - warehouse
  - item type
  - keyword
- Sales report totals are calculated from filtered shipment lines.
- Sales report must preserve actual shipped quantity and actual sales amount.

## Repair Rules

### Repair Order

- Repair order number must be unique.
- Repair order requires order date, customer, and status.
- Supported statuses:
  - `open`
  - `in_progress`
  - `completed`
  - `cancelled`
- `vehicle_id` is optional because intake may happen before the vehicle master is linked.
- `plate_no` and `mileage` are optional intake fields.
- Complaint, diagnosis, and remark are free-text notes and may be updated as work progresses.

### Maintenance Record

- Maintenance record number must be unique.
- Maintenance record requires service date, customer, and service type.
- `vehicle_id` is optional.
- `repair_order_id` is optional.
- `next_service_date` cannot be earlier than `service_date`.
- `plate_no`, `mileage`, and `next_service_mileage` are optional fields.
- Service content and remark are free-text notes.

## Current Limitations

- Stock reservation is not implemented.
- Shipment reversal / delete flow is not implemented.
- Export features are not implemented yet.
- Repair order line items are not implemented yet.

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
- `last_cost_price`, `average_cost_price`, `sale_price`, and `safety_stock` cannot be negative.
- Brand is optional.
- Category is optional, but if selected it must be a category with `type = part`.

### Vehicle

- `model_code` must be unique.
- Vehicle name is required.
- `last_cost_price`, `average_cost_price`, and `sale_price` cannot be negative.
- `year` is optional and limited to a reasonable year range in validation.
- Brand is optional.
- Category is optional, but if selected it must be a category with `type = vehicle`.

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

## Purchase Rules

### Purchase Order

- Purchase order number must be unique.
- Purchase order requires:
  - order date
  - supplier
  - warehouse
  - status
- `expected_date` is optional but cannot be earlier than `order_date`.
- Supported statuses:
  - `draft`
  - `confirmed`
  - `completed`
  - `cancelled`
- `total_amount` must preserve actual purchase amount and cannot be negative.
- `total_amount` is synchronized from line totals.

### Purchase Order Item

- Purchase order items support:
  - `part`
  - `vehicle`
- User must choose one concrete item matching `item_type`.
- `quantity` must be at least 1.
- `unit_price` cannot be negative.
- `line_total` is calculated as `quantity * unit_price`.
- `item_code` and `item_name` are stored as a snapshot.
- Creating, updating, or deleting a line must recalculate parent `total_amount`.

### Purchase Receipt

- Purchase receipt must be linked to one purchase order.
- Only purchase orders with remaining receivable quantity can be received.
- Cancelled purchase orders cannot be received.
- Receipt line quantity must be greater than zero and cannot exceed the remaining quantity of the purchase order item.
- Receipt line `unit_cost` stores the actual received amount.
- Receipt posting increases stock and creates `stock_movements` with `movement_type = in`.
- Receipt posting updates `received_quantity` on purchase order items.
- Receipt posting updates `last_cost_price`.

### Average Cost

- Parts and vehicles each maintain their own `average_cost_price`.
- Average cost is recalculated from purchase receipt posting in the current phase.
- `last_cost_price` preserves the most recent received cost.
- Average cost is stored with 4 decimal places.

## Sales Rules

### Sales Order

- Sales order number must be unique.
- Sales order requires:
  - order date
  - customer
  - warehouse
  - status
- `delivery_date` is optional but cannot be earlier than `order_date`.
- Supported statuses:
  - `draft`
  - `confirmed`
  - `completed`
  - `cancelled`
- `total_amount` must preserve actual sales amount and cannot be negative.
- `total_amount` is synchronized from line totals.

### Sales Order Item

- Sales order items support:
  - `part`
  - `vehicle`
- User must choose one concrete item matching `item_type`.
- `quantity` must be at least 1.
- `unit_price` cannot be negative.
- `line_total` is calculated as `quantity * unit_price`.
- `item_code` and `item_name` are stored as a snapshot.
- Creating, updating, or deleting a line must recalculate parent `total_amount`.

## Current Limitations

- Stock module currently supports manual adjustment and purchase stock-in only.
- Sales order line items are implemented, but sales stock-out is not implemented yet.
- No automatic stock reservation or safety-stock alert logic is implemented.

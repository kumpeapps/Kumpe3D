# Inventory Management Implementation Summary

## Overview
Implemented a reservation-based inventory system where parts are reserved when orders are placed, but only removed from physical stock when scanned during fulfillment.

## Database Changes

### Migration 004: Order Item Fulfillment Tracking

**New Fields on `order_items` table:**
- `is_filled` (Boolean) - Indicates if all parts have been scanned/filled
- `filled_at` (DateTime) - When the item was completed
- `filled_by` (String) - Username of who completed it
- `scanned_parts` (Text/JSON) - Legacy tracking field
- `notes` (Text) - Fulfillment notes

**New Table: `order_item_parts`**
- Junction table linking order items to their required parts
- Tracks scanning status for each part
- Fields:
  - `order_item_id` → `order_items.id`
  - `part_id` → `parts.id`
  - `quantity` - How many of this part needed
  - `is_scanned` - Whether this part has been scanned
  - `scanned_at`, `scanned_by` - Audit fields

**To run migration:**
```bash
docker compose exec backend alembic upgrade head
```

## Code Components

### 1. Updated Models (`backend/app/db/models/order.py`)

**OrderItem model:**
- Added fulfillment tracking fields
- Added `parts` relationship to OrderItemPart

**New OrderItemPart model:**
- Tracks each part needed for an order item
- Manages scan status
- Links to Part model for details

### 2. Inventory Service (`backend/app/services/inventory_service.py`)

**Key Methods:**

```python
async def get_part_availability(part_id: int) -> Dict:
    """
    Returns:
        - physical_stock: Actual quantity on hand
        - reserved: Quantity reserved by unfilled orders
        - available: physical_stock - reserved
        - pending_orders: Count of orders waiting for this part
    """
```

```python
async def reserve_parts_for_order_item(order_item_id, product_id, selected_options):
    """
    Creates OrderItemPart records when order is placed.
    Call this in your order creation endpoint.
    """
```

```python
async def scan_part(order_item_part_id, scanned_by):
    """
    Marks part as scanned and decrements physical stock.
    Returns True if successful.
    """
```

```python
async def check_product_availability(product_id, quantity):
    """
    Checks if enough stock exists to fulfill product order.
    Returns available status and max_quantity possible.
    """
```

### 3. Fulfillment API (`backend/app/api/routes/fulfillment.py`)

**Endpoints:**

- `GET /api/v1/fulfillment/orders/pending`
  - Lists all orders with unfilled items
  - Shows fill progress for each order

- `GET /api/v1/fulfillment/orders/{order_id}/details`
  - Detailed view of order with all parts
  - Shows which parts are scanned/unscanned

- `POST /api/v1/fulfillment/scan-part/{order_item_part_id}`
  - Scans a part (marks as filled, decrements stock)
  - Auto-marks order item as filled when all parts scanned

- `POST /api/v1/fulfillment/unscan-part/{order_item_part_id}`
  - Undoes a scan (returns part to stock)
  - For error correction

- `GET /api/v1/fulfillment/orders/{order_id}/packing-slip`
  - Generates packing slip data
  - **Only includes filled items**

**Note:** You need to register this router in your main.py:
```python
from app.api.routes import fulfillment

app.include_router(fulfillment.router, prefix="/api/v1")
```

## Integration Points

### When Creating an Order

After creating OrderItem records, call the inventory service:

```python
from app.services.inventory_service import InventoryService

inventory = InventoryService(db)

for item in order.items:
    await inventory.reserve_parts_for_order_item(
        order_item_id=item.id,
        product_id=item.product_id,
        selected_options=[opt.id for opt in item.selected_options]  # If any
    )
```

### Before Adding to Cart

Check availability before allowing add-to-cart:

```python
inventory = InventoryService(db)
availability = await inventory.check_product_availability(
    product_id=product_id,
    quantity=quantity_requested
)

if not availability["available"]:
    raise HTTPException(
        status_code=400,
        detail=f"Insufficient stock. Max available: {availability['max_quantity']}"
    )
```

### Product List/Detail Pages

Show available quantity instead of physical stock:

```python
inventory = InventoryService(db)
availability = await inventory.get_part_availability(part_id)

# Display availability["available"] to customers
# Display availability["physical_stock"] in admin panel
```

## Frontend UI (To Be Built)

### Order Fulfillment Screen
**Location:** `/admin/orders/{id}/fulfill`

**Features Needed:**
1. List all order items with their parts
2. Show scan status for each part
3. Barcode scanner input or manual part selection
4. Real-time progress indicator
5. "Mark as Filled" button when all parts scanned
6. Print packing slip button (only works when items filled)

**UI Layout:**
```
Order #12345 - John Doe
Status: Processing | Created: 2026-01-03

[=========75%=========    ] 3 of 4 items filled

Item 1: Ancient Dragon Miniature ✓ FILLED
  ✓ PART-001 Dragon Body (Scanned by: jsmith at 10:30 AM)
  ✓ PART-002 Base (Scanned by: jsmith at 10:31 AM)

Item 2: Modern Tank Model ⏳ PENDING
  ✓ PART-003 Tank Hull (Scanned by: jsmith at 10:32 AM)
  ☐ PART-004 Turret (Not scanned)
  ☐ PART-005 Tracks (Not scanned)

[Scan Barcode] _______________ [Manual Select Part ▼]

[Print Packing Slip] (disabled until all filled)
```

### Inventory Dashboard
**Location:** `/admin/inventory`

**Show for each part:**
- Physical Stock
- Reserved (by pending orders)
- **Available** (physical - reserved) ← Highlight this
- Reorder needed indicator

## Testing Checklist

- [ ] Run migration 004 successfully
- [ ] Create test order with multiple items
- [ ] Verify OrderItemPart records created
- [ ] Test scan_part endpoint
- [ ] Verify physical stock decrements only on scan
- [ ] Check available stock calculation
- [ ] Test packing slip excludes unfilled items
- [ ] Test unscan_part (error correction)
- [ ] Verify product availability checks before order
- [ ] Test cart validation with insufficient stock

## Next Steps

1. ✅ Database migration created
2. ✅ Models updated
3. ✅ Inventory service implemented
4. ✅ Fulfillment API endpoints created
5. ⏳ Register fulfillment router in main.py
6. ⏳ Build Angular fulfillment UI
7. ⏳ Integrate reservation logic into order creation
8. ⏳ Update product availability display
9. ⏳ Add barcode scanning support
10. ⏳ Create packing slip PDF generator

## Questions/Decisions Needed

1. **Barcode Format:** What format for part barcodes? (Code 128, QR, etc.)
2. **Scanner Hardware:** USB scanner? Mobile app? Both?
3. **Partial Fulfillment:** Ship partially filled orders? Or all-or-nothing?
4. **Stock Alerts:** Email when parts low after reservations?
5. **Historical Data:** Keep scan history indefinitely? Archive old orders?

## Documentation

See `.github/copilot-instructions.md` for full inventory philosophy documentation.

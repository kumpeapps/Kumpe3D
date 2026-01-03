# Option Parts Feature Documentation

## Overview

The OptionPart feature allows product options to link to multiple inventory parts with support for OR relationships. This enables complex Bill of Materials (BOM) scenarios where a single option can consume multiple parts, or where alternative parts can be used interchangeably.

## Architecture

### Database Schema

**option_parts table:**
- `option_id` (INTEGER, FK to product_options.id, CASCADE DELETE)
- `part_id` (INTEGER, FK to parts.id, CASCADE DELETE)
- `quantity` (INTEGER, DEFAULT 1) - Number of units consumed
- `alternative_group` (INTEGER, NULLABLE) - Groups alternative parts together
- `priority` (INTEGER, DEFAULT 0) - Selection priority within alternative group
- `notes` (TEXT, NULLABLE) - Additional notes
- Composite Primary Key: (option_id, part_id)
- Index on alternative_group for query performance

### Models

**Python (backend/app/db/models/product.py):**
```python
class OptionPart(Base):
    __tablename__ = "option_parts"
    
    option_id: Mapped[int] = mapped_column(ForeignKey("product_options.id", ondelete="CASCADE"), primary_key=True)
    part_id: Mapped[int] = mapped_column(ForeignKey("parts.id", ondelete="CASCADE"), primary_key=True)
    quantity: Mapped[int] = mapped_column(default=1)
    alternative_group: Mapped[int | None] = mapped_column(index=True)
    priority: Mapped[int] = mapped_column(default=0)
    notes: Mapped[str | None]
    
    # Relationships
    option: Mapped["ProductOption"] = relationship(back_populates="parts")
    part: Mapped["Part"] = relationship(lazy="joined")
```

**TypeScript (frontend/src/app/core/models/product.model.ts):**
```typescript
export interface OptionPart {
  part_id: number;
  quantity: number;
  alternative_group?: number;
  priority?: number;
  notes?: string;
  part?: Part;  // Populated in API responses
}

export interface ProductOption {
  id?: number;
  name: string;
  option_group?: string;
  price_modifier: number;
  parts?: OptionPart[];  // Array of linked parts
  // ...other fields
}
```

## Use Cases

### Example 1: Simple Part Linkage
**Product Option:** Color: Red
- Parts: Red PLA filament (quantity: 100g)

```json
{
  "name": "Red",
  "option_group": "Color",
  "parts": [
    {
      "part_id": 5,
      "quantity": 100
    }
  ]
}
```

### Example 2: OR Relationships (Alternative Parts)
**Product Option:** Include Key: Yes
- Parts: 2" key (alternative_group=1, priority=1) OR 1" key (alternative_group=1, priority=2)

```json
{
  "name": "Yes",
  "option_group": "Include Key",
  "parts": [
    {
      "part_id": 10,
      "quantity": 1,
      "alternative_group": 1,
      "priority": 1
    },
    {
      "part_id": 11,
      "quantity": 1,
      "alternative_group": 1,
      "priority": 2
    }
  ]
}
```

**System behavior:** When this option is selected, the system will:
1. Check stock for 2" key (priority 1)
2. If available, use 2" key
3. If not available, use 1" key (priority 2)
4. If neither available, prevent order or alert customer

### Example 3: Multiple Parts Per Option
**Product Option:** Premium Packaging
- Parts: Gift box (1), Tissue paper (2), Ribbon (1), Thank you card (1)

```json
{
  "name": "Premium",
  "option_group": "Packaging",
  "parts": [
    {"part_id": 20, "quantity": 1},
    {"part_id": 21, "quantity": 2},
    {"part_id": 22, "quantity": 1},
    {"part_id": 23, "quantity": 1}
  ]
}
```

## UI Workflow

### Product Form - Option Parts Section

When editing a product option, you'll see:

1. **Option Details**: Name, group, price modifier, sort order
2. **Parts for this option**: Expandable section
   - Button: "Add Part"
   - For each part:
     - Part dropdown (shows part_number, name, stock)
     - Quantity field
     - OR Group field (optional number)
     - Delete button

### Creating OR Groups

To create alternative parts:
1. Add first part (e.g., 2" key)
2. Set OR Group = 1
3. Add second part (e.g., 1" key)
4. Set OR Group = 1 (same as first)
5. System recognizes these as alternatives

Parts with the same `alternative_group` value are treated as alternatives. The system will select based on:
1. Stock availability
2. Priority value (lower = higher priority)

## API Structure

### Create/Update Product with Option Parts

**POST /api/v1/products/**
```json
{
  "sku": "DRAGON-001",
  "title": "Mini Dragon",
  "base_price": 19.99,
  "options": [
    {
      "name": "Red",
      "option_group": "Color",
      "price_modifier": 0,
      "parts": [
        {
          "part_id": 5,
          "quantity": 100
        }
      ]
    },
    {
      "name": "Yes",
      "option_group": "Include Key",
      "price_modifier": 2.50,
      "parts": [
        {
          "part_id": 10,
          "quantity": 1,
          "alternative_group": 1,
          "priority": 1
        },
        {
          "part_id": 11,
          "quantity": 1,
          "alternative_group": 1,
          "priority": 2
        }
      ]
    }
  ]
}
```

### Get Product with Option Parts

**GET /api/v1/products/{id}**

Response includes nested parts:
```json
{
  "data": {
    "id": 1,
    "sku": "DRAGON-001",
    "options": [
      {
        "id": 1,
        "name": "Red",
        "parts": [
          {
            "part_id": 5,
            "quantity": 100,
            "part": {
              "id": 5,
              "part_number": "PLA-RED-1KG",
              "name": "Red PLA Filament",
              "stock_quantity": 5000
            }
          }
        ]
      }
    ]
  }
}
```

## Migration

### Applied Migration: 003_add_option_parts.sql

1. Creates `option_parts` table
2. Migrates existing `part_id` data from `product_options` to `option_parts`
3. Recreates `product_options` without `part_id` column
4. Adds indexes for performance

**Status:** ✅ Migration completed successfully

## Testing Checklist

- [ ] Create product with option linking to single part
- [ ] Create product with option linking to multiple parts
- [ ] Create product with OR group (alternative parts)
- [ ] Edit product option parts
- [ ] Delete option part
- [ ] Verify stock deduction when order placed
- [ ] Test alternative part selection when primary out of stock
- [ ] Verify API returns nested part details

## Future Enhancements

1. **Stock Reservation**: Reserve parts when order placed but not shipped
2. **Smart Selection**: Auto-select alternatives based on cost, supplier, or other criteria
3. **Bulk Operations**: Copy option parts across products
4. **Analytics**: Track which alternative parts are used most frequently
5. **Validation**: Warn if all alternatives are low stock

## Technical Notes

- **Eager Loading**: Product detail endpoint uses nested `selectinload()` to fetch all option parts in a single query
- **Cascade Delete**: Deleting an option or part automatically removes the OptionPart links
- **Indexing**: `alternative_group` column is indexed for faster OR group queries
- **Frontend**: Uses FormArray for dynamic parts management
- **Validation**: Quantity must be >= 1, part_id is required

## Related Files

**Backend:**
- [backend/app/db/models/product.py](../backend/app/db/models/product.py) - OptionPart model
- [backend/app/schemas/product.py](../backend/app/schemas/product.py) - OptionPart schemas
- [backend/app/api/v1/products.py](../backend/app/api/v1/products.py) - API endpoints
- [backend/migrations/003_add_option_parts.sql](../backend/migrations/003_add_option_parts.sql) - Database migration

**Frontend:**
- [frontend/src/app/core/models/product.model.ts](../frontend/src/app/core/models/product.model.ts) - TypeScript interfaces
- [frontend/src/app/features/admin/products/product-form-dialog.component.ts](../frontend/src/app/features/admin/products/product-form-dialog.component.ts) - UI component

## Support

For questions or issues, refer to:
- [GitHub Copilot Instructions](../.github/copilot-instructions.md)
- [Project README](../README.md)

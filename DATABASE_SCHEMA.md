# Database Schema Design - Kumpe3D

## Overview
This document defines the database schema for the Kumpe3D e-commerce platform. The schema is designed to be database-agnostic using SQLAlchemy ORM.

**Target Databases**: PostgreSQL (primary), MySQL (compatible)  
**ORM**: SQLAlchemy 2.0  
**Migrations**: Alembic

---

## Design Principles

1. **Database Agnostic**: Use SQLAlchemy types that work across databases
2. **Normalization**: Minimize data redundancy (3NF where practical)
3. **Indexing**: Index all foreign keys and frequently queried columns
4. **Constraints**: Use database-level constraints for data integrity
5. **Soft Deletes**: Preserve data with `is_active` flags where appropriate
6. **Audit Trails**: Track creation and modification times
7. **UUIDs**: Use auto-increment IDs for simplicity (compatible with legacy)

---

## Entity Relationship Diagram

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│    users    │───────│  user_roles  │───────│    roles    │
└─────────────┘       └──────────────┘       └─────────────┘
       │                                             │
       │                                             │
       │              ┌───────────────────┐          │
       └──────────────│  role_permissions │──────────┘
                      └───────────────────┘
                               │
                      ┌─────────────────┐
                      │  permissions    │
                      └─────────────────┘

┌──────────────┐      ┌─────────────────┐      ┌──────────────┐
│   products   │──────│ product_images  │      │  categories  │
└──────────────┘      └─────────────────┘      └──────────────┘
       │                                               │
       │                                               │
       └───────────────────┬───────────────────────────┘
                           │
                  ┌────────────────┐
                  │product_categories│
                  └────────────────┘

┌──────────────┐      ┌─────────────┐
│  filament    │      │  catalogs   │
└──────────────┘      └─────────────┘

┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│    users     │──────│  cart_items  │──────│  products    │
└──────────────┘      └──────────────┘      └──────────────┘

┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│    users     │──────│   orders     │──────│  addresses   │
└──────────────┘      └──────────────┘      └──────────────┘
                             │
                      ┌──────┴───────┐
                      │              │
              ┌───────────────┐ ┌─────────────────┐
              │ order_items   │ │ order_history   │
              └───────────────┘ └─────────────────┘

┌──────────────┐
│  countries   │
└──────────────┘

┌──────────────┐
│  zip_codes   │
└──────────────┘

┌──────────────────┐
│ site_parameters  │
└──────────────────┘
```

---

## Tables

### users
User accounts for customers and administrators.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | User ID |
| email | String(255) | Unique, Not Null | Email address (login) |
| username | String(100) | Unique, Nullable | Optional username |
| password_hash | String(255) | Not Null | Bcrypt hashed password |
| first_name | String(100) | Nullable | First name |
| last_name | String(100) | Nullable | Last name |
| is_active | Boolean | Default True | Account active status |
| is_verified | Boolean | Default False | Email verified |
| failed_login_attempts | Integer | Default 0 | Failed login counter |
| locked_until | DateTime | Nullable | Account lock expiration |
| last_login | DateTime | Nullable | Last successful login |
| created_at | DateTime | Default now() | Account creation time |
| updated_at | DateTime | Default now() | Last update time |

**Indexes**:
- `idx_users_email` on `email`
- `idx_users_username` on `username`
- `idx_users_is_active` on `is_active`

---

### roles
User roles for RBAC.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Role ID |
| name | String(50) | Unique, Not Null | Role name (admin, user, guest) |
| description | String(255) | Nullable | Role description |
| is_active | Boolean | Default True | Role active status |
| created_at | DateTime | Default now() | Creation time |

**Indexes**:
- `idx_roles_name` on `name`

**Seed Data**:
- `admin` - Full system access
- `user` - Registered user access
- `guest` - Public access only

---

### permissions
Granular permissions for RBAC.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Permission ID |
| name | String(100) | Unique, Not Null | Permission name |
| resource | String(50) | Not Null | Resource type (products, orders, users) |
| action | String(50) | Not Null | Action (create, read, update, delete) |
| description | String(255) | Nullable | Permission description |
| created_at | DateTime | Default now() | Creation time |

**Indexes**:
- `idx_permissions_name` on `name`
- `idx_permissions_resource` on `resource`

**Examples**:
- `products:create` - Create products
- `products:read` - View products
- `orders:read` - View orders
- `users:manage` - Manage users

---

### user_roles
Many-to-many relationship between users and roles.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| user_id | Integer | FK(users.id), Not Null | User ID |
| role_id | Integer | FK(roles.id), Not Null | Role ID |
| assigned_at | DateTime | Default now() | Assignment time |
| assigned_by | Integer | FK(users.id), Nullable | Admin who assigned |

**Primary Key**: (`user_id`, `role_id`)

**Indexes**:
- `idx_user_roles_user` on `user_id`
- `idx_user_roles_role` on `role_id`

---

### role_permissions
Many-to-many relationship between roles and permissions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| role_id | Integer | FK(roles.id), Not Null | Role ID |
| permission_id | Integer | FK(permissions.id), Not Null | Permission ID |
| created_at | DateTime | Default now() | Assignment time |

**Primary Key**: (`role_id`, `permission_id`)

**Indexes**:
- `idx_role_permissions_role` on `role_id`
- `idx_role_permissions_permission` on `permission_id`

---

### products
Product catalog.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Product ID |
| sku | String(20) | Unique, Not Null | SKU format: XXXXXXXXX-XXX-XXX |
| title | String(255) | Not Null | Product title |
| description | Text | Nullable | Product description |
| base_price | Decimal(10,2) | Not Null | Base price |
| cost | Decimal(10,2) | Default 0 | Product cost |
| weight | Decimal(10,2) | Nullable | Weight in ounces |
| is_active | Boolean | Default True | Product active status |
| featured | Boolean | Default False | Featured product |
| meta_title | String(255) | Nullable | SEO meta title |
| meta_description | String(500) | Nullable | SEO meta description |
| sort_order | Integer | Default 0 | Display sort order |
| created_at | DateTime | Default now() | Creation time |
| updated_at | DateTime | Default now() | Last update time |
| created_by | Integer | FK(users.id), Nullable | Creator user ID |
| updated_by | Integer | FK(users.id), Nullable | Last updater user ID |

**Indexes**:
- `idx_products_sku` on `sku`
- `idx_products_is_active` on `is_active`
- `idx_products_featured` on `featured`
- `idx_products_sort_order` on `sort_order`

**Notes**:
- SKU format: `{base_sku}-{filament_type}-{color_code}`
- Example: `123456789-PLA-001`
- **Stock quantity is calculated dynamically** based on parts inventory via `product_parts` table
- Products without parts associations have unlimited stock by default
- To get product stock: `SELECT MIN(parts.stock_quantity / product_parts.quantity) FROM parts JOIN product_parts WHERE product_parts.product_id = ? AND product_parts.is_optional = FALSE`

---

### product_images
Product photos and images.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Image ID |
| product_id | Integer | FK(products.id), Not Null | Product ID |
| file_path | String(500) | Not Null | Image file path/URL |
| alt_text | String(255) | Nullable | Alt text for accessibility |
| sort_order | Integer | Default 0 | Display order |
| is_primary | Boolean | Default False | Primary product image |
| created_at | DateTime | Default now() | Upload time |

**Indexes**:
- `idx_product_images_product` on `product_id`
- `idx_product_images_sort` on `sort_order`

---

### parts
Individual parts that make up products. Used for inventory management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Part ID |
| part_number | String(50) | Unique, Not Null | Part number/SKU |
| name | String(255) | Not Null | Part name |
| description | Text | Nullable | Part description |
| stock_quantity | Integer | Default 0 | Current stock quantity |
| low_stock_threshold | Integer | Default 5 | Low stock alert threshold |
| reorder_quantity | Integer | Default 10 | Suggested reorder quantity |
| unit_cost | Decimal(10,2) | Default 0 | Cost per unit |
| supplier | String(255) | Nullable | Supplier name |
| supplier_part_number | String(100) | Nullable | Supplier's part number |
| location | String(100) | Nullable | Storage location |
| notes | Text | Nullable | Additional notes |\n| is_active | Boolean | Default True | Part active status |
| created_at | DateTime | Default now() | Creation time |
| updated_at | DateTime | Default now() | Last update time |

**Indexes**:
- `idx_parts_part_number` on `part_number`
- `idx_parts_name` on `name`
- `idx_parts_stock` on `stock_quantity`
- `idx_parts_active` on `is_active`

**Notes**:
- Stock quantity is tracked at the part level
- Admin can view/manage parts inventory
- Low stock alerts trigger when `stock_quantity < low_stock_threshold`
- **Parts list is admin-only, not exposed to public API**

---

### product_parts
Many-to-many relationship defining which parts make up each product (Bill of Materials). Supports flexible part requirements including alternatives.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| product_id | Integer | FK(products.id), Not Null | Product ID |
| part_id | Integer | FK(parts.id), Not Null | Part ID |
| quantity | Integer | Not Null, Default 1 | Quantity of this part needed |
| is_optional | Boolean | Default False | Part is completely optional |
| alternative_group | Integer | Nullable | Group ID for alternative parts (OR) |
| priority | Integer | Default 0 | Preference order within alternative group |
| notes | String(500) | Nullable | Assembly notes |
| created_at | DateTime | Default now() | Association time |

**Primary Key**: (`product_id`, `part_id`)

**Indexes**:
- `idx_product_parts_product` on `product_id`
- `idx_product_parts_part` on `part_id`
- `idx_product_parts_alt_group` on `alternative_group`

**Notes**:
- **Flexible part requirements**:
  - **Simple required**: `alternative_group = NULL`, `is_optional = FALSE` - part is always required
  - **Optional**: `is_optional = TRUE` - part is optional (doesn't affect stock)
  - **Alternatives (OR)**: Same `alternative_group` number (e.g., 1, 2, 3) - only ONE part from group needed
  - `priority` determines preference when multiple alternatives available (higher = preferred)

- **Examples**:
  - **Product A** (requires part 1 AND part 2):
    - part 1: `alternative_group=NULL`, `is_optional=FALSE`
    - part 2: `alternative_group=NULL`, `is_optional=FALSE`
  
  - **Product B** (requires part 1 AND (part 2 OR part 3)):
    - part 1: `alternative_group=NULL`, `is_optional=FALSE`
    - part 2: `alternative_group=1`, `is_optional=FALSE`, `priority=1`
    - part 3: `alternative_group=1`, `is_optional=FALSE`, `priority=0`
  
  - **Product C** (requires only part 1):
    - part 1: `alternative_group=NULL`, `is_optional=FALSE`

- **Stock calculation logic**:
  1. Group parts by `alternative_group` (NULL parts are individual requirements)
  2. For each alternative group, take the part with highest stock (considering quantity needed)
  3. Calculate: `min(part.stock_quantity / product_parts.quantity)` across all groups
  4. Ignore optional parts in calculation

- **Example calculation** (Product B above, part 1 stock=10, part 2 stock=6, part 3 stock=4):
  - Required: part 1 (qty 1) = 10/1 = 10 units possible
  - Alternative group 1: max(part 2: 6/1, part 3: 4/1) = 6 units possible
  - Product stock = min(10, 6) = 6 units

- Admin interface shows parts list with grouping and alternatives
- **Public API does NOT expose parts information** - only shows calculated stock quantity

---

### categories
Product categories.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Category ID |
| name | String(100) | Unique, Not Null | Category name |
| slug | String(100) | Unique, Not Null | URL-friendly slug |
| description | Text | Nullable | Category description |
| photo | String(500) | Nullable | Category image path |
| parent_id | Integer | FK(categories.id), Nullable | Parent category (for hierarchy) |
| sort_order | Integer | Default 0 | Display order |
| is_active | Boolean | Default True | Category active status |
| created_at | DateTime | Default now() | Creation time |
| updated_at | DateTime | Default now() | Last update time |

**Indexes**:
- `idx_categories_slug` on `slug`
- `idx_categories_parent` on `parent_id`
- `idx_categories_sort` on `sort_order`
- `idx_categories_active` on `is_active`

---

### product_categories
Many-to-many relationship between products and categories.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| product_id | Integer | FK(products.id), Not Null | Product ID |
| category_id | Integer | FK(categories.id), Not Null | Category ID |
| created_at | DateTime | Default now() | Assignment time |

**Primary Key**: (`product_id`, `category_id`)

**Indexes**:
- `idx_product_categories_product` on `product_id`
- `idx_product_categories_category` on `category_id`

---

### catalogs
Product catalogs (collections).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Catalog ID |
| name | String(100) | Unique, Not Null | Catalog name |
| slug | String(100) | Unique, Not Null | URL-friendly slug |
| description | Text | Nullable | Catalog description |
| sort_order | Integer | Default 0 | Display order |
| is_active | Boolean | Default True | Catalog active status |
| created_at | DateTime | Default now() | Creation time |
| updated_at | DateTime | Default now() | Last update time |

**Indexes**:
- `idx_catalogs_slug` on `slug`
- `idx_catalogs_sort` on `sort_order`

---

### filament
Filament types and colors for 3D printing.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Filament ID |
| swatch_id | String(3) | Unique, Not Null | 3-digit color code |
| color_name | String(100) | Not Null | Color name |
| hex_color | String(7) | Nullable | Hex color code (#RRGGBB) |
| type | String(50) | Not Null | Filament type (PLA, ABS, PETG) |
| brand | String(100) | Nullable | Filament brand |
| is_active | Boolean | Default True | Available for use |
| sort_order | Integer | Default 0 | Display order |
| created_at | DateTime | Default now() | Creation time |
| updated_at | DateTime | Default now() | Last update time |

**Indexes**:
- `idx_filament_swatch` on `swatch_id`
- `idx_filament_type` on `type`
- `idx_filament_active` on `is_active`

---

### cart_items
Shopping cart items.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Cart item ID |
| session_id | String(255) | Not Null | Session ID (for guest carts) |
| user_id | Integer | FK(users.id), Nullable | User ID (for logged-in users) |
| product_id | Integer | FK(products.id), Not Null | Product ID |
| sku | String(20) | Not Null | Product SKU with filament |
| customization | Text | Nullable | Custom text/options |
| quantity | Integer | Not Null, Default 1 | Item quantity |
| price | Decimal(10,2) | Not Null | Price at time of add |
| created_at | DateTime | Default now() | Added to cart time |
| updated_at | DateTime | Default now() | Last update time |

**Indexes**:
- `idx_cart_items_session` on `session_id`
- `idx_cart_items_user` on `user_id`
- `idx_cart_items_product` on `product_id`
- `idx_cart_items_updated` on `updated_at`

**Notes**:
- Cart items expire after 30 days of inactivity
- Guest carts merge with user cart on login

---

### addresses
Shipping and billing addresses.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Address ID |
| user_id | Integer | FK(users.id), Nullable | User ID (null for guest) |
| first_name | String(100) | Not Null | First name |
| last_name | String(100) | Not Null | Last name |
| company_name | String(255) | Nullable | Company name |
| address_line1 | String(255) | Not Null | Street address |
| address_line2 | String(255) | Nullable | Apt/Suite/etc |
| city | String(100) | Not Null | City |
| state | String(2) | Not Null | State code (2-letter) |
| zip_code | String(10) | Not Null | ZIP/Postal code |
| country | String(2) | Not Null | Country code (ISO 3166-1 alpha-2) |
| phone | String(20) | Nullable | Phone number |
| is_default | Boolean | Default False | Default address |
| address_type | String(20) | Not Null | Type: shipping, billing, both |
| created_at | DateTime | Default now() | Creation time |
| updated_at | DateTime | Default now() | Last update time |

**Indexes**:
- `idx_addresses_user` on `user_id`
- `idx_addresses_zip` on `zip_code`

---

### orders
Customer orders.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Order ID |
| order_number | String(50) | Unique, Not Null | Order number |
| user_id | Integer | FK(users.id), Nullable | User ID (null for guest) |
| session_id | String(255) | Not Null | Session ID |
| email | String(255) | Not Null | Customer email |
| first_name | String(100) | Not Null | First name |
| last_name | String(100) | Not Null | Last name |
| company_name | String(255) | Nullable | Company name |
| shipping_address | Integer | FK(addresses.id), Nullable | Shipping address |
| billing_address | Integer | FK(addresses.id), Nullable | Billing address |
| subtotal | Decimal(10,2) | Not Null | Subtotal |
| tax_amount | Decimal(10,2) | Default 0 | Tax amount |
| shipping_amount | Decimal(10,2) | Default 0 | Shipping cost |
| discount_amount | Decimal(10,2) | Default 0 | Discount amount |
| total | Decimal(10,2) | Not Null | Total amount |
| status_id | Integer | Not Null, Default 1 | Order status (1=pending) |
| payment_method | String(50) | Nullable | Payment method (paypal) |
| payment_transaction_id | String(255) | Nullable | PayPal transaction ID |
| tracking_number | String(255) | Nullable | Shipping tracking number |
| carrier | String(50) | Nullable | Shipping carrier |
| notes | Text | Nullable | Order notes |
| client_ip | String(45) | Nullable | Customer IP address |
| client_browser | String(255) | Nullable | Customer browser |
| referral | String(500) | Nullable | Referral source |
| po_number | String(100) | Nullable | Purchase order number |
| so_number | String(100) | Nullable | Sales order number (Zoho) |
| invoice_number | String(100) | Nullable | Invoice number |
| created_at | DateTime | Default now() | Order date |
| updated_at | DateTime | Default now() | Last update time |
| shipped_at | DateTime | Nullable | Shipment date |
| delivered_at | DateTime | Nullable | Delivery date |

**Indexes**:
- `idx_orders_order_number` on `order_number`
- `idx_orders_user` on `user_id`
- `idx_orders_email` on `email`
- `idx_orders_status` on `status_id`
- `idx_orders_created` on `created_at`

**Order Status Values**:
1. Pending
2. Processing
3. Processed
4. Shipped
5. Delivered
6. Cancelled
7. Refunded

---

### order_items
Order line items.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Order item ID |
| order_id | Integer | FK(orders.id), Not Null | Order ID |
| product_id | Integer | FK(products.id), Nullable | Product ID |
| sku | String(20) | Not Null | Product SKU |
| title | String(255) | Not Null | Product title (snapshot) |
| customization | Text | Nullable | Custom text/options |
| quantity | Integer | Not Null | Quantity ordered |
| price | Decimal(10,2) | Not Null | Unit price |
| cost | Decimal(10,2) | Default 0 | Unit cost |
| subtotal | Decimal(10,2) | Not Null | Line item subtotal |
| created_at | DateTime | Default now() | Creation time |

**Indexes**:
- `idx_order_items_order` on `order_id`
- `idx_order_items_product` on `product_id`
- `idx_order_items_sku` on `sku`

---

### order_history
Order status change history.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | History ID |
| order_id | Integer | FK(orders.id), Not Null | Order ID |
| status_id | Integer | Not Null | New status ID |
| notes | Text | Nullable | Status change notes |
| updated_by | String(100) | Not Null | User/system that updated |
| created_at | DateTime | Default now() | Status change time |

**Indexes**:
- `idx_order_history_order` on `order_id`
- `idx_order_history_created` on `created_at`

---

### countries
Country data for shipping.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Country ID |
| name | String(100) | Not Null | Country name |
| iso2 | String(2) | Unique, Not Null | ISO 3166-1 alpha-2 code |
| iso3 | String(3) | Unique, Not Null | ISO 3166-1 alpha-3 code |
| currency | String(3) | Not Null | Currency code |
| currency_name | String(100) | Nullable | Currency name |
| currency_symbol | String(5) | Nullable | Currency symbol |
| emoji | String(10) | Nullable | Country flag emoji |
| us_sanctions | Boolean | Default False | Under US sanctions |
| high_risk | Boolean | Default False | High-risk country |
| packaging_restrictions | Boolean | Default False | Packaging restrictions |
| usps_block | Boolean | Default False | USPS shipping blocked |
| is_active | Boolean | Default True | Available for shipping |

**Indexes**:
- `idx_countries_iso2` on `iso2`
- `idx_countries_active` on `is_active`

---

### zip_codes
ZIP code database for address validation.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | ZIP code ID |
| zip | String(10) | Not Null | ZIP/Postal code |
| city | String(100) | Not Null | City name |
| state_id | String(2) | Not Null | State code |
| state_name | String(100) | Not Null | State name |
| county | String(100) | Nullable | County name |
| latitude | Decimal(10,7) | Nullable | Latitude |
| longitude | Decimal(10,7) | Nullable | Longitude |

**Indexes**:
- `idx_zip_codes_zip` on `zip`
- `idx_zip_codes_city` on `city`
- `idx_zip_codes_state` on `state_id`

---

### site_parameters
Site configuration parameters.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | Integer | PK, Auto-increment | Parameter ID |
| parameter | String(100) | Unique, Not Null | Parameter key (snake_case) |
| value | Text | Not Null | Parameter value |
| type | String(20) | Not Null | Data type (string, int, bool, json) |
| description | String(500) | Nullable | Parameter description |
| is_public | Boolean | Default False | Exposed to frontend |
| updated_at | DateTime | Default now() | Last update time |
| updated_by | Integer | FK(users.id), Nullable | Last updater |

**Indexes**:
- `idx_site_parameters_parameter` on `parameter`
- `idx_site_parameters_public` on `is_public`

**Example Parameters**:
- `site_name` - "Kumpe3D"
- `tax_rate_default` - "0.065"
- `shipping_free_threshold` - "50.00"
- `paypal_client_id` - PayPal client ID
- `low_stock_alert_enabled` - "true"

---

## Views (Optional)

### vw_products_with_inventory
Product list with calculated inventory status.

### vw_orders_summary
Order summary with customer and status information.

### vw_user_roles_permissions
Flattened view of user roles and permissions.

---

## Alembic Migration Strategy

### Initial Migration
- Create all tables with proper relationships
- Add indexes for performance
- Add constraints for data integrity

### Seed Data Migration
- Insert default roles (admin, user, guest)
- Insert default permissions
- Assign permissions to roles
- Insert countries data
- Insert ZIP codes data (if available)

### Data Migration from Legacy
- Map legacy `Web_3dprints` database to new schema
- Transform data to match new structure
- Preserve legacy IDs where possible for continuity

---

## Data Types by Database

### PostgreSQL
- String → VARCHAR
- Text → TEXT
- Integer → INTEGER
- Decimal → NUMERIC
- Boolean → BOOLEAN
- DateTime → TIMESTAMP WITH TIME ZONE

### MySQL
- String → VARCHAR
- Text → TEXT
- Integer → INT
- Decimal → DECIMAL
- Boolean → TINYINT(1)
- DateTime → DATETIME

SQLAlchemy handles these translations automatically.

---

## Performance Considerations

### Indexes
- All foreign keys indexed
- Frequently filtered columns indexed
- Sort columns indexed
- Unique constraints double as indexes

### Partitioning (Future)
- Consider partitioning `orders` by date for large datasets
- Consider partitioning `order_items` by order_id range

### Caching
- Cache site parameters
- Cache product catalog with Redis
- Cache user permissions

---

## Security

### Sensitive Data
- Password hashes use bcrypt with high work factor
- Never log passwords or tokens
- Payment transaction IDs stored securely
- PII (email, address) encrypted at rest (future consideration)

### Access Control
- All admin operations require RBAC checks
- Row-level security for user-specific data
- Audit trail in order_history

---

## Backup Strategy

### Daily Backups
- Full database backup
- Transaction log backup (PostgreSQL WAL)

### Retention
- Daily backups: 30 days
- Weekly backups: 90 days
- Monthly backups: 1 year

---

## Migration from Legacy

### Mapping
- `Web_3dprints.users` → `users`
- `Web_3dprints.products` → `products`
- `Web_3dprints.orders` → `orders`
- `Web_3dprints.orders__items` → `order_items`
- `Web_3dprints.orders__history` → `order_history`
- `Web_3dprints.cart__items` → `cart_items`
- `Public.countries` → `countries`
- `Public.vw_Addresses__Zips` → `zip_codes`

### Transformation
- Extract roles from user flags
- Create default permissions
- Normalize addresses
- Update SKU format if needed

---

## Future Enhancements

### Phase 2
- Add `product_reviews` table
- Add `wishlists` table
- Add `discount_codes` table
- Add `gift_cards` table

### Phase 3
- Add `subscriptions` table for recurring orders
- Add `notifications` table for user alerts
- Add `audit_logs` table for admin actions

---

**Last Updated**: January 1, 2026  
**Version**: 1.0  
**Status**: Draft - Ready for Implementation

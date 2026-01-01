export interface Product {
  id?: number;
  sku: string;
  title: string;  // Main product name
  description?: string;
  base_price: number;
  cost?: number;
  weight?: number;
  stock_quantity?: number;  // Calculated from parts
  is_active?: boolean;
  featured?: boolean;
  meta_title?: string;
  meta_description?: string;
  sort_order?: number;
  images?: ProductImage[];
  categories?: Category[];
  created_at?: string;
  updated_at?: string;
}

export interface ProductImage {
  id?: number;
  file_path: string;  // URL or path to the image
  alt_text?: string;
  sort_order?: number;
  is_primary?: boolean;
  created_at?: string;
}

export interface Category {
  id?: number;
  name: string;
  slug: string;
  description?: string;
  photo?: string;
  parent_id?: number;
  sort_order?: number;
  is_active?: boolean;
  created_at?: string;
}

export interface Part {
  id?: number;
  part_number: string;
  name: string;
  type?: string;  // Part type/category
  description?: string;
  stock_quantity?: number;
  low_stock_threshold?: number;
  reorder_quantity?: number;
  unit_cost?: number;
  price_modifier: number;  // Price adjustment for this part
  supplier?: string;
  supplier_part_number?: string;
  location?: string;
  notes?: string;
  alternative_group?: string;  // For OR relationships
  is_active?: boolean;
  is_low_stock?: boolean;
  created_at?: string;
  updated_at?: string;
}

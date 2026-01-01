export interface Product {
  id: number;
  sku: string;
  title: string;
  description?: string;
  base_price: number;
  cost: number;
  weight?: number;
  stock_quantity: number;
  is_active: boolean;
  featured: boolean;
  meta_title?: string;
  meta_description?: string;
  sort_order: number;
  images: ProductImage[];
  categories: Category[];
  created_at: string;
  updated_at: string;
}

export interface ProductImage {
  id: number;
  file_path: string;
  alt_text?: string;
  sort_order: number;
  is_primary: boolean;
  created_at: string;
}

export interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  photo?: string;
  parent_id?: number;
  sort_order: number;
  is_active: boolean;
  created_at: string;
}

export interface Part {
  id: number;
  part_number: string;
  name: string;
  description?: string;
  stock_quantity: number;
  low_stock_threshold: number;
  reorder_quantity: number;
  unit_cost: number;
  supplier?: string;
  supplier_part_number?: string;
  location?: string;
  notes?: string;
  is_active: boolean;
  is_low_stock: boolean;
  created_at: string;
  updated_at: string;
}

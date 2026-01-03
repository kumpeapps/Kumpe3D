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
  allow_order_when_out_of_stock?: boolean;
  meta_title?: string;
  meta_description?: string;
  sort_order?: number;
  images?: ProductImage[];
  primary_image?: string;  // URL of primary image
  categories?: Category[];
  options?: ProductOption[];  // Product options like colors, sizes
  created_at?: string;
  updated_at?: string;
}

export interface ProductOption {
  id?: number;
  product_id?: number;
  name: string;  // e.g., "Color: Red", "Size: Large"
  option_group?: string;  // e.g., "Color", "Size"
  price_modifier: number;  // Price adjustment for this option
  sort_order?: number;
  is_active?: boolean;
  parts?: OptionPart[];  // Parts linked to this option
  created_at?: string;
  updated_at?: string;
}

export interface OptionPart {
  part_id: number;
  quantity: number;
  alternative_group?: number;  // For OR relationships (same group = alternatives)
  priority?: number;
  notes?: string;
  part?: Part;  // Populated in responses
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
  icon?: string;
  parent_id?: number;
  sort_order?: number;
  is_active?: boolean;
  show_on_home?: boolean;
  created_at?: string;
}

export interface Part {
  id?: number;
  product_id?: number;
  name: string;
  option_group: string;  // e.g., "Color", "Size", "Material"
  price_modifier: number;  // Price adjustment for this option
  part_id?: number;  // Internal link to inventory part
  sort_order?: number;
  is_active?: boolean;
  created_at?: string;
  updated_at?: string;
}

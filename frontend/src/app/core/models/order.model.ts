export interface CartItem {
  id: number;
  product_id: number;
  quantity: number;
  price: number;
  selected_options?: string[];  // Array of selected option names
  customization_notes?: string;
  created_at: string;
  updated_at?: string;
  product?: {
    id: number;
    sku: string;
    title: string;
    base_price: number;
    stock_quantity?: number;
    images?: Array<{
      file_path: string;
      alt_text?: string;
    }>;
  };
}

export interface Order {
  id: number;
  order_number: string;
  email: string;
  first_name: string;
  last_name: string;
  company_name?: string;
  subtotal: number;
  tax_amount: number;
  shipping_amount: number;
  discount_amount: number;
  total: number;
  status_id: number;
  status_name: string;
  payment_method?: string;
  tracking_number?: string;
  carrier?: string;
  items: OrderItem[];
  shipping_address?: Address;
  created_at: string;
}

export interface OrderItem {
  id: number;
  sku: string;
  title: string;
  customization?: string;
  quantity: number;
  price: number;
  subtotal: number;
}

export interface Address {
  id: number;
  first_name: string;
  last_name: string;
  company_name?: string;
  address_line1: string;
  address_line2?: string;
  city: string;
  state: string;
  zip_code: string;
  country: string;
  phone?: string;
  address_type: string;
  is_default: boolean;
  created_at: string;
}

export interface CheckoutRequest {
  session_id: string;
  user_id?: number;
  shipping_address: Partial<Address>;
  billing_address?: Partial<Address>;
}

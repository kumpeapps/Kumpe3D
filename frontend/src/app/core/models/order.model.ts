export interface CartItem {
  id: number;
  product_id: number;
  sku: string;
  quantity: number;
  price: number | string;  // Can be string from backend Decimal
  selected_options?: number[];  // Array of selected option IDs
  customization_notes?: string;
  created_at: string;
  updated_at?: string;
  product_title?: string;
  product_image?: string;
  option_names?: string[];  // Names of selected options for display
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

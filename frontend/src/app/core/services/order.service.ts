import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface CreateOrderRequest {
  session_id?: string;
  email: string;
  first_name: string;
  last_name: string;
  company_name?: string;
  shipping_address: {
    first_name: string;
    last_name: string;
    company_name?: string;
    address_line1: string;
    address_line2?: string;
    city: string;
    state: string;
    zip_code: string;
    country: string;
    phone: string;
  };
  billing_address?: {
    first_name: string;
    last_name: string;
    company_name?: string;
    address_line1: string;
    address_line2?: string;
    city: string;
    state: string;
    zip_code: string;
    country: string;
    phone: string;
  };
  payment_transaction_id: string;
  notes?: string;
  client_ip?: string;
  client_browser?: string;
  referral?: string;
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
  phone: string;
}

export interface OrderResponse {
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
  updated_at: string;
}

interface APIResponse<T> {
  data: T;
  meta?: any;
  error?: any;
}

@Injectable({
  providedIn: 'root'
})
export class OrderService {
  private http = inject(HttpClient);
  private apiUrl = '/api/v1/orders/';

  createOrder(orderData: CreateOrderRequest): Observable<OrderResponse> {
    return this.http.post<APIResponse<OrderResponse>>(this.apiUrl, orderData)
      .pipe(
        map(response => response.data)
      );
  }

  getOrder(orderId: number): Observable<OrderResponse> {
    return this.http.get<APIResponse<OrderResponse>>(`${this.apiUrl}${orderId}`)
      .pipe(
        map(response => response.data)
      );
  }

  getOrderByNumber(orderNumber: string): Observable<OrderResponse> {
    return this.http.get<APIResponse<OrderResponse>>(`${this.apiUrl}number/${orderNumber}`)
      .pipe(
        map(response => response.data)
      );
  }

  getOrders(): Observable<OrderResponse[]> {
    return this.http.get<APIResponse<OrderResponse[]>>(this.apiUrl)
      .pipe(
        map(response => response.data)
      );
  }
}

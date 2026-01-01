import { Injectable, inject, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '@environments/environment';
import { CartItem, APIResponse } from '@core/models';
import { AuthService } from './auth.service';

export interface AddToCartRequest {
  product_id: number;
  quantity: number;
  selected_parts?: { [key: string]: number };
  customization_notes?: string;
}

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private http = inject(HttpClient);
  private authService = inject(AuthService);
  private apiUrl = `${environment.apiUrl}/cart`;

  // Cart state
  private cartItems = signal<CartItem[]>([]);
  
  // Computed cart totals
  items$ = computed(() => this.cartItems());
  itemCount$ = computed(() => this.cartItems().reduce((sum, item) => sum + item.quantity, 0));
  subtotal$ = computed(() => this.cartItems().reduce((sum, item) => sum + (item.price * item.quantity), 0));

  constructor() {
    // Load cart on init if user is authenticated
    this.authService.currentUser$.subscribe(user => {
      if (user) {
        this.loadCart().subscribe();
      }
    });
  }

  /**
   * Load cart from server
   */
  loadCart(): Observable<APIResponse<CartItem[]>> {
    return this.http.get<APIResponse<CartItem[]>>(this.apiUrl).pipe(
      tap(response => {
        if (response.data) {
          this.cartItems.set(response.data);
        }
      })
    );
  }

  /**
   * Add item to cart
   */
  addToCart(request: AddToCartRequest): Observable<APIResponse<CartItem>> {
    return this.http.post<APIResponse<CartItem>>(`${this.apiUrl}/items`, request).pipe(
      tap(() => {
        // Reload cart after adding
        this.loadCart().subscribe();
      })
    );
  }

  /**
   * Update cart item quantity
   */
  updateQuantity(itemId: number, quantity: number): Observable<APIResponse<CartItem>> {
    return this.http.put<APIResponse<CartItem>>(`${this.apiUrl}/items/${itemId}`, { quantity }).pipe(
      tap(() => {
        // Update local cart
        this.cartItems.update(items =>
          items.map(item => item.id === itemId ? { ...item, quantity } : item)
        );
      })
    );
  }

  /**
   * Remove item from cart
   */
  removeFromCart(itemId: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/items/${itemId}`).pipe(
      tap(() => {
        // Remove from local cart
        this.cartItems.update(items => items.filter(item => item.id !== itemId));
      })
    );
  }

  /**
   * Clear entire cart
   */
  clearCart(): Observable<void> {
    return this.http.delete<void>(this.apiUrl).pipe(
      tap(() => {
        this.cartItems.set([]);
      })
    );
  }

  /**
   * Merge guest cart with user cart on login
   */
  mergeCart(guestSessionId: string): Observable<APIResponse<CartItem[]>> {
    return this.http.post<APIResponse<CartItem[]>>(`${this.apiUrl}/merge`, { guest_session_id: guestSessionId }).pipe(
      tap(response => {
        if (response.data) {
          this.cartItems.set(response.data);
        }
      })
    );
  }
}

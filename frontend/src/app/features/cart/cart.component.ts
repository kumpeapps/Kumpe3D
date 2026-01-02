import { Component, inject, signal, computed, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { FormsModule } from '@angular/forms';
import { CartService } from '@core/services/cart.service';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    FormsModule,
    MatButtonModule,
    MatCardModule,
    MatIconModule,
    MatDividerModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
  ],
  template: `
    <div class="cart-container">
      <div class="cart-header">
        <h1>Shopping Cart</h1>
        @if (cartService.itemCount$() > 0) {
          <span class="item-count">{{ cartService.itemCount$() }} item(s)</span>
        }
      </div>

      @if (loading()) {
        <div class="loading">
          <mat-spinner></mat-spinner>
        </div>
      } @else if (cartService.items$().length === 0) {
        <div class="empty-cart">
          <mat-icon class="empty-icon">shopping_cart</mat-icon>
          <h2>Your cart is empty</h2>
          <p>Add some products to get started</p>
          <button mat-raised-button color="primary" routerLink="/products">
            <mat-icon>storefront</mat-icon>
            Browse Products
          </button>
        </div>
      } @else {
        <div class="cart-content">
          <div class="cart-items">
            @for (item of cartService.items$(); track item.id) {
              <mat-card class="cart-item">
                <div class="item-image">
                  <img [src]="item.product?.images?.[0]?.file_path || 'assets/placeholder.png'" 
                       [alt]="item.product?.title">
                </div>

                <div class="item-details">
                  <h3 class="item-title">
                    <a [routerLink]="['/products', item.product_id]">{{ item.product?.title }}</a>
                  </h3>
                  
                  @if (item.product?.sku) {
                    <p class="item-sku">SKU: {{ item.product.sku }}</p>
                  }

                  @if (item.selected_options && item.selected_options.length > 0) {
                    <div class="item-options">
                      <strong>Options:</strong>
                      <ul>
                        @for (option of item.selected_options; track option) {
                          <li>{{ option }}</li>
                        }
                      </ul>
                    </div>
                  }

                  @if (item.customization_notes) {
                    <div class="item-notes">
                      <strong>Notes:</strong>
                      <p>{{ item.customization_notes }}</p>
                    </div>
                  }
                </div>

                <div class="item-price">
                  <div class="price">\${{ item.price.toFixed(2) }}</div>
                  @if (item.price !== item.product?.base_price) {
                    <div class="base-price">\${{ (+item.product.base_price).toFixed(2) }}</div>
                  }
                </div>

                <div class="item-quantity">
                  <button mat-icon-button (click)="decrementQuantity(item)" [disabled]="updating() === item.id">
                    <mat-icon>remove</mat-icon>
                  </button>
                  <input 
                    type="number" 
                    [(ngModel)]="item.quantity" 
                    (blur)="updateQuantity(item)"
                    min="1"
                    [max]="item.product?.stock_quantity || 999"
                    [disabled]="updating() === item.id">
                  <button mat-icon-button (click)="incrementQuantity(item)" [disabled]="updating() === item.id">
                    <mat-icon>add</mat-icon>
                  </button>
                </div>

                <div class="item-subtotal">
                  <div class="subtotal-label">Subtotal</div>
                  <div class="subtotal-amount">\${{ (item.price * item.quantity).toFixed(2) }}</div>
                </div>

                <div class="item-actions">
                  <button 
                    mat-icon-button 
                    color="warn" 
                    (click)="removeItem(item)"
                    [disabled]="removing() === item.id"
                    matTooltip="Remove from cart">
                    @if (removing() === item.id) {
                      <mat-spinner diameter="20"></mat-spinner>
                    } @else {
                      <mat-icon>delete</mat-icon>
                    }
                  </button>
                </div>
              </mat-card>
            }
          </div>

          <div class="cart-summary">
            <mat-card>
              <h2>Order Summary</h2>
              
              <div class="summary-row">
                <span>Subtotal:</span>
                <span>\${{ cartService.subtotal$().toFixed(2) }}</span>
              </div>

              <div class="summary-row">
                <span>Shipping:</span>
                <span class="calculated-later">Calculated at checkout</span>
              </div>

              <div class="summary-row">
                <span>Tax:</span>
                <span class="calculated-later">Calculated at checkout</span>
              </div>

              <mat-divider></mat-divider>

              <div class="summary-row total">
                <span>Estimated Total:</span>
                <span>\${{ cartService.subtotal$().toFixed(2) }}</span>
              </div>

              <button 
                mat-raised-button 
                color="primary" 
                class="checkout-button"
                routerLink="/checkout"
                [disabled]="cartService.items$().length === 0">
                <mat-icon>payment</mat-icon>
                Proceed to Checkout
              </button>

              <button 
                mat-stroked-button 
                class="continue-shopping"
                routerLink="/products">
                <mat-icon>arrow_back</mat-icon>
                Continue Shopping
              </button>
            </mat-card>
          </div>
        </div>
      }
    </div>
  `,
  styles: [`
    .cart-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 24px;
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
    }

    .cart-header h1 {
      margin: 0;
    }

    .item-count {
      color: #666;
      font-size: 16px;
    }

    .loading {
      display: flex;
      justify-content: center;
      padding: 48px;
    }

    .empty-cart {
      text-align: center;
      padding: 64px 24px;
    }

    .empty-icon {
      font-size: 120px;
      width: 120px;
      height: 120px;
      color: #ccc;
      margin-bottom: 16px;
    }

    .empty-cart h2 {
      color: #666;
      margin-bottom: 8px;
    }

    .empty-cart p {
      color: #999;
      margin-bottom: 24px;
    }

    .cart-content {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 24px;
    }

    @media (max-width: 968px) {
      .cart-content {
        grid-template-columns: 1fr;
      }

      .cart-summary {
        order: 2;
      }

      .cart-items {
        order: 1;
      }
    }

    .cart-items {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .cart-item {
      display: grid;
      grid-template-columns: 100px 1fr 120px 150px 120px 48px;
      gap: 16px;
      align-items: center;
      padding: 16px !important;
    }

    @media (max-width: 768px) {
      .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 12px;
      }

      .item-price, .item-quantity, .item-subtotal {
        grid-column: 1 / -1;
      }

      .item-actions {
        grid-column: 2;
        grid-row: 1;
        justify-self: end;
      }
    }

    .item-image img {
      width: 100%;
      height: auto;
      object-fit: cover;
      border-radius: 4px;
    }

    .item-details {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .item-title {
      margin: 0;
      font-size: 18px;
      font-weight: 500;
    }

    .item-title a {
      color: inherit;
      text-decoration: none;
    }

    .item-title a:hover {
      color: #3f51b5;
    }

    .item-sku {
      margin: 0;
      font-size: 14px;
      color: #666;
    }

    .item-options ul {
      margin: 4px 0 0 0;
      padding-left: 20px;
      font-size: 14px;
    }

    .item-notes {
      font-size: 14px;
      color: #666;
    }

    .item-notes p {
      margin: 4px 0 0 0;
    }

    .item-price {
      text-align: right;
    }

    .price {
      font-size: 20px;
      font-weight: bold;
      color: #3f51b5;
    }

    .base-price {
      font-size: 14px;
      color: #999;
      text-decoration: line-through;
    }

    .item-quantity {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .item-quantity input {
      width: 60px;
      text-align: center;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }

    .item-quantity input::-webkit-inner-spin-button,
    .item-quantity input::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    .item-subtotal {
      text-align: right;
    }

    .subtotal-label {
      font-size: 12px;
      color: #666;
      margin-bottom: 4px;
    }

    .subtotal-amount {
      font-size: 18px;
      font-weight: 600;
    }

    .cart-summary h2 {
      margin-top: 0;
      margin-bottom: 24px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      font-size: 16px;
    }

    .summary-row.total {
      font-size: 20px;
      font-weight: bold;
      padding-top: 16px;
    }

    .calculated-later {
      color: #666;
      font-style: italic;
    }

    mat-divider {
      margin: 16px 0;
    }

    .checkout-button {
      width: 100%;
      height: 48px;
      font-size: 16px;
      margin-top: 24px;
    }

    .continue-shopping {
      width: 100%;
      height: 40px;
      margin-top: 12px;
    }
  `]
})
export class CartComponent implements OnInit {
  cartService = inject(CartService);
  private snackBar = inject(MatSnackBar);

  loading = signal(true);
  updating = signal<number | null>(null);
  removing = signal<number | null>(null);

  ngOnInit() {
    this.cartService.loadCart().subscribe({
      next: () => this.loading.set(false),
      error: (err) => {
        console.error('Error loading cart:', err);
        this.loading.set(false);
        this.snackBar.open('Error loading cart', 'Close', { duration: 3000 });
      }
    });
  }

  updateQuantity(item: any) {
    if (item.quantity < 1) {
      item.quantity = 1;
      return;
    }

    this.updating.set(item.id);
    this.cartService.updateQuantity(item.id, item.quantity).subscribe({
      next: () => {
        this.updating.set(null);
        this.snackBar.open('Cart updated', 'Close', { duration: 2000 });
      },
      error: (err) => {
        console.error('Error updating quantity:', err);
        this.updating.set(null);
        this.snackBar.open('Error updating cart', 'Close', { duration: 3000 });
      }
    });
  }

  incrementQuantity(item: any) {
    item.quantity++;
    this.updateQuantity(item);
  }

  decrementQuantity(item: any) {
    if (item.quantity > 1) {
      item.quantity--;
      this.updateQuantity(item);
    }
  }

  removeItem(item: any) {
    this.removing.set(item.id);
    this.cartService.removeFromCart(item.id).subscribe({
      next: () => {
        this.removing.set(null);
        this.snackBar.open('Item removed from cart', 'Close', { duration: 2000 });
      },
      error: (err) => {
        console.error('Error removing item:', err);
        this.removing.set(null);
        this.snackBar.open('Error removing item', 'Close', { duration: 3000 });
      }
    });
  }
}

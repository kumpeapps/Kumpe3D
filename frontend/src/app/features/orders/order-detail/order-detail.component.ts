import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { OrderService, OrderResponse } from '@core/services/order.service';

@Component({
  selector: 'app-order-detail',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatDividerModule,
    MatProgressSpinnerModule,
  ],
  template: `
    <div class="container">
      @if (loading()) {
        <div class="loading-container">
          <mat-spinner></mat-spinner>
          <p>Loading order details...</p>
        </div>
      } @else if (error()) {
        <mat-card class="error-card">
          <mat-card-header>
            <mat-icon color="warn">error</mat-icon>
            <mat-card-title>Order Not Found</mat-card-title>
          </mat-card-header>
          <mat-card-content>
            <p>{{ error() }}</p>
          </mat-card-content>
          <mat-card-actions>
            <button mat-raised-button color="primary" routerLink="/shop">Continue Shopping</button>
          </mat-card-actions>
        </mat-card>
      } @else if (order()) {
        <!-- Success Header -->
        <div class="success-header">
          <mat-icon class="success-icon">check_circle</mat-icon>
          <h1>Order Confirmed!</h1>
          <p class="order-number">Order #{{ order()!.order_number }}</p>
          <p class="thank-you">Thank you for your order. We'll send a confirmation email to <strong>{{ order()!.email }}</strong></p>
        </div>

        <!-- Order Summary Card -->
        <mat-card class="order-summary">
          <mat-card-header>
            <mat-card-title>Order Summary</mat-card-title>
            <mat-card-subtitle>{{ order()!.created_at | date:'medium' }}</mat-card-subtitle>
          </mat-card-header>
          <mat-card-content>
            <div class="summary-grid">
              <div class="summary-item">
                <span class="label">Status:</span>
                <span class="value">
                  <mat-icon class="status-icon">{{ getStatusIcon(order()!.status_id) }}</mat-icon>
                  {{ order()!.status_name }}
                </span>
              </div>
              <div class="summary-item">
                <span class="label">Payment Method:</span>
                <span class="value">{{ order()!.payment_method || 'PayPal' }}</span>
              </div>
              @if (order()!.tracking_number) {
                <div class="summary-item">
                  <span class="label">Tracking:</span>
                  <span class="value">{{ order()!.tracking_number }}</span>
                </div>
              }
            </div>
          </mat-card-content>
        </mat-card>

        <!-- Shipping Address -->
        <mat-card class="shipping-address">
          <mat-card-header>
            <mat-icon>local_shipping</mat-icon>
            <mat-card-title>Shipping Address</mat-card-title>
          </mat-card-header>
          <mat-card-content>
            @if (order()!.shipping_address) {
              <address>
                <strong>{{ order()!.shipping_address!.first_name }} {{ order()!.shipping_address!.last_name }}</strong><br>
                @if (order()!.shipping_address!.company_name) {
                  {{ order()!.shipping_address!.company_name }}<br>
                }
                {{ order()!.shipping_address!.address_line1 }}<br>
                @if (order()!.shipping_address!.address_line2) {
                  {{ order()!.shipping_address!.address_line2 }}<br>
                }
                {{ order()!.shipping_address!.city }}, {{ order()!.shipping_address!.state }} {{ order()!.shipping_address!.zip_code }}<br>
                {{ order()!.shipping_address!.country }}<br>
                <br>
                Phone: {{ order()!.shipping_address!.phone }}
              </address>
            }
          </mat-card-content>
        </mat-card>

        <!-- Order Items -->
        <mat-card class="order-items">
          <mat-card-header>
            <mat-card-title>Order Items</mat-card-title>
          </mat-card-header>
          <mat-card-content>
            <div class="items-list">
              @for (item of order()!.items; track item.id) {
                <div class="item">
                  <div class="item-details">
                    <strong>{{ item.title }}</strong>
                    <span class="sku">SKU: {{ item.sku }}</span>
                    @if (item.customization) {
                      <span class="customization">{{ item.customization }}</span>
                    }
                  </div>
                  <div class="item-quantity">
                    Qty: {{ item.quantity }}
                  </div>
                  <div class="item-price">
                    {{ item.price | currency }} each
                  </div>
                  <div class="item-subtotal">
                    {{ item.subtotal | currency }}
                  </div>
                </div>
                @if (!$last) {
                  <mat-divider></mat-divider>
                }
              }
            </div>
          </mat-card-content>
        </mat-card>

        <!-- Order Totals -->
        <mat-card class="order-totals">
          <mat-card-content>
            <div class="totals-grid">
              <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ order()!.subtotal | currency }}</span>
              </div>
              <div class="total-row">
                <span>Shipping:</span>
                <span>{{ order()!.shipping_amount | currency }}</span>
              </div>
              <div class="total-row">
                <span>Tax:</span>
                <span>{{ order()!.tax_amount | currency }}</span>
              </div>
              @if (order()!.discount_amount > 0) {
                <div class="total-row discount">
                  <span>Discount:</span>
                  <span>-{{ order()!.discount_amount | currency }}</span>
                </div>
              }
              <mat-divider></mat-divider>
              <div class="total-row grand-total">
                <span>Total:</span>
                <span>{{ order()!.total | currency }}</span>
              </div>
            </div>
          </mat-card-content>
        </mat-card>

        <!-- Actions -->
        <div class="actions">
          <button mat-raised-button color="primary" routerLink="/shop">
            <mat-icon>shopping_cart</mat-icon>
            Continue Shopping
          </button>
          <button mat-raised-button routerLink="/orders">
            <mat-icon>list</mat-icon>
            View All Orders
          </button>
        </div>
      }
    </div>
  `,
  styles: [`
    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 24px;
    }

    .loading-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 48px;
      gap: 16px;
    }

    .error-card {
      text-align: center;
      mat-icon {
        font-size: 48px;
        width: 48px;
        height: 48px;
        margin-bottom: 16px;
      }
    }

    .success-header {
      text-align: center;
      padding: 32px 24px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 8px;
      margin-bottom: 24px;

      .success-icon {
        font-size: 64px;
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        color: #4caf50;
      }

      h1 {
        margin: 0 0 8px;
        font-size: 32px;
      }

      .order-number {
        font-size: 20px;
        margin: 8px 0;
        font-weight: 500;
      }

      .thank-you {
        margin: 16px 0 0;
        opacity: 0.95;
      }
    }

    mat-card {
      margin-bottom: 16px;
    }

    mat-card-header {
      mat-icon {
        margin-right: 8px;
      }
    }

    .summary-grid {
      display: grid;
      gap: 16px;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      align-items: center;

      .label {
        color: #666;
      }

      .value {
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .status-icon {
        font-size: 20px;
        width: 20px;
        height: 20px;
      }
    }

    .shipping-address {
      mat-card-header mat-icon {
        color: #2196f3;
      }

      address {
        font-style: normal;
        line-height: 1.6;
      }
    }

    .items-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .item {
      display: grid;
      grid-template-columns: 2fr auto auto auto;
      gap: 16px;
      align-items: center;
      padding: 8px 0;

      .item-details {
        display: flex;
        flex-direction: column;
        gap: 4px;

        strong {
          font-size: 16px;
        }

        .sku {
          font-size: 14px;
          color: #666;
        }

        .customization {
          font-size: 14px;
          font-style: italic;
          color: #2196f3;
        }
      }

      .item-quantity,
      .item-price {
        text-align: right;
        color: #666;
      }

      .item-subtotal {
        text-align: right;
        font-weight: 600;
        font-size: 16px;
      }
    }

    .totals-grid {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-width: 400px;
      margin-left: auto;

      .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 16px;

        &.discount {
          color: #4caf50;
        }

        &.grand-total {
          font-size: 20px;
          font-weight: 600;
          color: #1976d2;
          padding-top: 12px;
        }
      }
    }

    .actions {
      display: flex;
      gap: 16px;
      justify-content: center;
      margin-top: 24px;

      button {
        padding: 12px 24px;
        
        mat-icon {
          margin-right: 8px;
        }
      }
    }

    @media (max-width: 768px) {
      .item {
        grid-template-columns: 1fr;
        gap: 8px;

        .item-quantity,
        .item-price,
        .item-subtotal {
          text-align: left;
        }
      }

      .actions {
        flex-direction: column;
      }
    }
  `]
})
export class OrderDetailComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private orderService = inject(OrderService);

  order = signal<OrderResponse | null>(null);
  loading = signal(true);
  error = signal<string | null>(null);

  ngOnInit() {
    const orderIdentifier = this.route.snapshot.paramMap.get('id');
    if (!orderIdentifier) {
      this.error.set('Invalid order identifier');
      this.loading.set(false);
      return;
    }

    // Try to determine if it's an order number (contains letters) or ID (only numbers)
    const isOrderNumber = /[A-Z]/.test(orderIdentifier);

    const orderRequest = isOrderNumber 
      ? this.orderService.getOrderByNumber(orderIdentifier)
      : this.orderService.getOrder(parseInt(orderIdentifier, 10));

    orderRequest.subscribe({
      next: (order) => {
        this.order.set(order);
        this.loading.set(false);
      },
      error: (err) => {
        console.error('Error loading order:', err);
        this.error.set(err.error?.error?.message || 'Failed to load order details');
        this.loading.set(false);
      }
    });
  }

  getStatusIcon(statusId: number): string {
    const icons: Record<number, string> = {
      1: 'pending',
      2: 'autorenew',
      3: 'check_circle',
      4: 'local_shipping',
      5: 'done_all',
      6: 'cancel',
      7: 'money_off',
    };
    return icons[statusId] || 'help';
  }
}


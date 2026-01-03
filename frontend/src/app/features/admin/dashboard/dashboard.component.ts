import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatGridListModule } from '@angular/material/grid-list';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { HttpClient } from '@angular/common/http';

interface DashboardStats {
  total_orders: number;
  pending_orders: number;
  total_products: number;
  active_products: number;
  total_revenue: number;
  recent_orders: Array<{
    order_number: string;
    customer: string;
    total: number;
    status: string;
  }>;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatGridListModule,
    MatProgressSpinnerModule,
  ],
  template: `
    <div class="container">
      <div class="header">
        <h1>Admin Dashboard</h1>
        <div class="quick-actions">
          <button mat-raised-button color="primary" routerLink="/admin/products">
            <mat-icon>inventory</mat-icon>
            Manage Products
          </button>
          <button mat-raised-button color="accent" routerLink="/admin/orders">
            <mat-icon>receipt</mat-icon>
            View Orders
          </button>
        </div>
      </div>

      @if (loading()) {
        <div class="loading">
          <mat-spinner></mat-spinner>
        </div>
      } @else {
        <!-- Stats Cards -->
        <div class="stats-grid">
          <mat-card class="stat-card orders">
            <mat-card-content>
              <div class="stat-icon">
                <mat-icon>shopping_cart</mat-icon>
              </div>
              <div class="stat-info">
                <h2>{{ stats()?.total_orders || 0 }}</h2>
                <p>Total Orders</p>
                @if (stats()?.pending_orders) {
                  <span class="badge">{{ stats()!.pending_orders }} pending</span>
                }
              </div>
            </mat-card-content>
          </mat-card>

          <mat-card class="stat-card products">
            <mat-card-content>
              <div class="stat-icon">
                <mat-icon>inventory_2</mat-icon>
              </div>
              <div class="stat-info">
                <h2>{{ stats()?.total_products || 0 }}</h2>
                <p>Total Products</p>
                @if (stats()?.active_products !== undefined) {
                  <span class="badge">{{ stats()!.active_products }} active</span>
                }
              </div>
            </mat-card-content>
          </mat-card>

          <mat-card class="stat-card revenue">
            <mat-card-content>
              <div class="stat-icon">
                <mat-icon>attach_money</mat-icon>
              </div>
              <div class="stat-info">
                <h2>{{ (stats()?.total_revenue || 0) | currency }}</h2>
                <p>Total Revenue</p>
              </div>
            </mat-card-content>
          </mat-card>
        </div>

        <!-- Recent Orders -->
        <mat-card class="recent-orders">
          <mat-card-header>
            <mat-card-title>Recent Orders</mat-card-title>
            <button mat-button routerLink="/admin/orders">View All</button>
          </mat-card-header>
          <mat-card-content>
            @if (stats()?.recent_orders && stats()!.recent_orders.length > 0) {
              <div class="orders-list">
                @for (order of stats()!.recent_orders; track order.order_number) {
                  <div class="order-item">
                    <div class="order-info">
                      <strong>{{ order.order_number }}</strong>
                      <span class="customer">{{ order.customer }}</span>
                    </div>
                    <div class="order-details">
                      <span class="status">{{ order.status }}</span>
                      <span class="amount">{{ order.total | currency }}</span>
                    </div>
                  </div>
                }
              </div>
            } @else {
              <p class="no-orders">No orders yet</p>
            }
          </mat-card-content>
        </mat-card>

        <!-- Quick Links -->
        <div class="quick-links">
          <mat-card routerLink="/admin/products">
            <mat-icon>add_box</mat-icon>
            <h3>Add Product</h3>
          </mat-card>
          <mat-card routerLink="/admin/users">
            <mat-icon>people</mat-icon>
            <h3>Manage Users</h3>
          </mat-card>
          <mat-card routerLink="/admin/parts">
            <mat-icon>build</mat-icon>
            <h3>Inventory</h3>
          </mat-card>
        </div>
      }
    </div>
  `,
  styles: [`
    .container {
      padding: 24px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;

      h1 {
        margin: 0;
      }

      .quick-actions {
        display: flex;
        gap: 12px;

        button mat-icon {
          margin-right: 8px;
        }
      }
    }

    .loading {
      display: flex;
      justify-content: center;
      padding: 48px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
      margin-bottom: 24px;
    }

    .stat-card {
      mat-card-content {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 24px !important;
      }

      .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;

        mat-icon {
          font-size: 32px;
          width: 32px;
          height: 32px;
          color: white;
        }
      }

      .stat-info {
        flex: 1;

        h2 {
          margin: 0 0 4px;
          font-size: 32px;
          font-weight: 600;
        }

        p {
          margin: 0;
          color: #666;
          font-size: 14px;
        }

        .badge {
          display: inline-block;
          margin-top: 8px;
          padding: 4px 12px;
          background: rgba(13, 119, 94, 0.1);
          color: var(--kumpe-primary);
          border-radius: 12px;
          font-size: 12px;
          font-weight: 500;
        }
      }

      &.orders .stat-icon {
        background: linear-gradient(135deg, var(--kumpe-primary) 0%, var(--kumpe-primary-dark) 100%);
      }

      &.products .stat-icon {
        background: linear-gradient(135deg, #2da38a 0%, var(--kumpe-primary) 100%);
      }

      &.revenue .stat-icon {
        background: linear-gradient(135deg, var(--kumpe-success) 0%, var(--kumpe-primary) 100%);
      }
    }

    .recent-orders {
      margin-bottom: 24px;

      mat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
      }

      .orders-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: #f5f5f5;
        border-radius: 8px;
        transition: background 0.2s;

        &:hover {
          background: #eeeeee;
        }

        .order-info {
          display: flex;
          flex-direction: column;
          gap: 4px;

          strong {
            font-size: 16px;
          }

          .customer {
            color: #666;
            font-size: 14px;
          }
        }

        .order-details {
          display: flex;
          align-items: center;
          gap: 16px;

          .status {
            padding: 4px 12px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
          }

          .amount {
            font-size: 18px;
            font-weight: 600;
            color: var(--kumpe-primary);
          }
        }
      }

      .no-orders {
        text-align: center;
        padding: 32px;
        color: #999;
      }
    }

    .quick-links {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;

      mat-card {
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;

        &:hover {
          transform: translateY(-4px);
          box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        mat-icon {
          font-size: 48px;
          width: 48px;
          height: 48px;
          color: var(--kumpe-primary);
          margin-bottom: 12px;
        }

        h3 {
          margin: 0;
          font-size: 16px;
          font-weight: 500;
        }
      }
    }

    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
      }

      .quick-actions {
        width: 100%;
        flex-direction: column;

        button {
          width: 100%;
        }
      }

      .order-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
    }
  `]
})
export class DashboardComponent implements OnInit {
  private http = inject(HttpClient);
  
  stats = signal<DashboardStats | null>(null);
  loading = signal(true);

  ngOnInit() {
    this.loadStats();
  }

  loadStats() {
    this.http.get<any>('/api/v1/admin/stats/dashboard').subscribe({
      next: (response) => {
        this.stats.set(response.data);
        this.loading.set(false);
      },
      error: (error) => {
        console.error('Error loading stats:', error);
        // Set default empty stats
        this.stats.set({
          total_orders: 0,
          pending_orders: 0,
          total_products: 0,
          active_products: 0,
          total_revenue: 0,
          recent_orders: []
        });
        this.loading.set(false);
      }
    });
  }
}

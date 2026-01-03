import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { MatChipsModule } from '@angular/material/chips';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

interface Order {
  id: number;
  order_number: string;
  email: string;
  first_name: string;
  last_name: string;
  total: number;
  status_id: number;
  status_name: string;
  created_at: string;
  payment_method?: string;
}

interface OrderListResponse {
  data: Order[];
  meta: {
    page: number;
    per_page: number;
    total: number;
  };
}

@Component({
  selector: 'app-admin-order-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    FormsModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatCardModule,
    MatChipsModule,
    MatProgressSpinnerModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatSnackBarModule,
    MatPaginatorModule,
  ],
  template: `
    <div class="container">
      <div class="header">
        <h1>Order Management</h1>
      </div>

      <!-- Filters -->
      <mat-card class="filters">
        <div class="filter-row">
          <mat-form-field appearance="outline">
            <mat-label>Search</mat-label>
            <input matInput [(ngModel)]="searchQuery" (ngModelChange)="onSearchChange()" placeholder="Order number, email, name...">
            <mat-icon matPrefix>search</mat-icon>
          </mat-form-field>

          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select [(ngModel)]="statusFilter" (ngModelChange)="loadOrders()">
              <mat-option [value]="null">All Statuses</mat-option>
              <mat-option [value]="1">Pending</mat-option>
              <mat-option [value]="2">Processing</mat-option>
              <mat-option [value]="3">Processed</mat-option>
              <mat-option [value]="4">Shipped</mat-option>
              <mat-option [value]="5">Delivered</mat-option>
              <mat-option [value]="6">Cancelled</mat-option>
              <mat-option [value]="7">Refunded</mat-option>
            </mat-select>
          </mat-form-field>
        </div>
      </mat-card>

      @if (loading()) {
        <div class="loading">
          <mat-spinner></mat-spinner>
        </div>
      } @else {
        <mat-card>
          <table mat-table [dataSource]="orders()" class="orders-table">
            <!-- Order Number Column -->
            <ng-container matColumnDef="order_number">
              <th mat-header-cell *matHeaderCellDef>Order #</th>
              <td mat-cell *matCellDef="let order">
                <a [routerLink]="['/orders', order.order_number]" class="order-link">
                  {{ order.order_number }}
                </a>
              </td>
            </ng-container>

            <!-- Customer Column -->
            <ng-container matColumnDef="customer">
              <th mat-header-cell *matHeaderCellDef>Customer</th>
              <td mat-cell *matCellDef="let order">
                <div class="customer-info">
                  <strong>{{ order.first_name }} {{ order.last_name }}</strong>
                  <span class="email">{{ order.email }}</span>
                </div>
              </td>
            </ng-container>

            <!-- Date Column -->
            <ng-container matColumnDef="created_at">
              <th mat-header-cell *matHeaderCellDef>Date</th>
              <td mat-cell *matCellDef="let order">
                {{ order.created_at | date:'short' }}
              </td>
            </ng-container>

            <!-- Total Column -->
            <ng-container matColumnDef="total">
              <th mat-header-cell *matHeaderCellDef>Total</th>
              <td mat-cell *matCellDef="let order">
                <strong>{{ order.total | currency }}</strong>
              </td>
            </ng-container>

            <!-- Status Column -->
            <ng-container matColumnDef="status">
              <th mat-header-cell *matHeaderCellDef>Status</th>
              <td mat-cell *matCellDef="let order">
                <mat-chip [class]="'status-' + order.status_id">
                  {{ order.status_name }}
                </mat-chip>
              </td>
            </ng-container>

            <!-- Payment Column -->
            <ng-container matColumnDef="payment">
              <th mat-header-cell *matHeaderCellDef>Payment</th>
              <td mat-cell *matCellDef="let order">
                {{ order.payment_method || 'PayPal' }}
              </td>
            </ng-container>

            <!-- Actions Column -->
            <ng-container matColumnDef="actions">
              <th mat-header-cell *matHeaderCellDef>Actions</th>
              <td mat-cell *matCellDef="let order">
                <button mat-icon-button [routerLink]="['/orders', order.order_number]" title="View Order">
                  <mat-icon>visibility</mat-icon>
                </button>
              </td>
            </ng-container>

            <tr mat-header-row *matHeaderRowDef="displayedColumns"></tr>
            <tr mat-row *matRowDef="let row; columns: displayedColumns;"></tr>
          </table>

          <!-- Pagination -->
          <mat-paginator 
            [length]="totalOrders()"
            [pageSize]="pageSize"
            [pageSizeOptions]="[10, 25, 50, 100]"
            (page)="onPageChange($event)"
            showFirstLastButtons>
          </mat-paginator>
        </mat-card>
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
      margin-bottom: 24px;

      h1 {
        margin: 0;
      }
    }

    .filters {
      margin-bottom: 24px;
      padding: 16px;

      .filter-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 16px;
      }

      mat-form-field {
        width: 100%;
      }
    }

    .loading {
      display: flex;
      justify-content: center;
      padding: 48px;
    }

    .orders-table {
      width: 100%;
    }

    .order-link {
      color: var(--kumpe-primary);
      text-decoration: none;
      font-weight: 500;

      &:hover {
        text-decoration: underline;
      }
    }

    .customer-info {
      display: flex;
      flex-direction: column;
      gap: 4px;

      .email {
        font-size: 12px;
        color: #666;
      }
    }

    mat-chip {
      font-size: 12px;
      min-height: 24px;

      &.status-1 { background: #fff3e0; color: #e65100; } // Pending
      &.status-2 { background: #e3f2fd; color: #1565c0; } // Processing
      &.status-3 { background: #e8f5e9; color: #2e7d32; } // Processed
      &.status-4 { background: #f3e5f5; color: #6a1b9a; } // Shipped
      &.status-5 { background: #c8e6c9; color: #1b5e20; } // Delivered
      &.status-6 { background: #ffebee; color: #c62828; } // Cancelled
      &.status-7 { background: #fce4ec; color: #ad1457; } // Refunded
    }

    @media (max-width: 768px) {
      .filter-row {
        grid-template-columns: 1fr !important;
      }

      .orders-table {
        font-size: 12px;
      }
    }
  `]
})
export class AdminOrderListComponent implements OnInit {
  private http = inject(HttpClient);
  private snackBar = inject(MatSnackBar);

  orders = signal<Order[]>([]);
  loading = signal(true);
  totalOrders = signal(0);
  
  displayedColumns = ['order_number', 'customer', 'created_at', 'total', 'status', 'payment', 'actions'];
  
  searchQuery = '';
  statusFilter: number | null = null;
  currentPage = 1;
  pageSize = 25;
  
  private searchTimeout: any;

  ngOnInit() {
    this.loadOrders();
  }

  loadOrders() {
    this.loading.set(true);
    
    let url = `/api/v1/orders/admin/all?page=${this.currentPage}&per_page=${this.pageSize}`;
    if (this.searchQuery) {
      url += `&search=${encodeURIComponent(this.searchQuery)}`;
    }
    if (this.statusFilter !== null) {
      url += `&status_id=${this.statusFilter}`;
    }

    this.http.get<OrderListResponse>(url).subscribe({
      next: (response) => {
        this.orders.set(response.data);
        this.totalOrders.set(response.meta.total);
        this.loading.set(false);
      },
      error: (error) => {
        console.error('Error loading orders:', error);
        this.snackBar.open('Failed to load orders', 'Close', { duration: 3000 });
        this.loading.set(false);
      }
    });
  }

  onSearchChange() {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => {
      this.currentPage = 1;
      this.loadOrders();
    }, 500);
  }

  onPageChange(event: PageEvent) {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadOrders();
  }
}

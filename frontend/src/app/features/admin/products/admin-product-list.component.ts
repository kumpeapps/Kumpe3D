import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { MatChipsModule } from '@angular/material/chips';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { ProductService } from '@core/services/product.service';
import { Product } from '@core/models';

@Component({
  selector: 'app-admin-product-list',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatCardModule,
    MatChipsModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
  ],
  template: `
    <div class="container">
      <div class="header">
        <h1>Product Management</h1>
        <button mat-raised-button color="primary" (click)="createProduct()">
          <mat-icon>add</mat-icon>
          Create Product
        </button>
      </div>

      @if (loading()) {
        <div class="loading">
          <mat-spinner></mat-spinner>
        </div>
      } @else {
        <mat-card>
          <table mat-table [dataSource]="products()" class="products-table">
            <!-- Image Column -->
            <ng-container matColumnDef="image">
              <th mat-header-cell *matHeaderCellDef>Image</th>
              <td mat-cell *matCellDef="let product">
                @if (product.primary_image) {
                  <img [src]="product.primary_image" [alt]="product.title" class="product-image">
                } @else {
                  <div class="no-image">
                    <mat-icon>image</mat-icon>
                  </div>
                }
              </td>
            </ng-container>

            <!-- SKU Column -->
            <ng-container matColumnDef="sku">
              <th mat-header-cell *matHeaderCellDef>SKU</th>
              <td mat-cell *matCellDef="let product">{{ product.sku }}</td>
            </ng-container>

            <!-- Title Column -->
            <ng-container matColumnDef="title">
              <th mat-header-cell *matHeaderCellDef>Title</th>
              <td mat-cell *matCellDef="let product">
                <strong>{{ product.title }}</strong>
                @if (product.featured) {
                  <mat-chip class="featured-chip">Featured</mat-chip>
                }
              </td>
            </ng-container>

            <!-- Price Column -->
            <ng-container matColumnDef="base_price">
              <th mat-header-cell *matHeaderCellDef>Price</th>
              <td mat-cell *matCellDef="let product">{{ product.base_price | currency }}</td>
            </ng-container>

            <!-- Stock Column -->
            <ng-container matColumnDef="stock">
              <th mat-header-cell *matHeaderCellDef>Stock</th>
              <td mat-cell *matCellDef="let product">
                @if (product.allow_order_when_out_of_stock) {
                  <mat-chip color="primary">MTO</mat-chip>
                } @else {
                  <span>In Stock</span>
                }
              </td>
            </ng-container>

            <!-- Status Column -->
            <ng-container matColumnDef="is_active">
              <th mat-header-cell *matHeaderCellDef>Status</th>
              <td mat-cell *matCellDef="let product">
                @if (product.is_active) {
                  <mat-chip color="accent">Active</mat-chip>
                } @else {
                  <mat-chip>Inactive</mat-chip>
                }
              </td>
            </ng-container>

            <!-- Actions Column -->
            <ng-container matColumnDef="actions">
              <th mat-header-cell *matHeaderCellDef>Actions</th>
              <td mat-cell *matCellDef="let product">
                <button mat-icon-button (click)="editProduct(product)">
                  <mat-icon>edit</mat-icon>
                </button>
                <button mat-icon-button color="warn" (click)="deleteProduct(product)">
                  <mat-icon>delete</mat-icon>
                </button>
              </td>
            </ng-container>

            <tr mat-header-row *matHeaderRowDef="displayedColumns"></tr>
            <tr mat-row *matRowDef="let row; columns: displayedColumns;"></tr>
          </table>
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

      button mat-icon {
        margin-right: 8px;
      }
    }

    .loading {
      display: flex;
      justify-content: center;
      padding: 48px;
    }

    .products-table {
      width: 100%;
    }

    .product-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 4px;
    }

    .no-image {
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
      border-radius: 4px;

      mat-icon {
        color: #ccc;
      }
    }

    .featured-chip {
      margin-left: 8px;
      font-size: 11px;
      min-height: 20px;
    }

    mat-chip {
      font-size: 12px;
    }
  `]
})
export class AdminProductListComponent implements OnInit {
  private productService = inject(ProductService);
  private router = inject(Router);
  private snackBar = inject(MatSnackBar);

  products = signal<Product[]>([]);
  loading = signal(true);
  displayedColumns = ['image', 'sku', 'title', 'base_price', 'stock', 'is_active', 'actions'];

  ngOnInit() {
    this.loadProducts();
  }

  loadProducts() {
    this.loading.set(true);
    this.productService.getProducts({ per_page: 100 }).subscribe({
      next: (response) => {
        this.products.set(response.data || []);
        this.loading.set(false);
      },
      error: (error) => {
        console.error('Error loading products:', error);
        this.snackBar.open('Failed to load products', 'Close', { duration: 3000 });
        this.loading.set(false);
      }
    });
  }

  createProduct() {
    this.router.navigate(['/admin/products/new']);
  }

  editProduct(product: Product) {
    this.router.navigate(['/admin/products', product.id, 'edit']);
  }

  deleteProduct(product: Product) {
    if (confirm(`Are you sure you want to delete "${product.title}"?`)) {
      // TODO: Implement delete product API call
      this.snackBar.open('Delete functionality coming soon', 'Close', { duration: 3000 });
    }
  }
}

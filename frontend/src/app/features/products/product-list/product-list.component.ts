import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatChipsModule } from '@angular/material/chips';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { ProductService } from '@core/services/product.service';
import { Product, Category } from '@core/models';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    FormsModule,
    MatCardModule,
    MatButtonModule,
    MatInputModule,
    MatFormFieldModule,
    MatSelectModule,
    MatPaginatorModule,
    MatChipsModule,
    MatIconModule,
    MatProgressSpinnerModule,
  ],
  template: `
    <div class="product-list-container">
      <div class="header">
        <h1>Products</h1>
        
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Search</mat-label>
            <input matInput [(ngModel)]="searchQuery" (ngModelChange)="onSearchChange()" placeholder="Search products...">
            <mat-icon matSuffix>search</mat-icon>
          </mat-form-field>

          <mat-form-field appearance="outline">
            <mat-label>Category</mat-label>
            <mat-select [(ngModel)]="selectedCategory" (ngModelChange)="onFilterChange()">
              <mat-option [value]="null">All Categories</mat-option>
              @for (category of categories(); track category.id) {
                <mat-option [value]="category.id">{{ category.name }}</mat-option>
              }
            </mat-select>
          </mat-form-field>

          <mat-chip-set>
            <mat-chip (click)="toggleInStockFilter()" [highlighted]="inStockOnly">
              In Stock Only
            </mat-chip>
          </mat-chip-set>
        </div>
      </div>

      @if (loading()) {
        <div class="loading-container">
          <mat-spinner></mat-spinner>
        </div>
      } @else if (error()) {
        <div class="error-message">
          <p>{{ error() }}</p>
          <button mat-raised-button color="primary" (click)="loadProducts()">Retry</button>
        </div>
      } @else {
        <div class="products-grid">
          @for (product of products(); track product.id) {
            <mat-card class="product-card">
              <img mat-card-image [src]="product.images?.[0]?.file_path || 'assets/placeholder.png'" [alt]="product.title">
              <mat-card-header>
                <mat-card-title>{{ product.title }}</mat-card-title>
                <mat-card-subtitle>{{ product.categories?.[0]?.name }}</mat-card-subtitle>
              </mat-card-header>
              <mat-card-content>
                <p class="description">{{ product.description }}</p>
                <div class="price">\${{ product.base_price.toFixed(2) }}</div>
                @if (!product.stock_quantity || product.stock_quantity === 0) {
                  <span class="out-of-stock">Out of Stock</span>
                } @else if (product.stock_quantity < 10) {
                  <span class="low-stock">Only {{ product.stock_quantity }} left</span>
                }
              </mat-card-content>
              <mat-card-actions>
                <button mat-button color="primary" [routerLink]="['/products', product.id]">View Details</button>
              </mat-card-actions>
            </mat-card>
          } @empty {
            <div class="no-products">
              <p>No products found</p>
            </div>
          }
        </div>

        @if (totalProducts() > pageSize) {
          <mat-paginator 
            [length]="totalProducts()"
            [pageSize]="pageSize"
            [pageIndex]="currentPage - 1"
            [pageSizeOptions]="[12, 24, 48]"
            (page)="onPageChange($event)">
          </mat-paginator>
        }
      }
    </div>
  `,
  styles: [`
    .product-list-container {
      padding: 24px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .header {
      margin-bottom: 32px;
    }

    h1 {
      margin-bottom: 24px;
    }

    .filters {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
      align-items: center;
    }

    .filters mat-form-field {
      min-width: 200px;
    }

    .loading-container {
      display: flex;
      justify-content: center;
      padding: 48px;
    }

    .error-message {
      text-align: center;
      padding: 48px;
      color: #f44336;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }

    .product-card {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .product-card img {
      height: 200px;
      object-fit: cover;
    }

    mat-card-content {
      flex: 1;
    }

    .description {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-bottom: 16px;
      color: #666;
    }

    .price {
      font-size: 24px;
      font-weight: bold;
      color: #3f51b5;
      margin-bottom: 8px;
    }

    .out-of-stock {
      color: #f44336;
      font-weight: 500;
    }

    .low-stock {
      color: #ff9800;
      font-weight: 500;
    }

    .no-products {
      grid-column: 1 / -1;
      text-align: center;
      padding: 48px;
      color: #666;
    }

    mat-paginator {
      margin-top: 24px;
    }
  `]
})
export class ProductListComponent implements OnInit {
  private productService = inject(ProductService);

  products = signal<Product[]>([]);
  categories = signal<Category[]>([]);
  loading = signal(false);
  error = signal('');
  
  searchQuery = '';
  selectedCategory: number | null = null;
  inStockOnly = false;
  
  currentPage = 1;
  pageSize = 12;
  totalProducts = signal(0);

  private searchTimeout: any;

  ngOnInit(): void {
    this.loadCategories();
    this.loadProducts();
  }

  loadCategories(): void {
    this.productService.getCategories().subscribe({
      next: (response) => {
        if (response.data) {
          this.categories.set(response.data);
        }
      },
      error: (err) => {
        console.error('Failed to load categories:', err);
      }
    });
  }

  loadProducts(): void {
    this.loading.set(true);
    this.error.set('');

    this.productService.getProducts({
      page: this.currentPage,
      per_page: this.pageSize,
      category_id: this.selectedCategory || undefined,
      search: this.searchQuery || undefined,
      in_stock: this.inStockOnly || undefined,
    }).subscribe({
      next: (response) => {
        this.loading.set(false);
        if (response.data) {
          this.products.set(response.data);
          if (response.meta) {
            this.totalProducts.set(response.meta.total || 0);
          }
        }
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set('Failed to load products. Please try again.');
        console.error('Failed to load products:', err);
      }
    });
  }

  onSearchChange(): void {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => {
      this.currentPage = 1;
      this.loadProducts();
    }, 500);
  }

  onFilterChange(): void {
    this.currentPage = 1;
    this.loadProducts();
  }

  onPageChange(event: PageEvent): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadProducts();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  toggleInStockFilter(): void {
    this.inStockOnly = !this.inStockOnly;
    this.onFilterChange();
  }
}


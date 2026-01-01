import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatInputModule } from '@angular/material/input';
import { MatChipsModule } from '@angular/material/chips';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { ProductService } from '@core/services/product.service';
import { CartService } from '@core/services/cart.service';
import { Product, Part } from '@core/models';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    FormsModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatFormFieldModule,
    MatSelectModule,
    MatInputModule,
    MatChipsModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
  ],
  template: `
    <div class="product-detail-container">
      @if (loading()) {
        <div class="loading-container">
          <mat-spinner></mat-spinner>
        </div>
      } @else if (error()) {
        <div class="error-message">
          <p>{{ error() }}</p>
          <button mat-raised-button color="primary" [routerLink]="['/products']">Back to Products</button>
        </div>
      } @else if (product()) {
        <div class="product-detail">
          <div class="product-images">
            <img [src]="selectedImage() || product()?.images?.[0]?.file_path || 'assets/placeholder.png'" [alt]="product()!.title" class="main-image">
            @if (product()?.images && product()!.images!.length > 1) {
              <div class="image-thumbnails">
                @for (image of product()!.images!; track image.id) {
                  <img 
                    [src]="image.file_path" 
                    [alt]="image.alt_text || product()!.title"
                    [class.active]="selectedImage() === image.file_path"
                    (click)="selectedImage.set(image.file_path)">
                }
              </div>
            }
          </div>

          <div class="product-info">
            <div class="breadcrumb">
              <a routerLink="/products">Products</a>
              @if (product()?.categories && product()!.categories!.length > 0) {
                <span> / {{ product()!.categories![0].name }}</span>
              }
            </div>

            <h1>{{ product()!.title }}</h1>
            
            @if (product()!.sku) {
              <p class="sku">SKU: {{ product()!.sku }}</p>
            }

            <div class="price-section">
              <div class="price">\${{ calculatedPrice().toFixed(2) }}</div>
              @if (product()?.stock_quantity !== undefined && product()!.stock_quantity !== null) {
                @if (product()!.stock_quantity === 0) {
                  <mat-chip class="out-of-stock">Out of Stock</mat-chip>
                } @else if (product()!.stock_quantity! < 10) {
                  <mat-chip class="low-stock">Only {{ product()!.stock_quantity }} left</mat-chip>
                } @else {
                  <mat-chip class="in-stock">In Stock</mat-chip>
                }
              }
            </div>

            <p class="description">{{ product()!.description }}</p>

            <!-- Parts selection -->
            @if (availableParts().length > 0) {
              <div class="parts-section">
                <h3>Customize Your Product</h3>
                @for (partGroup of getPartGroups(); track partGroup.group) {
                  <mat-form-field appearance="outline">
                    <mat-label>{{ partGroup.name }}</mat-label>
                    <mat-select [(ngModel)]="selectedParts[partGroup.group]" (ngModelChange)="updatePrice()">
                      @for (part of partGroup.parts; track part.id) {
                        <mat-option [value]="part.id">
                          {{ part.name }} 
                          @if (part.price_modifier !== 0) {
                            <span>({{ part.price_modifier > 0 ? '+' : '' }}\${{ part.price_modifier.toFixed(2) }})</span>
                          }
                        </mat-option>
                      }
                    </mat-select>
                  </mat-form-field>
                }
              </div>
            }

            <!-- Quantity -->
            <div class="quantity-section">
              <mat-form-field appearance="outline">
                <mat-label>Quantity</mat-label>
                <input matInput type="number" [(ngModel)]="quantity" min="1" [max]="product()!.stock_quantity || 999">
              </mat-form-field>
            </div>

            <!-- Customization notes -->
            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Special Instructions (Optional)</mat-label>
              <textarea matInput [(ngModel)]="customizationNotes" rows="3" placeholder="Any special requests or customizations?"></textarea>
            </mat-form-field>

            <!-- Actions -->
            <div class="actions">
              <button 
                mat-raised-button 
                color="primary" 
                (click)="addToCart()"
                [disabled]="addingToCart() || product()!.stock_quantity === 0">
                @if (addingToCart()) {
                  <mat-spinner diameter="20"></mat-spinner>
                } @else {
                  <mat-icon>shopping_cart</mat-icon>
                  Add to Cart
                }
              </button>
              <button mat-stroked-button [routerLink]="['/products']">
                <mat-icon>arrow_back</mat-icon>
                Back to Products
              </button>
            </div>
          </div>
        </div>
      }
    </div>
  `,
  styles: [`
    .product-detail-container {
      padding: 24px;
      max-width: 1200px;
      margin: 0 auto;
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

    .product-detail {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 48px;
    }

    @media (max-width: 768px) {
      .product-detail {
        grid-template-columns: 1fr;
        gap: 24px;
      }
    }

    .product-images {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .main-image {
      width: 100%;
      height: auto;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .image-thumbnails {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .image-thumbnails img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
      cursor: pointer;
      border: 2px solid transparent;
      transition: border-color 0.2s;
    }

    .image-thumbnails img:hover,
    .image-thumbnails img.active {
      border-color: #3f51b5;
    }

    .product-info {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .breadcrumb {
      color: #666;
      font-size: 14px;
    }

    .breadcrumb a {
      color: #3f51b5;
      text-decoration: none;
    }

    .breadcrumb a:hover {
      text-decoration: underline;
    }

    h1 {
      margin: 0;
      font-size: 32px;
    }

    .sku {
      color: #666;
      margin: 0;
    }

    .price-section {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .price {
      font-size: 36px;
      font-weight: bold;
      color: #3f51b5;
    }

    .out-of-stock {
      background-color: #f44336;
      color: white;
    }

    .low-stock {
      background-color: #ff9800;
      color: white;
    }

    .in-stock {
      background-color: #4caf50;
      color: white;
    }

    .description {
      line-height: 1.6;
      color: #444;
    }

    .parts-section {
      border-top: 1px solid #ddd;
      padding-top: 16px;
    }

    .parts-section h3 {
      margin-top: 0;
    }

    mat-form-field {
      width: 100%;
    }

    .quantity-section mat-form-field {
      max-width: 150px;
    }

    .full-width {
      width: 100%;
    }

    .actions {
      display: flex;
      gap: 16px;
      margin-top: 16px;
    }

    .actions button {
      flex: 1;
    }

    .actions button mat-icon {
      margin-right: 8px;
    }
  `]
})
export class ProductDetailComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private productService = inject(ProductService);
  private cartService = inject(CartService);
  private snackBar = inject(MatSnackBar);

  product = signal<Product | null>(null);
  availableParts = signal<Part[]>([]);
  loading = signal(false);
  error = signal('');
  addingToCart = signal(false);
  
  selectedImage = signal('');
  selectedParts: { [key: string]: number } = {};
  quantity = 1;
  customizationNotes = '';
  calculatedPrice = signal(0);

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadProduct(+id);
    } else {
      this.router.navigate(['/products']);
    }
  }

  loadProduct(id: number): void {
    this.loading.set(true);
    this.error.set('');

    this.productService.getProduct(id).subscribe({
      next: (response) => {
        this.loading.set(false);
        if (response.data) {
          this.product.set(response.data);
          this.selectedImage.set(response.data.images?.[0]?.file_path || '');
          this.calculatedPrice.set(response.data.base_price);
          
          // Load parts if product requires them
          if (response.data.id) {
            this.loadParts(response.data.id);
          }
        }
      },
      error: (err) => {
        this.loading.set(false);
        this.error.set('Failed to load product. Please try again.');
        console.error('Failed to load product:', err);
      }
    });
  }

  loadParts(productId: number): void {
    this.productService.getProductParts(productId).subscribe({
      next: (response) => {
        if (response.data) {
          this.availableParts.set(response.data);
        }
      },
      error: (err) => {
        console.error('Failed to load parts:', err);
      }
    });
  }

  getPartGroups(): Array<{ group: string; name: string; parts: Part[] }> {
    const groups = new Map<string, Part[]>();
    
    this.availableParts().forEach(part => {
      const groupKey = part.alternative_group || 'default';
      if (!groups.has(groupKey)) {
        groups.set(groupKey, []);
      }
      groups.get(groupKey)!.push(part);
    });

    return Array.from(groups.entries()).map(([group, parts]) => ({
      group,
      name: parts[0]?.type || 'Options',
      parts
    }));
  }

  updatePrice(): void {
    if (!this.product()) return;

    let price = this.product()!.base_price;
    
    // Add price modifiers from selected parts
    Object.values(this.selectedParts).forEach(partId => {
      const part = this.availableParts().find(p => p.id === partId);
      if (part) {
        price += part.price_modifier;
      }
    });

    this.calculatedPrice.set(price);
  }

  addToCart(): void {
    if (!this.product() || this.addingToCart()) return;

    this.addingToCart.set(true);

    this.cartService.addToCart({
      product_id: this.product()!.id!,
      quantity: this.quantity,
      selected_parts: this.selectedParts,
      customization_notes: this.customizationNotes || undefined,
    }).subscribe({
      next: () => {
        this.addingToCart.set(false);
        this.snackBar.open('Added to cart!', 'Close', { duration: 3000 });
      },
      error: (err) => {
        this.addingToCart.set(false);
        this.snackBar.open('Failed to add to cart. Please try again.', 'Close', { duration: 5000 });
        console.error('Failed to add to cart:', err);
      }
    });
  }
}


import { Component, inject, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { ProductService } from '../../core/services/product.service';
import { Category } from '../../core/models/product.model';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink, MatButtonModule, MatCardModule, MatIconModule, MatProgressSpinnerModule],
  template: `
    <div class="home-container">
      <!-- Hero Section -->
      <section class="hero">
        <div class="container">
          <h1>Welcome to Kumpe3D</h1>
          <p class="subtitle">Quality 3D Printed Products</p>
          <p class="description">From grooming tools to decorative ornaments, we offer custom 3D printed products with fast shipping</p>
          <div class="cta-buttons">
            <a mat-raised-button class="btn-primary" routerLink="/products">Shop Now</a>
            <a mat-stroked-button class="btn-outline" routerLink="/products">Browse Catalog</a>
          </div>
        </div>
      </section>

      <!-- Categories Section -->
      <section class="categories-section">
        <div class="container">
          <div class="section-header">
            <h2>Shop by Category</h2>
            <p>Explore our collection of high-quality 3D printed products</p>
          </div>
          
          @if (loading) {
            <div class="loading-spinner">
              <mat-spinner></mat-spinner>
            </div>
          } @else {
            <div class="category-grid">
              @for (category of categories; track category.id) {
                <a [routerLink]="['/products']" [queryParams]="{category_id: category.id}" class="category-card">
                  <div class="category-icon">
                    <mat-icon>{{ category.icon || 'category' }}</mat-icon>
                  </div>
                  <h3>{{ category.name }}</h3>
                  <p>{{ category.description || 'View all products' }}</p>
                  <span class="shop-link">
                    Shop Now
                    <mat-icon>arrow_forward</mat-icon>
                  </span>
                </a>
              }
            </div>
          }
        </div>
      </section>

      <!-- Features Section -->
      <section class="features-section bg-light">
        <div class="container">
          <div class="section-header">
            <h2>Why Choose Kumpe3D?</h2>
          </div>
          <div class="feature-grid">
            <div class="feature-card">
              <div class="feature-icon">
                <mat-icon>verified</mat-icon>
              </div>
              <h3>High Quality</h3>
              <p>Professional grade 3D printing with attention to detail and quality materials</p>
            </div>
            <div class="feature-card">
              <div class="feature-icon">
                <mat-icon>local_shipping</mat-icon>
              </div>
              <h3>Fast Shipping</h3>
              <p>Quick turnaround times with shipping to US, Canada, and UK</p>
            </div>
            <div class="feature-card">
              <div class="feature-icon">
                <mat-icon>palette</mat-icon>
              </div>
              <h3>Custom Colors</h3>
              <p>Choose from a wide variety of colors and finishes to match your style</p>
            </div>
            <div class="feature-card">
              <div class="feature-icon">
                <mat-icon>support_agent</mat-icon>
              </div>
              <h3>Great Support</h3>
              <p>Friendly customer service ready to help with your orders</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Call to Action -->
      <section class="cta-section">
        <div class="container text-center">
          <h2>Ready to Get Started?</h2>
          <p>Browse our full catalog of 3D printed products</p>
          <a mat-raised-button class="btn-primary btn-lg" routerLink="/products">
            View All Products
            <mat-icon>arrow_forward</mat-icon>
          </a>
        </div>
      </section>
    </div>
  `,
  styles: [`
    .hero {
      background: linear-gradient(135deg, var(--kumpe-primary) 0%, var(--kumpe-primary-dark) 100%);
      color: white;
      padding: 100px 0 80px;
      text-align: center;
      position: relative;
      overflow: hidden;
      
      &::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
        opacity: 0.5;
      }
      
      .container {
        position: relative;
        z-index: 1;
      }
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 16px;
      color: white;
      
      @media (max-width: 768px) {
        font-size: 2rem;
      }
    }

    .subtitle {
      font-size: 1.5rem;
      font-weight: 500;
      margin-bottom: 12px;
      opacity: 0.95;
    }

    .description {
      font-size: 1.125rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto 32px;
    }

    .cta-buttons {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-primary {
      background-color: white !important;
      color: var(--kumpe-primary) !important;
      padding: 12px 32px;
      font-weight: 600;
      
      &:hover {
        background-color: var(--kumpe-bg-light) !important;
      }
    }

    .btn-outline {
      border: 2px solid white !important;
      color: white !important;
      padding: 10px 32px;
      font-weight: 600;
      
      &:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
      }
    }

    .btn-lg {
      padding: 14px 36px !important;
      font-size: 1.125rem !important;
      
      mat-icon {
        margin-left: 8px;
      }
    }

    .categories-section {
      padding: 80px 0;
    }

    .features-section {
      padding: 80px 0;
    }

    .cta-section {
      padding: 80px 0;
      background: linear-gradient(135deg, var(--kumpe-primary) 0%, var(--kumpe-primary-dark) 100%);
      color: white;
      
      h2 {
        color: white;
        font-size: 2.5rem;
        margin-bottom: 16px;
      }
      
      p {
        font-size: 1.25rem;
        margin-bottom: 32px;
        opacity: 0.9;
      }
    }

    .section-header {
      text-align: center;
      margin-bottom: 48px;
      
      h2 {
        font-size: 2.5rem;
        margin-bottom: 12px;
      }
      
      p {
        font-size: 1.125rem;
        color: var(--kumpe-text-color);
      }
    }

    .category-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 24px;
      
      @media (max-width: 768px) {
        grid-template-columns: 1fr;
      }
    }

    .category-card {
      background: white;
      border-radius: 12px;
      padding: 32px;
      text-align: center;
      text-decoration: none;
      color: inherit;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      border: 2px solid transparent;
      
      &:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(13, 119, 94, 0.15);
        border-color: var(--kumpe-primary);
        
        .category-icon {
          background-color: var(--kumpe-primary);
          
          mat-icon {
            color: white;
          }
        }
        
        .shop-link {
          color: var(--kumpe-primary);
        }
      }
      
      .category-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 24px;
        background-color: var(--kumpe-bg-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        
        mat-icon {
          font-size: 40px;
          width: 40px;
          height: 40px;
          color: var(--kumpe-primary);
          transition: all 0.3s ease;
        }
      }
      
      h3 {
        font-size: 1.5rem;
        margin-bottom: 12px;
        color: var(--kumpe-title-color);
      }
      
      p {
        color: var(--kumpe-text-color);
        margin-bottom: 16px;
        min-height: 48px;
      }
      
      .shop-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
        color: var(--kumpe-text-color);
        transition: color 0.3s ease;
        
        mat-icon {
          font-size: 18px;
          width: 18px;
          height: 18px;
        }
      }
    }

    .feature-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 32px;
      
      @media (max-width: 768px) {
        grid-template-columns: 1fr;
      }
    }

    .feature-card {
      text-align: center;
      padding: 24px;
      
      .feature-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 20px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(13, 119, 94, 0.1);
        
        mat-icon {
          font-size: 36px;
          width: 36px;
          height: 36px;
          color: var(--kumpe-primary);
        }
      }
      
      h3 {
        font-size: 1.25rem;
        margin-bottom: 12px;
      }
      
      p {
        color: var(--kumpe-text-color);
        line-height: 1.6;
      }
    }

    .loading-spinner {
      display: flex;
      justify-content: center;
      padding: 60px 0;
    }
  `]
})
export class HomeComponent implements OnInit {
  private productService = inject(ProductService);
  private cdr = inject(ChangeDetectorRef);
  
  categories: Category[] = [];
  loading = true;

  ngOnInit(): void {
    this.loadCategories();
  }

  loadCategories(): void {
    console.log('Loading categories...');
    this.productService.getCategories().subscribe({
      next: (response) => {
        console.log('Categories response:', response);
        this.categories = response.data || [];
        this.loading = false;
        console.log('Categories loaded:', this.categories.length);
        this.cdr.detectChanges();
      },
      error: (error) => {
        console.error('Error loading categories:', error);
        this.loading = false;
        this.cdr.detectChanges();
      }
    });
  }


}

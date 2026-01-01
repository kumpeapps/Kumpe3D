import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink, MatButtonModule, MatCardModule],
  template: `
    <div class="home-container">
      <section class="hero">
        <div class="container">
          <h1>Welcome to Kumpe3D</h1>
          <p class="subtitle">Custom 3D Printing Services</p>
          <p>Quality prints, fast turnaround, competitive prices</p>
          <div class="cta-buttons">
            <a mat-raised-button color="primary" routerLink="/products">Browse Products</a>
            <a mat-button routerLink="/auth/register">Get Started</a>
          </div>
        </div>
      </section>

      <section class="features container">
        <h2>Why Choose Us</h2>
        <div class="feature-grid">
          <mat-card>
            <mat-card-content>
              <h3>High Quality</h3>
              <p>Professional grade 3D printing with attention to detail</p>
            </mat-card-content>
          </mat-card>
          <mat-card>
            <mat-card-content>
              <h3>Fast Shipping</h3>
              <p>Quick turnaround times to get your prints to you faster</p>
            </mat-card-content>
          </mat-card>
          <mat-card>
            <mat-card-content>
              <h3>Custom Options</h3>
              <p>Customize colors and finishes to match your needs</p>
            </mat-card-content>
          </mat-card>
        </div>
      </section>
    </div>
  `,
  styles: [`
    .hero {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 80px 0;
      text-align: center;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 16px;
    }

    .subtitle {
      font-size: 1.5rem;
      margin-bottom: 8px;
    }

    .cta-buttons {
      margin-top: 32px;
      display: flex;
      gap: 16px;
      justify-content: center;
    }

    .features {
      padding: 80px 0;
    }

    .features h2 {
      text-align: center;
      margin-bottom: 48px;
    }

    .feature-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
    }

    mat-card {
      text-align: center;
      padding: 24px;
    }
  `]
})
export class HomeComponent {}

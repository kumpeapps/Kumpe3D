import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterLink, MatButtonModule, MatCardModule],
  template: `
    <div class="container">
      <h1>Shopping Cart</h1>
      <p>Cart implementation coming soon...</p>
      <a mat-raised-button color="primary" routerLink="/products">Continue Shopping</a>
    </div>
  `,
  styles: [`
    .container {
      padding: 24px;
    }
  `]
})
export class CartComponent {}

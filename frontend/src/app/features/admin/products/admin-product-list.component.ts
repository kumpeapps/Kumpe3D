import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-admin-product-list',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="container">
      <h1>Product Management</h1>
      <p>Product management coming soon...</p>
    </div>
  `,
  styles: [`
    .container {
      padding: 24px;
    }
  `]
})
export class AdminProductListComponent {}

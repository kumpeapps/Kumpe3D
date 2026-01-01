import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-admin-order-list',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="container">
      <h1>Order Management</h1>
      <p>Order management coming soon...</p>
    </div>
  `,
  styles: [`
    .container {
      padding: 24px;
    }
  `]
})
export class AdminOrderListComponent {}

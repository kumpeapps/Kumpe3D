import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatStepperModule } from '@angular/material/stepper';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatRadioModule } from '@angular/material/radio';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatIconModule } from '@angular/material/icon';
import { MatDividerModule } from '@angular/material/divider';
import { CartService } from '@core/services/cart.service';
import { AuthService } from '@core/services/auth.service';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    ReactiveFormsModule,
    MatStepperModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatCardModule,
    MatRadioModule,
    MatCheckboxModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
    MatIconModule,
    MatDividerModule,
  ],
  template: `
    <div class="checkout-container">
      <h1>Checkout</h1>

      @if (cartService.items$().length === 0) {
        <mat-card class="empty-cart">
          <mat-icon class="empty-icon">shopping_cart</mat-icon>
          <h2>Your cart is empty</h2>
          <p>Add some items to checkout</p>
          <button mat-raised-button color="primary" routerLink="/products">
            Browse Products
          </button>
        </mat-card>
      } @else {
        <mat-stepper [linear]="true" #stepper>
          <!-- Step 1: Shipping Information -->
          <mat-step [stepControl]="shippingForm">
            <ng-template matStepLabel>Shipping Information</ng-template>
            
            <form [formGroup]="shippingForm" class="checkout-form">
              <div class="form-grid">
                <mat-form-field appearance="outline">
                  <mat-label>Email</mat-label>
                  <input matInput formControlName="email" type="email" required>
                  @if (shippingForm.get('email')?.hasError('required')) {
                    <mat-error>Email is required</mat-error>
                  }
                  @if (shippingForm.get('email')?.hasError('email')) {
                    <mat-error>Please enter a valid email</mat-error>
                  }
                </mat-form-field>

                <mat-form-field appearance="outline">
                  <mat-label>Phone</mat-label>
                  <input matInput formControlName="phone" type="tel">
                </mat-form-field>
              </div>

              <div class="form-grid">
                <mat-form-field appearance="outline">
                  <mat-label>First Name</mat-label>
                  <input matInput formControlName="first_name" required>
                  @if (shippingForm.get('first_name')?.hasError('required')) {
                    <mat-error>First name is required</mat-error>
                  }
                </mat-form-field>

                <mat-form-field appearance="outline">
                  <mat-label>Last Name</mat-label>
                  <input matInput formControlName="last_name" required>
                  @if (shippingForm.get('last_name')?.hasError('required')) {
                    <mat-error>Last name is required</mat-error>
                  }
                </mat-form-field>
              </div>

              <mat-form-field appearance="outline" class="full-width">
                <mat-label>Company (Optional)</mat-label>
                <input matInput formControlName="company_name">
              </mat-form-field>

              <mat-form-field appearance="outline" class="full-width">
                <mat-label>Address Line 1</mat-label>
                <input matInput formControlName="address_line1" required>
                @if (shippingForm.get('address_line1')?.hasError('required')) {
                  <mat-error>Address is required</mat-error>
                }
              </mat-form-field>

              <mat-form-field appearance="outline" class="full-width">
                <mat-label>Address Line 2 (Optional)</mat-label>
                <input matInput formControlName="address_line2">
              </mat-form-field>

              <div class="form-grid-3">
                <mat-form-field appearance="outline">
                  <mat-label>City</mat-label>
                  <input matInput formControlName="city" required>
                  @if (shippingForm.get('city')?.hasError('required')) {
                    <mat-error>City is required</mat-error>
                  }
                </mat-form-field>

                <mat-form-field appearance="outline">
                  <mat-label>State</mat-label>
                  <input matInput formControlName="state_province" required>
                  @if (shippingForm.get('state_province')?.hasError('required')) {
                    <mat-error>State is required</mat-error>
                  }
                </mat-form-field>

                <mat-form-field appearance="outline">
                  <mat-label>ZIP Code</mat-label>
                  <input matInput formControlName="zip_postal_code" required>
                  @if (shippingForm.get('zip_postal_code')?.hasError('required')) {
                    <mat-error>ZIP code is required</mat-error>
                  }
                </mat-form-field>
              </div>

              <mat-form-field appearance="outline" class="full-width">
                <mat-label>Country</mat-label>
                <input matInput formControlName="country" required>
                @if (shippingForm.get('country')?.hasError('required')) {
                  <mat-error>Country is required</mat-error>
                }
              </mat-form-field>

              <div class="step-actions">
                <button mat-stroked-button routerLink="/cart">
                  <mat-icon>arrow_back</mat-icon>
                  Back to Cart
                </button>
                <button mat-raised-button color="primary" matStepperNext>
                  Continue to Payment
                  <mat-icon>arrow_forward</mat-icon>
                </button>
              </div>
            </form>
          </mat-step>

          <!-- Step 2: Payment Method -->
          <mat-step [stepControl]="paymentForm">
            <ng-template matStepLabel>Payment Method</ng-template>
            
            <form [formGroup]="paymentForm" class="checkout-form">
              <h3>Select Payment Method</h3>
              
              <mat-radio-group formControlName="payment_method" class="payment-methods">
                <mat-radio-button value="paypal">
                  <div class="payment-option">
                    <mat-icon>account_balance_wallet</mat-icon>
                    <div>
                      <strong>PayPal</strong>
                      <p>Pay securely with PayPal</p>
                    </div>
                  </div>
                </mat-radio-button>

                <mat-radio-button value="credit_card" disabled>
                  <div class="payment-option">
                    <mat-icon>credit_card</mat-icon>
                    <div>
                      <strong>Credit Card</strong>
                      <p>Coming soon</p>
                    </div>
                  </div>
                </mat-radio-button>
              </mat-radio-group>

              @if (paymentForm.get('payment_method')?.value === 'paypal') {
                <div class="paypal-info">
                  <mat-icon>info</mat-icon>
                  <p>You will be redirected to PayPal to complete your payment securely.</p>
                </div>
              }

              <div class="step-actions">
                <button mat-stroked-button matStepperPrevious>
                  <mat-icon>arrow_back</mat-icon>
                  Back
                </button>
                <button mat-raised-button color="primary" matStepperNext>
                  Review Order
                  <mat-icon>arrow_forward</mat-icon>
                </button>
              </div>
            </form>
          </mat-step>

          <!-- Step 3: Review & Place Order -->
          <mat-step>
            <ng-template matStepLabel>Review Order</ng-template>
            
            <div class="review-section">
              <div class="review-content">
                <!-- Shipping Address -->
                <mat-card class="review-card">
                  <h3>Shipping Address</h3>
                  <div class="address-display">
                    <p><strong>{{ shippingForm.get('first_name')?.value }} {{ shippingForm.get('last_name')?.value }}</strong></p>
                    @if (shippingForm.get('company_name')?.value) {
                      <p>{{ shippingForm.get('company_name')?.value }}</p>
                    }
                    <p>{{ shippingForm.get('address_line1')?.value }}</p>
                    @if (shippingForm.get('address_line2')?.value) {
                      <p>{{ shippingForm.get('address_line2')?.value }}</p>
                    }
                    <p>{{ shippingForm.get('city')?.value }}, {{ shippingForm.get('state_province')?.value }} {{ shippingForm.get('zip_postal_code')?.value }}</p>
                    <p>{{ shippingForm.get('country')?.value }}</p>
                    <p>{{ shippingForm.get('email')?.value }}</p>
                    @if (shippingForm.get('phone')?.value) {
                      <p>{{ shippingForm.get('phone')?.value }}</p>
                    }
                  </div>
                  <button mat-button color="primary" (click)="stepper.selectedIndex = 0">
                    <mat-icon>edit</mat-icon>
                    Edit
                  </button>
                </mat-card>

                <!-- Payment Method -->
                <mat-card class="review-card">
                  <h3>Payment Method</h3>
                  <div class="payment-display">
                    @if (paymentForm.get('payment_method')?.value === 'paypal') {
                      <div class="payment-method-info">
                        <mat-icon>account_balance_wallet</mat-icon>
                        <strong>PayPal</strong>
                      </div>
                    }
                  </div>
                  <button mat-button color="primary" (click)="stepper.selectedIndex = 1">
                    <mat-icon>edit</mat-icon>
                    Edit
                  </button>
                </mat-card>

                <!-- Order Items -->
                <mat-card class="review-card">
                  <h3>Order Items ({{ cartService.itemCount$() }})</h3>
                  <div class="order-items">
                    @for (item of cartService.items$(); track item.id) {
                      <div class="review-item">
                        @if (item.product_image) {
                          <img [src]="item.product_image" 
                               [alt]="item.product_title || 'Product'">
                        } @else {
                          <div class="no-item-image">
                            <mat-icon>image</mat-icon>
                          </div>
                        }
                        <div class="item-info">
                          <strong>{{ item.product_title || 'Unknown Product' }}</strong>
                          <p>Quantity: {{ item.quantity }}</p>
                          @if (item.option_names && item.option_names.length > 0) {
                            <p class="options">{{ item.option_names.join(', ') }}</p>
                          }
                        </div>
                        <div class="item-price">
                          \${{ ((+item.price) * item.quantity).toFixed(2) }}
                        </div>
                      </div>
                    }
                  </div>
                </mat-card>
              </div>

              <!-- Order Summary (Sticky) -->
              <div class="order-summary-sticky">
                <mat-card>
                  <h3>Order Summary</h3>
                  
                  <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>\${{ cartService.subtotal$().toFixed(2) }}</span>
                  </div>

                  <div class="summary-row">
                    <span>Shipping:</span>
                    <span>\${{ shippingCost().toFixed(2) }}</span>
                  </div>

                  <div class="summary-row">
                    <span>Tax:</span>
                    <span>\${{ taxAmount().toFixed(2) }}</span>
                  </div>

                  <mat-divider></mat-divider>

                  <div class="summary-row total">
                    <span>Total:</span>
                    <span>\${{ orderTotal().toFixed(2) }}</span>
                  </div>

                  <button 
                    mat-raised-button 
                    color="primary" 
                    class="place-order-btn"
                    (click)="placeOrder()"
                    [disabled]="placingOrder()">
                    @if (placingOrder()) {
                      <mat-spinner diameter="20"></mat-spinner>
                      Processing...
                    } @else {
                      <ng-container>
                        <mat-icon>payment</mat-icon>
                        Place Order
                      </ng-container>
                    }
                  </button>

                  <div class="step-actions">
                    <button mat-stroked-button matStepperPrevious>
                      <mat-icon>arrow_back</mat-icon>
                      Back
                    </button>
                  </div>
                </mat-card>
              </div>
            </div>
          </mat-step>
        </mat-stepper>
      }
    </div>
  `,
  styles: [`
    .checkout-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 24px;
    }

    h1 {
      margin-bottom: 32px;
    }

    .empty-cart {
      text-align: center;
      padding: 64px 24px;
    }

    .empty-icon {
      font-size: 120px;
      width: 120px;
      height: 120px;
      color: #ccc;
      margin-bottom: 16px;
    }

    .checkout-form {
      padding: 24px 0;
      max-width: 800px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-grid-3 {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 768px) {
      .form-grid,
      .form-grid-3 {
        grid-template-columns: 1fr;
      }
    }

    .full-width {
      width: 100%;
    }

    .step-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 24px;
      gap: 16px;
    }

    .payment-methods {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin: 24px 0;
    }

    .payment-option {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 8px 0;
    }

    .payment-option mat-icon {
      font-size: 32px;
      width: 32px;
      height: 32px;
    }

    .payment-option p {
      margin: 4px 0 0 0;
      font-size: 14px;
      color: #666;
    }

    .paypal-info {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 16px;
      background: #e3f2fd;
      border-radius: 4px;
      margin-top: 16px;
    }

    .paypal-info mat-icon {
      color: #1976d2;
    }

    .paypal-info p {
      margin: 0;
      color: #1565c0;
    }

    .review-section {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 24px;
      padding: 24px 0;
    }

    @media (max-width: 968px) {
      .review-section {
        grid-template-columns: 1fr;
      }

      .order-summary-sticky {
        order: 1;
      }

      .review-content {
        order: 2;
      }
    }

    .review-content {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .review-card {
      padding: 24px !important;
    }

    .review-card h3 {
      margin: 0 0 16px 0;
    }

    .address-display p {
      margin: 4px 0;
    }

    .payment-method-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .order-items {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .review-item {
      display: grid;
      grid-template-columns: 80px 1fr auto;
      gap: 16px;
      align-items: center;
    }

    .review-item img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
    }

    .no-item-image {
      width: 80px;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
      border-radius: 4px;
    }

    .no-item-image mat-icon {
      font-size: 40px;
      width: 40px;
      height: 40px;
      color: #ccc;
    }

    .item-info strong {
      display: block;
      margin-bottom: 4px;
    }

    .item-info p {
      margin: 2px 0;
      font-size: 14px;
      color: #666;
    }

    .item-info .options {
      font-style: italic;
    }

    .item-price {
      font-size: 18px;
      font-weight: 600;
    }

    .order-summary-sticky {
      position: sticky;
      top: 24px;
      height: fit-content;
    }

    .order-summary-sticky h3 {
      margin: 0 0 16px 0;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      font-size: 16px;
    }

    .summary-row.total {
      font-size: 20px;
      font-weight: bold;
      padding-top: 16px;
    }

    mat-divider {
      margin: 16px 0;
    }

    .place-order-btn {
      width: 100%;
      height: 48px;
      font-size: 16px;
      margin: 24px 0 16px 0;
    }
  `]
})
export class CheckoutComponent implements OnInit {
  private fb = inject(FormBuilder);
  private router = inject(Router);
  private snackBar = inject(MatSnackBar);
  
  cartService = inject(CartService);
  authService = inject(AuthService);

  placingOrder = signal(false);

  shippingForm!: FormGroup;
  paymentForm!: FormGroup;

  // Computed values
  shippingCost = computed(() => 5.00);
  taxRate = computed(() => {
    const state = this.shippingForm?.get('state_province')?.value?.toUpperCase();
    return state === 'TX' ? 0.08 : 0.00;
  });
  taxAmount = computed(() => this.cartService.subtotal$() * this.taxRate());
  orderTotal = computed(() => this.cartService.subtotal$() + this.shippingCost() + this.taxAmount());

  ngOnInit() {
    // Redirect if cart is empty
    if (this.cartService.items$().length === 0) {
      this.router.navigate(['/cart']);
      return;
    }

    // Initialize forms
    this.shippingForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      phone: [''],
      first_name: ['', Validators.required],
      last_name: ['', Validators.required],
      company_name: [''],
      address_line1: ['', Validators.required],
      address_line2: [''],
      city: ['', Validators.required],
      state_province: ['', Validators.required],
      zip_postal_code: ['', Validators.required],
      country: ['United States', Validators.required],
    });

    this.paymentForm = this.fb.group({
      payment_method: ['paypal', Validators.required],
    });

    // Pre-fill if user is logged in
    this.authService.currentUser$.subscribe(currentUser => {
      if (currentUser) {
        this.shippingForm.patchValue({
          email: currentUser.email,
          first_name: currentUser.first_name || '',
          last_name: currentUser.last_name || '',
        });
      }
    });
  }

  placeOrder() {
    if (!this.shippingForm.valid || !this.paymentForm.valid) {
      this.snackBar.open('Please complete all required fields', 'Close', { duration: 3000 });
      return;
    }

    this.placingOrder.set(true);

    // TODO: Implement PayPal integration
    // For now, just show success message
    setTimeout(() => {
      this.placingOrder.set(false);
      this.snackBar.open('Order placed successfully! (PayPal integration coming soon)', 'Close', { duration: 5000 });
      this.router.navigate(['/orders']);
    }, 2000);
  }
}

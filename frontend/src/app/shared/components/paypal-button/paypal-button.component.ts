import { Component, Input, Output, EventEmitter, OnInit, ElementRef, ViewChild, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';

declare var paypal: any;

export interface PayPalOrderData {
  amount: number;
  currency?: string;
  description?: string;
}

export interface PayPalResult {
  orderID: string;
  payerID: string;
  paymentID: string;
  facilitatorAccessToken: string;
}

@Component({
  selector: 'app-paypal-button',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div #paypalButtonContainer></div>
  `,
  styles: []
})
export class PaypalButtonComponent implements AfterViewInit {
  @ViewChild('paypalButtonContainer', { static: false }) paypalButtonContainer!: ElementRef;
  
  @Input() orderData!: PayPalOrderData;
  @Input() disabled = false;
  
  @Output() onApprove = new EventEmitter<PayPalResult>();
  @Output() onError = new EventEmitter<any>();
  @Output() onCancel = new EventEmitter<void>();

  ngAfterViewInit() {
    if (typeof paypal === 'undefined') {
      console.error('PayPal SDK not loaded');
      this.onError.emit({ message: 'PayPal SDK not loaded' });
      return;
    }

    this.renderPayPalButton();
  }

  private renderPayPalButton() {
    paypal.Buttons({
      style: {
        layout: 'vertical',
        color: 'gold',
        shape: 'rect',
        label: 'paypal'
      },
      createOrder: (_data: any, actions: any) => {
        return actions.order.create({
          purchase_units: [{
            amount: {
              currency_code: this.orderData.currency || 'USD',
              value: this.orderData.amount.toFixed(2)
            },
            description: this.orderData.description || 'Order from Kumpe3D'
          }]
        });
      },
      onApprove: async (_data: any, actions: any) => {
        const order = await actions.order.capture();
        
        this.onApprove.emit({
          orderID: order.id,
          payerID: order.payer.payer_id,
          paymentID: order.purchase_units[0].payments.captures[0].id,
          facilitatorAccessToken: order.purchase_units[0].payments.captures[0].id
        });
      },
      onError: (err: any) => {
        console.error('PayPal error:', err);
        this.onError.emit(err);
      },
      onCancel: () => {
        this.onCancel.emit();
      }
    }).render(this.paypalButtonContainer.nativeElement);
  }
}

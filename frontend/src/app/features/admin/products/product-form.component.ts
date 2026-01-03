import { Component, OnInit, inject, ChangeDetectorRef, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, FormArray } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatSelectModule } from '@angular/material/select';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatCardModule } from '@angular/material/card';
import { MatDividerModule } from '@angular/material/divider';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { HttpClient } from '@angular/common/http';
import { environment } from '@environments/environment';
import { ProductService } from '@core/services/product.service';
import { Product } from '@core/models';

interface Part {
  id: number;
  part_number: string;
  name: string;
  stock_quantity: number;
}

@Component({
  selector: 'app-product-form',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatCheckboxModule,
    MatSelectModule,
    MatIconModule,
    MatSnackBarModule,
    MatCardModule,
    MatDividerModule,
    MatProgressSpinnerModule,
  ],
  template: `
    <div class="page-container">
      <div class="page-header">
        <button mat-icon-button (click)="goBack()" class="back-button">
          <mat-icon>arrow_back</mat-icon>
        </button>
        <h1>{{ isEditMode ? 'Edit Product' : 'Create Product' }}</h1>
        <div class="header-actions">
          <button mat-button (click)="goBack()">Cancel</button>
          <button mat-raised-button color="primary" (click)="save()" [disabled]="saving || !productForm.valid">
            @if (saving) {
              <mat-spinner diameter="20"></mat-spinner>
            }
            {{ isEditMode ? 'Update' : 'Create' }} Product
          </button>
        </div>
      </div>

      @if (loading) {
        <div class="loading-container">
          <mat-spinner></mat-spinner>
        </div>
      } @else {
        <form [formGroup]="productForm" class="product-form">
          
          <!-- Basic Information -->
          <section class="form-section">
            <h2>Basic Information</h2>
            <div class="form-grid">
              <mat-form-field appearance="outline">
                <mat-label>SKU</mat-label>
                <input matInput formControlName="sku" required>
                @if (productForm.get('sku')?.hasError('required')) {
                  <mat-error>SKU is required</mat-error>
                }
              </mat-form-field>

              <mat-form-field appearance="outline">
                <mat-label>Title</mat-label>
                <input matInput formControlName="title" required>
                @if (productForm.get('title')?.hasError('required')) {
                  <mat-error>Title is required</mat-error>
                }
              </mat-form-field>
            </div>

            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Description</mat-label>
              <textarea matInput formControlName="description" rows="3"></textarea>
            </mat-form-field>
          </section>

          <!-- Pricing -->
          <section class="form-section">
            <h2>Pricing & Weight</h2>
            <div class="form-grid">
              <mat-form-field appearance="outline">
                <mat-label>Base Price</mat-label>
                <input matInput type="number" formControlName="base_price" required step="0.01">
                <span matPrefix>$&nbsp;</span>
                @if (productForm.get('base_price')?.hasError('required')) {
                  <mat-error>Price is required</mat-error>
                }
              </mat-form-field>

              <mat-form-field appearance="outline">
                <mat-label>Cost</mat-label>
                <input matInput type="number" formControlName="cost" step="0.01">
                <span matPrefix>$&nbsp;</span>
              </mat-form-field>

              <mat-form-field appearance="outline">
                <mat-label>Weight (oz)</mat-label>
                <input matInput type="number" formControlName="weight" step="0.1">
              </mat-form-field>
            </div>
          </section>

          <!-- Options -->
          <section class="form-section">
            <div class="section-header">
              <h2>Product Options</h2>
              <button mat-raised-button type="button" (click)="addOption()" color="accent">
                <mat-icon>add</mat-icon>
                <span>Add Option</span>
              </button>
            </div>
            <p class="section-description">Customer-selectable options like colors or sizes.</p>

            <div formArrayName="options">
              @for (option of options.controls; track $index; let i = $index) {
                <div [formGroupName]="i" class="option-card">
                  <div class="option-main">
                    <mat-form-field appearance="outline">
                      <mat-label>Option Name</mat-label>
                      <input matInput formControlName="name" placeholder="e.g., Red">
                    </mat-form-field>

                    <mat-form-field appearance="outline">
                      <mat-label>Group</mat-label>
                      <input matInput formControlName="option_group" placeholder="e.g., Color">
                    </mat-form-field>

                    <mat-form-field appearance="outline">
                      <mat-label>Price Modifier</mat-label>
                      <input matInput type="number" formControlName="price_modifier" step="0.01">
                      <span matPrefix>$&nbsp;</span>
                    </mat-form-field>

                    <mat-form-field appearance="outline">
                      <mat-label>Sort Order</mat-label>
                      <input matInput type="number" formControlName="sort_order">
                    </mat-form-field>

                    <button mat-icon-button type="button" color="warn" (click)="removeOption(i)" class="delete-btn">
                      <mat-icon>delete</mat-icon>
                    </button>
                  </div>

                  <!-- Parts for this option -->
                  <div class="option-parts">
                    <div class="subsection-header">
                      <span class="subsection-title">Parts</span>
                      <button mat-button type="button" (click)="addOptionPart(i)">
                        <mat-icon>add</mat-icon>
                        <span>Add Part</span>
                      </button>
                    </div>

                    <div formArrayName="parts">
                      @for (part of getOptionParts(i).controls; track $index; let j = $index) {
                        <div [formGroupName]="j" class="part-row">
                          <mat-form-field appearance="outline" class="part-select">
                            <mat-label>Part</mat-label>
                            <mat-select formControlName="part_id" required>
                              @for (part of availableParts; track part.id) {
                                <mat-option [value]="part.id">
                                  {{ part.part_number }} - {{ part.name }}
                                </mat-option>
                              }
                            </mat-select>
                          </mat-form-field>

                          <mat-form-field appearance="outline" class="qty-field">
                            <mat-label>Qty</mat-label>
                            <input matInput type="number" formControlName="quantity" min="1" required>
                          </mat-form-field>

                          <mat-form-field appearance="outline" class="group-field">
                            <mat-label>OR Group</mat-label>
                            <input matInput type="number" formControlName="alternative_group">
                          </mat-form-field>

                          <button mat-icon-button type="button" color="warn" (click)="removeOptionPart(i, j)">
                            <mat-icon>delete</mat-icon>
                          </button>
                        </div>
                      }
                      @if (getOptionParts(i).length === 0) {
                        <p class="empty-message">No parts assigned</p>
                      }
                    </div>
                  </div>
                </div>
              }
              @if (options.length === 0) {
                <p class="empty-message">No options added yet</p>
              }
            </div>
          </section>

          <!-- Base Product BOM -->
          <section class="form-section">
            <div class="section-header">
              <h2>Base Product BOM</h2>
              <button mat-raised-button type="button" (click)="addProductPart()" color="accent">
                <mat-icon>add</mat-icon>
                <span>Add Part</span>
              </button>
            </div>
            <p class="section-description">Parts always required regardless of options.</p>

            <div formArrayName="product_parts">
              @for (part of productParts.controls; track $index; let i = $index) {
                <div [formGroupName]="i" class="bom-row">
                  <mat-form-field appearance="outline" class="part-select">
                    <mat-label>Part</mat-label>
                    <mat-select formControlName="part_id" required>
                      @for (part of availableParts; track part.id) {
                        <mat-option [value]="part.id">
                          {{ part.part_number }} - {{ part.name }}
                        </mat-option>
                      }
                    </mat-select>
                  </mat-form-field>

                  <mat-form-field appearance="outline" class="qty-field">
                    <mat-label>Quantity</mat-label>
                    <input matInput type="number" formControlName="quantity" min="1" required>
                  </mat-form-field>

                  <mat-form-field appearance="outline" class="group-field">
                    <mat-label>OR Group</mat-label>
                    <input matInput type="number" formControlName="alternative_group">
                  </mat-form-field>

                  <mat-checkbox formControlName="is_optional">Optional</mat-checkbox>

                  <button mat-icon-button type="button" color="warn" (click)="removeProductPart(i)">
                    <mat-icon>delete</mat-icon>
                  </button>
                </div>
              }
              @if (productParts.length === 0) {
                <p class="empty-message">No base parts added</p>
              }
            </div>
          </section>

          <!-- Status -->
          <section class="form-section">
            <h2>Status</h2>
            <div class="checkbox-group">
              <mat-checkbox formControlName="is_active">Active</mat-checkbox>
              <mat-checkbox formControlName="featured">Featured</mat-checkbox>
              <mat-checkbox formControlName="allow_order_when_out_of_stock">Allow ordering when out of stock</mat-checkbox>
            </div>
          </section>

          <!-- SEO -->
          <section class="form-section">
            <h2>SEO</h2>
            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Meta Title</mat-label>
              <input matInput formControlName="meta_title">
            </mat-form-field>

            <mat-form-field appearance="outline" class="full-width">
              <mat-label>Meta Description</mat-label>
              <textarea matInput formControlName="meta_description" rows="2"></textarea>
            </mat-form-field>
          </section>
        </form>
      }
    </div>
  `,
  styles: [`
    .page-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 32px 24px;
      background: #fafafa;
      min-height: 100vh;
    }

    .page-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 32px;
      padding: 24px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);

      .back-button {
        margin-right: 8px;
      }

      h1 {
        flex: 1;
        margin: 0;
        font-size: 24px;
        font-weight: 500;
        color: #202124;
      }

      .header-actions {
        display: flex;
        gap: 12px;

        mat-spinner {
          margin-right: 8px;
        }
      }
    }

    .loading-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 400px;
    }

    .product-form {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .form-section {
      padding: 32px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);

      h2 {
        margin: 0 0 20px 0;
        font-size: 18px;
        font-weight: 500;
        color: #202124;
      }

      .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;

        h2 {
          margin: 0;
        }
      }

      .section-description {
        margin: 0 0 20px 0;
        color: #5f6368;
        font-size: 14px;
      }
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 16px;
      margin-bottom: 16px;

      &:last-child {
        margin-bottom: 0;
      }
    }

    .full-width {
      width: 100%;
    }

    .checkbox-group {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .option-card {
      padding: 24px;
      background: #f8f9fa;
      border-radius: 8px;
      border: 1px solid #e8eaed;
      margin-bottom: 16px;

      &:last-child {
        margin-bottom: 0;
      }
    }

    .option-main {
      display: grid;
      grid-template-columns: 2fr 1.5fr 130px 100px 48px;
      gap: 16px;
      align-items: start;
      margin-bottom: 20px;
    }

    .option-parts {
      padding-top: 20px;
      border-top: 1px solid #e8eaed;
    }

    .subsection-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;

      .subsection-title {
        font-size: 14px;
        font-weight: 500;
        color: #5f6368;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
    }

    .part-row {
      display: grid;
      grid-template-columns: 2fr 110px 110px 48px;
      gap: 16px;
      align-items: start;
      margin-bottom: 16px;

      &:last-child {
        margin-bottom: 0;
      }

      .part-select {
        flex: 1;
        min-width: 0;
      }

      .qty-field,
      .group-field {
        width: 100%;
      }
    }

    .bom-row {
      display: grid;
      grid-template-columns: 2fr 110px 110px auto 48px;
      gap: 16px;
      align-items: start;
      margin-bottom: 16px;

      &:last-child {
        margin-bottom: 0;
      }

      .part-select {
        flex: 1;
        min-width: 0;
      }

      .qty-field,
      .group-field {
        width: 100%;
      }

      mat-checkbox {
        margin-top: 8px;
      }
    }

    .delete-btn {
      margin-top: 4px;
    }

    .empty-message {
      padding: 32px;
      text-align: center;
      color: #5f6368;
      font-size: 14px;
      font-style: italic;
      background: #f8f9fa;
      border-radius: 8px;
      border: 1px dashed #e8eaed;
    }

    mat-form-field {
      width: 100%;
    }
  `]
})
export class ProductFormComponent implements OnInit {
  private fb = inject(FormBuilder);
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private http = inject(HttpClient);
  private snackBar = inject(MatSnackBar);
  private productService = inject(ProductService);
  private cdr = inject(ChangeDetectorRef);

  productForm: FormGroup;
  availableParts: Part[] = [];
  loading = false;
  saving = false;
  isEditMode = false;
  productId: number | null = null;

  constructor() {
    // Initialize empty form immediately to prevent template errors
    this.productForm = this.fb.group({
      sku: ['', Validators.required],
      title: ['', Validators.required],
      description: [''],
      base_price: [0, [Validators.required, Validators.min(0)]],
      cost: [0],
      weight: [0],
      is_active: [true],
      featured: [false],
      allow_order_when_out_of_stock: [false],
      meta_title: [''],
      meta_description: [''],
      options: this.fb.array([]),
      product_parts: this.fb.array([])
    });
  }

  ngOnInit() {
    this.loadParts();
    
    // Check if we're in edit mode
    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.productId = +params['id'];
        this.loadProduct(this.productId);
      } else {
        // Initialize empty form for create mode
        this.initializeForm();
      }
    });
  }

  initializeForm(product?: Product) {
    this.productForm = this.fb.group({
      sku: [product?.sku || '', Validators.required],
      title: [product?.title || '', Validators.required],
      description: [product?.description || ''],
      base_price: [product?.base_price || 0, [Validators.required, Validators.min(0)]],
      cost: [product?.cost || 0],
      weight: [product?.weight || 0],
      is_active: [product?.is_active ?? true],
      featured: [product?.featured || false],
      allow_order_when_out_of_stock: [product?.allow_order_when_out_of_stock || false],
      meta_title: [product?.meta_title || ''],
      meta_description: [product?.meta_description || ''],
      options: this.fb.array([]),
      product_parts: this.fb.array([])
    });

    // Load existing options if editing
    if (product?.options) {
      product.options.forEach(option => {
        const optionParts = option.parts || [];
        this.options.push(this.fb.group({
          id: [option.id],
          name: [option.name],
          option_group: [option.option_group || ''],
          price_modifier: [option.price_modifier || 0],
          sort_order: [option.sort_order || 0],
          is_active: [option.is_active ?? true],
          parts: this.fb.array(
            optionParts.map((p: any) => this.fb.group({
              part_id: [p.part_id],
              quantity: [p.quantity || 1],
              alternative_group: [p.alternative_group],
              priority: [p.priority || 0],
              notes: [p.notes || '']
            }))
          )
        }));
      });
    }

    // Load existing product parts if editing
    if ((product as any)?.parts) {
      (product as any).parts.forEach((part: any) => {
        this.productParts.push(this.fb.group({
          id: [part.id],
          part_id: [part.part_id],
          quantity: [part.quantity],
          alternative_group: [part.alternative_group],
          is_optional: [part.is_optional || false]
        }));
      });
    }
  }

  loadProduct(id: number) {
    this.loading = true;
    this.productService.getProduct(id).subscribe({
      next: (response) => {
        this.initializeForm(response.data);
        this.loading = false;
        this.cdr.detectChanges();
      },
      error: (error) => {
        console.error('Error loading product:', error);
        this.snackBar.open('Failed to load product', 'Close', { duration: 5000 });
        this.loading = false;
        this.goBack();
      }
    });
  }

  loadParts() {
    this.http.get<any>(`${environment.apiUrl}/products/admin/parts`).subscribe({
      next: (response) => {
        this.availableParts = response.data || [];
      },
      error: (error) => {
        console.error('Error loading parts:', error);
      }
    });
  }

  get options() {
    return this.productForm.get('options') as FormArray;
  }

  getOptionParts(optionIndex: number): FormArray {
    return this.options.at(optionIndex).get('parts') as FormArray;
  }

  get productParts() {
    return this.productForm.get('product_parts') as FormArray;
  }

  addOption() {
    this.options.push(this.fb.group({
      name: [''],
      option_group: [''],
      price_modifier: [0],
      sort_order: [0],
      is_active: [true],
      parts: this.fb.array([])
    }));
  }

  removeOption(index: number) {
    this.options.removeAt(index);
  }

  addOptionPart(optionIndex: number) {
    this.getOptionParts(optionIndex).push(this.fb.group({
      part_id: [null, Validators.required],
      quantity: [1, [Validators.required, Validators.min(1)]],
      alternative_group: [null],
      priority: [0],
      notes: ['']
    }));
  }

  removeOptionPart(optionIndex: number, partIndex: number) {
    this.getOptionParts(optionIndex).removeAt(partIndex);
  }

  addProductPart() {
    this.productParts.push(this.fb.group({
      part_id: [null, Validators.required],
      quantity: [1, [Validators.required, Validators.min(1)]],
      alternative_group: [null],
      is_optional: [false]
    }));
  }

  removeProductPart(index: number) {
    this.productParts.removeAt(index);
  }

  save() {
    if (this.productForm.valid) {
      this.saving = true;
      const formValue = this.productForm.value;

      const request = this.isEditMode
        ? this.productService.updateProduct(this.productId!, formValue)
        : this.productService.createProduct(formValue);

      request.subscribe({
        next: () => {
          this.snackBar.open(`Product ${this.isEditMode ? 'updated' : 'created'} successfully`, 'Close', { duration: 3000 });
          this.goBack();
        },
        error: (error) => {
          console.error('Error saving product:', error);
          this.snackBar.open(error.error?.error?.message || 'Failed to save product', 'Close', { duration: 5000 });
          this.saving = false;
        }
      });
    }
  }

  goBack() {
    this.router.navigate(['/admin/products']);
  }
}

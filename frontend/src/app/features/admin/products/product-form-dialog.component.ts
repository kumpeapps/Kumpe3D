import { Component, OnInit, inject, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule, FormArray } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatSelectModule } from '@angular/material/select';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDividerModule } from '@angular/material/divider';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
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
  selector: 'app-product-form-dialog',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatCheckboxModule,
    MatSelectModule,
    MatIconModule,
    MatSnackBarModule,
    MatDividerModule,
    MatAutocompleteModule,
  ],
  template: `
    <h2 mat-dialog-title>{{ data.mode === 'create' ? 'Create' : 'Edit' }} Product</h2>
    <mat-dialog-content>
      <form [formGroup]="productForm" class="product-form">
        <!-- Basic Info -->
        <div class="section">
          <h3>Basic Information</h3>
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

          <mat-form-field appearance="outline" class="full-width">
            <mat-label>Description</mat-label>
            <textarea matInput formControlName="description" rows="4"></textarea>
          </mat-form-field>
        </div>

        <mat-divider></mat-divider>

        <!-- Pricing -->
        <div class="section">
          <h3>Pricing</h3>
          <div class="row">
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
        </div>

        <mat-divider></mat-divider>

        <!-- Options -->
        <div class="section">
          <div class="section-header">
            <h3>Product Options (Customer Choices)</h3>
            <button mat-raised-button type="button" (click)="addOption()">
              <mat-icon>add</mat-icon>
              Add Option
            </button>
          </div>
          <p class="help-text">Define customer-selectable options. Group related options together (e.g., all colors in "Color" group). Each option can link to a specific part for inventory tracking.</p>

          <div formArrayName="options" class="options-list">
            @for (option of options.controls; track $index; let i = $index) {
              <div [formGroupName]="i" class="option-container">
                <div class="option-header">
                  <mat-form-field appearance="outline" class="flex-1">
                    <mat-label>Option Name</mat-label>
                    <input matInput formControlName="name" placeholder="e.g., Red">
                    <mat-hint>Display name for customer</mat-hint>
                  </mat-form-field>

                  <mat-form-field appearance="outline" class="flex-1">
                    <mat-label>Group</mat-label>
                    <input matInput formControlName="option_group" placeholder="e.g., Color">
                    <mat-hint>Options in same group are alternatives</mat-hint>
                  </mat-form-field>

                  <mat-form-field appearance="outline" class="small">
                    <mat-label>Price +/-</mat-label>
                    <input matInput type="number" formControlName="price_modifier" step="0.01">
                    <span matPrefix>$&nbsp;</span>
                  </mat-form-field>

                  <mat-form-field appearance="outline" class="small">
                    <mat-label>Sort</mat-label>
                    <input matInput type="number" formControlName="sort_order">
                  </mat-form-field>

                  <button mat-icon-button type="button" color="warn" (click)="removeOption(i)">
                    <mat-icon>delete</mat-icon>
                  </button>
                </div>

                <!-- Parts for this option -->
                <div class="option-parts" [formGroupName]="i">
                  <div class="parts-header">
                    <span class="parts-label">Parts for this option:</span>
                    <button mat-stroked-button type="button" (click)="addOptionPart(i)" class="add-part-btn">
                      <mat-icon>add</mat-icon>
                      Add Part
                    </button>
                  </div>

                  <div formArrayName="parts">
                    @for (part of getOptionParts(i).controls; track $index; let j = $index) {
                      <div [formGroupName]="j" class="option-part-item">
                        <mat-form-field appearance="outline" class="flex-2">
                          <mat-label>Part</mat-label>
                          <mat-select formControlName="part_id" required>
                            @for (part of availableParts; track part.id) {
                              <mat-option [value]="part.id">
                                {{ part.part_number }} - {{ part.name }} ({{ part.stock_quantity }})
                              </mat-option>
                            }
                          </mat-select>
                        </mat-form-field>

                        <mat-form-field appearance="outline" class="small">
                          <mat-label>Qty</mat-label>
                          <input matInput type="number" formControlName="quantity" min="1" required>
                        </mat-form-field>

                        <mat-form-field appearance="outline" class="small">
                          <mat-label>OR Group</mat-label>
                          <input matInput type="number" formControlName="alternative_group" placeholder="Optional">
                          <mat-hint>Same # = alternatives</mat-hint>
                        </mat-form-field>

                        <button mat-icon-button type="button" color="warn" (click)="removeOptionPart(i, j)" class="small-btn">
                          <mat-icon>delete</mat-icon>
                        </button>
                      </div>
                    }
                    @if (getOptionParts(i).length === 0) {
                      <div class="no-parts-message">
                        No parts added. Click "Add Part" to link parts to this option.
                      </div>
                    }
                  </div>
                </div>
              </div>
            }
            @if (options.length === 0) {
              <div class="empty-message">
                No options added yet. Add options like colors, sizes, or finishes that customers can choose from.
              </div>
            }
          </div>
        </div>

        <mat-divider></mat-divider>

        <!-- Product Parts (Bill of Materials) -->
        <div class="section">
          <div class="section-header">
            <h3>Base Product BOM</h3>
            <button mat-raised-button type="button" (click)="addProductPart()">
              <mat-icon>add</mat-icon>
              Add Part
            </button>
          </div>
          <p class="help-text">Define parts needed for the base product (regardless of options selected). For option-specific parts (like filament colors), link them to the options above.</p>

          <div formArrayName="product_parts" class="parts-list">
            @for (part of productParts.controls; track $index; let i = $index) {
              <div [formGroupName]="i" class="part-item">
                <mat-form-field appearance="outline">
                  <mat-label>Part</mat-label>
                  <mat-select formControlName="part_id" required>
                    @for (part of availableParts; track part.id) {
                      <mat-option [value]="part.id">
                        {{ part.part_number }} - {{ part.name }} (Stock: {{ part.stock_quantity }})
                      </mat-option>
                    }
                  </mat-select>
                </mat-form-field>

                <mat-form-field appearance="outline">
                  <mat-label>Quantity</mat-label>
                  <input matInput type="number" formControlName="quantity" required min="1">
                </mat-form-field>

                <mat-checkbox formControlName="is_optional">Optional</mat-checkbox>

                <button mat-icon-button type="button" color="warn" (click)="removeProductPart(i)">
                  <mat-icon>delete</mat-icon>
                </button>
              </div>
            }
            @if (productParts.length === 0) {
              <div class="empty-message">
                No base parts added. If this product only varies by options (like different filament colors), you can leave this empty and link parts to options above.
              </div>
            }
          </div>
        </div>

        <mat-divider></mat-divider>

        <!-- Status -->
        <div class="section">
          <h3>Status</h3>
          <div class="checkboxes">
            <mat-checkbox formControlName="is_active">Active</mat-checkbox>
            <mat-checkbox formControlName="featured">Featured</mat-checkbox>
            <mat-checkbox formControlName="allow_order_when_out_of_stock">
              Allow ordering when out of stock (Made to Order)
            </mat-checkbox>
          </div>
        </div>

        <mat-divider></mat-divider>

        <!-- SEO -->
        <div class="section">
          <h3>SEO (Optional)</h3>
          <mat-form-field appearance="outline">
            <mat-label>Meta Title</mat-label>
            <input matInput formControlName="meta_title">
          </mat-form-field>

          <mat-form-field appearance="outline" class="full-width">
            <mat-label>Meta Description</mat-label>
            <textarea matInput formControlName="meta_description" rows="2"></textarea>
          </mat-form-field>
        </div>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button (click)="cancel()">Cancel</button>
      <button mat-raised-button color="primary" (click)="save()" [disabled]="!productForm.valid || saving">
        @if (saving) {
          Saving...
        } @else {
          {{ data.mode === 'create' ? 'Create' : 'Update' }}
        }
      </button>
    </mat-dialog-actions>
  `,
  styles: [`
    mat-dialog-content {
      max-height: 70vh;
      overflow-y: auto;
    }

    .product-form {
      display: flex;
      flex-direction: column;
      gap: 24px;
      padding: 8px;
    }

    .section {
      display: flex;
      flex-direction: column;
      gap: 16px;

      h3 {
        margin: 0;
        color: var(--kumpe-primary);
      }
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;

      button mat-icon {
        margin-right: 8px;
      }
    }

    .row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
    }

    .full-width {
      width: 100%;
    }

    .flex-1 {
      flex: 1;
    }

    .flex-2 {
      flex: 2;
    }

    .small {
      width: 120px;
    }

    .small-btn {
      margin-top: 8px;
    }

    .checkboxes {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .options-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .option-container {
      padding: 16px;
      background: #f5f5f5;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
    }

    .option-header {
      display: grid;
      grid-template-columns: 2fr 1.5fr 120px 80px 40px;
      gap: 12px;
      align-items: start;
      margin-bottom: 16px;
    }

    .option-parts {
      margin-top: 12px;
      padding: 12px;
      background: #fff;
      border-radius: 4px;
      border: 1px solid #e0e0e0;
    }

    .parts-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .parts-label {
      font-weight: 500;
      color: rgba(0, 0, 0, 0.87);
    }

    .add-part-btn {
      min-width: auto;
      mat-icon {
        margin-right: 4px;
        font-size: 18px;
        height: 18px;
        width: 18px;
      }
    }

    .option-part-item {
      display: grid;
      grid-template-columns: 3fr 100px 120px 40px;
      gap: 12px;
      align-items: start;
      margin-bottom: 8px;

      &:last-child {
        margin-bottom: 0;
      }
    }

    .no-parts-message {
      text-align: center;
      padding: 20px;
      color: rgba(0, 0, 0, 0.54);
      font-style: italic;
    }

    .option-item {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 2fr 100px 40px;
      gap: 12px;
      align-items: start;
      padding: 12px;
      background: #f5f5f5;
      border-radius: 4px;
    }

    .parts-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .part-item {
      display: grid;
      grid-template-columns: 3fr 100px 120px 40px;
      gap: 12px;
      align-items: start;
      padding: 12px;
      background: #f5f5f5;
      border-radius: 4px;
    }

    .help-text {
      margin: 0;
      font-size: 14px;
      color: #666;
    }

    .empty-message {
      padding: 24px;
      text-align: center;
      color: #666;
      background: #f9f9f9;
      border-radius: 4px;
      font-style: italic;
    }

    mat-form-field {
      width: 100%;
    }

    mat-divider {
      margin: 8px 0;
    }
  `]
})
export class ProductFormDialogComponent implements OnInit {
  private fb = inject(FormBuilder);
  private productService = inject(ProductService);
  private http = inject(HttpClient);
  private snackBar = inject(MatSnackBar);
  private dialogRef = inject(MatDialogRef<ProductFormDialogComponent>);

  productForm!: FormGroup;
  saving = false;
  availableParts: Part[] = [];

  constructor(@Inject(MAT_DIALOG_DATA) public data: { mode: 'create' | 'edit', product?: Product }) {
    console.log('ProductFormDialog data:', data);
    if (data.product) {
      console.log('Product parts:', (data.product as any).parts);
    }
  }

  ngOnInit() {
    this.loadParts();
    
    this.productForm = this.fb.group({
      sku: [this.data.product?.sku || '', Validators.required],
      title: [this.data.product?.title || '', Validators.required],
      description: [this.data.product?.description || ''],
      base_price: [this.data.product?.base_price || 0, [Validators.required, Validators.min(0)]],
      cost: [this.data.product?.cost || 0],
      weight: [this.data.product?.weight || 0],
      is_active: [this.data.product?.is_active ?? true],
      featured: [this.data.product?.featured || false],
      allow_order_when_out_of_stock: [this.data.product?.allow_order_when_out_of_stock || false],
      meta_title: [this.data.product?.meta_title || ''],
      meta_description: [this.data.product?.meta_description || ''],
      options: this.fb.array([]),
      product_parts: this.fb.array([])
    });

    // Load existing options if editing
    if (this.data.mode === 'edit' && this.data.product?.options) {
      this.data.product.options.forEach(option => {
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
    if (this.data.mode === 'edit' && (this.data.product as any)?.parts) {
      (this.data.product as any).parts.forEach((productPart: any) => {
        this.productParts.push(this.fb.group({
          id: [productPart.id],
          part_id: [productPart.part_id, Validators.required],
          quantity: [productPart.quantity || 1, [Validators.required, Validators.min(1)]],
          is_optional: [productPart.is_optional || false]
        }));
      });
    }
  }

  loadParts() {
    this.http.get<any>(`${environment.apiUrl}/products/admin/parts`).subscribe({
      next: (response) => {
        this.availableParts = response.data || [];
      },
      error: (error) => {
        console.error('Error loading parts:', error);
        this.snackBar.open('Failed to load parts list', 'Close', { duration: 3000 });
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

      const request = this.data.mode === 'create'
        ? this.productService.createProduct(formValue)
        : this.productService.updateProduct(this.data.product!.id || 0, formValue);

      request.subscribe({
        next: () => {
          this.snackBar.open(`Product ${this.data.mode === 'create' ? 'created' : 'updated'} successfully`, 'Close', { duration: 3000 });
          this.dialogRef.close(true);
        },
        error: (error) => {
          console.error('Error saving product:', error);
          this.snackBar.open(error.error?.error?.message || 'Failed to save product', 'Close', { duration: 5000 });
          this.saving = false;
        }
      });
    }
  }

  cancel() {
    this.dialogRef.close();
  }
}

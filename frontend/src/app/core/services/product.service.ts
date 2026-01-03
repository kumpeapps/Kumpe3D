import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '@environments/environment';
import { Product, Category, Part, APIResponse } from '@core/models';

export interface ProductListParams {
  page?: number;
  per_page?: number;
  category_id?: number;
  search?: string;
  in_stock?: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class ProductService {
  private http = inject(HttpClient);
  private apiUrl = `${environment.apiUrl}/products/`;

  /**
   * Get list of products with pagination and filtering
   */
  getProducts(params: ProductListParams = {}): Observable<APIResponse<Product[]>> {
    let httpParams = new HttpParams();
    
    if (params.page) httpParams = httpParams.set('page', params.page.toString());
    if (params.per_page) httpParams = httpParams.set('per_page', params.per_page.toString());
    if (params.category_id) httpParams = httpParams.set('category_id', params.category_id.toString());
    if (params.search) httpParams = httpParams.set('search', params.search);
    if (params.in_stock !== undefined) httpParams = httpParams.set('in_stock', params.in_stock.toString());

    return this.http.get<APIResponse<Product[]>>(this.apiUrl, { params: httpParams });
  }

  /**
   * Get single product by ID
   */
  getProduct(id: number): Observable<APIResponse<Product>> {
    return this.http.get<APIResponse<Product>>(`${this.apiUrl}${id}`);
  }

  /**
   * Get all categories
   */
  getCategories(): Observable<APIResponse<Category[]>> {
    return this.http.get<APIResponse<Category[]>>(`${environment.apiUrl}/products/categories/`);
  }

  /**
   * Get available parts for a product
   */
  getProductParts(productId: number): Observable<APIResponse<Part[]>> {
    return this.http.get<APIResponse<Part[]>>(`${this.apiUrl}${productId}/options`);
  }

  /**
   * Create product (admin only)
   */
  createProduct(product: Partial<Product>): Observable<APIResponse<Product>> {
    return this.http.post<APIResponse<Product>>(this.apiUrl, product);
  }

  /**
   * Update product (admin only)
   */
  updateProduct(idOrSku: number | string, product: Partial<Product>): Observable<APIResponse<Product>> {
    return this.http.put<APIResponse<Product>>(`${this.apiUrl}${idOrSku}`, product);
  }

  /**
   * Delete product (admin only)
   */
  deleteProduct(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}${id}`);
  }
}

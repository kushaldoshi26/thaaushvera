<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Get all products with pagination and filtering
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 12);
            $search = $request->query('search', '');
            $category = $request->query('category', '');
            $sortBy = $request->query('sort_by', 'created_at');
            $sortOrder = $request->query('sort_order', 'desc');

            $query = Product::query();

            // Search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Category filter
            if (!empty($category)) {
                if (is_numeric($category)) {
                    $query->where('category_id', $category);
                } else {
                    $query->where('category', $category);
                }
            }

            // Sorting
            $query->orderBy($sortBy, $sortOrder);

            // Paginate
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single product by ID
     */
    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new product (Admin only)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'discount' => 'nullable|integer|min:0|max:100',
                'stock' => 'nullable|integer|min:0',
                'category_id' => 'nullable|integer',
                'image' => 'nullable|string',
                'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
                'display_images' => 'nullable|string'
            ]);

            if ($request->hasFile('image_file')) {
                $file = $request->file('image_file');
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-.]/', '', $file->getClientOriginalName());
                $file->move(public_path('assets/products'), $filename);
                $validated['image'] = asset('assets/products/' . $filename);
                unset($validated['image_file']);
            }

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product (Admin only)
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'discount' => 'nullable|integer|min:0|max:100',
                'stock' => 'nullable|integer|min:0',
                'category_id' => 'nullable|integer',
                'image' => 'nullable|string',
                'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5048',
                'display_images' => 'nullable|string'
            ]);

            if ($request->hasFile('image_file')) {
                $file = $request->file('image_file');
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-.]/', '', $file->getClientOriginalName());
                $file->move(public_path('assets/products'), $filename);
                $validated['image'] = asset('assets/products/' . $filename);
                unset($validated['image_file']);
            }

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product (Admin only)
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($product) {
                if(method_exists($product, 'reviews')) $product->reviews()->delete();
                if(method_exists($product, 'cartItems')) $product->cartItems()->delete();
                if(method_exists($product, 'stockHistory')) $product->stockHistory()->delete();
                if(method_exists($product, 'orderItems')) $product->orderItems()->delete();
                
                $product->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

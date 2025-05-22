<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    // Get Method for Fetching the product
    public function show($slug) {
        try {
            $product = Product::with(['images', 'discount'])->where('slug', $slug)->firstOrFail();

            // if (!$product) {
            //     return response()->json(['error' => 'Product not found'], 404);
            // }

            $fullPrice = $product->price;
            $discountedPrice = $fullPrice;
            $discountData = null;

            if ($product->discount) {
                $discountData = [
                    'type' => $product->discount->type,
                    'amount' => $product->discount->discount,
                ];

                if ($product->discount->type === 'percent') {
                    $discountedPrice = $fullPrice - ($fullPrice * $product->discount->discount / 100);
                } else {
                    $discountedPrice = $fullPrice - $product->discount->discount;
                }
            }

            $response = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'slug' => $product->slug,
                'price' => [
                    'full' => $fullPrice,
                    'discounted' => $discountedPrice,
                ],
                'discount' => $discountData,
                'images' => $product->images->pluck('path')->toArray(),
            ];

            return response()->json($response); 
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } 
        catch (\Exception $e) {
             return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }

    // POST Method
    public function store(Request $request) {
        try {
            $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|unique:products',
            'price' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => $product
        ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
             return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }

    // Update Method
    public function update(Request $request, Product $product) {
        try {
            $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'slug' => 'string|unique:products,slug,' . $product->id,
            'price' => 'integer|min:0',
            'active' => 'boolean',
            ]);

            $product->update($validated);
            return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
            ]); 
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }

    // Delete Method
    public function destroy(Product $product) {
        try {
             $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }
}

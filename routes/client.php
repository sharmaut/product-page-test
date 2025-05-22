<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
|
| Route related to the client application test
|
*/

Route::get('products/{slug}', function ($slug) {
  if ($slug !== 'fall-limited-edition-sneakers') {
    return response()->json([
      'data' => null,
      'msg' => 'Item not found.'
    ], 404);
  }
  return response()->json([
    'data' => [
      'id' => '1',
      'name' => 'Fall Limited Edition Sneakers',
      'description' => 'These low-profile sneakers are your perfect casual wear companion. Featuring a durable rubber outer sole, they\'ll withstand everything the weather can offer.',
      'price' => [
        'full' => 250,
        'discounted' => 125
      ],
      'discount' => [
        'type' => 'percent',
        'amount' => 50
      ],
      'images' => [
        '/images/image-product-1.jpg',
        '/images/image-product-2.jpg',
        '/images/image-product-3.jpg',
        '/images/image-product-4.jpg',
      ]
    ]
  ]);
});

Route::post('products', function (Request $request) {
    // Income request data
    $validated = $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        'slug'        => 'required|string',
        'price'       => 'required|integer|min:0',
        'active'      => 'sometimes|boolean',
    ]);

    $product = $validated;
    $product['id'] = 2; 

    return response()->json([
        'msg'  => 'Product created successfully.',
        'data' => $product,
    ], 201);
});

Route::put('products/{id}', function ($id, Request $request) {

    $validated = $request->validate([
        'name'        => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'slug'        => 'sometimes|required|string',
        'price'       => 'sometimes|required|integer|min:0',
        'active'      => 'sometimes|boolean',
    ]);

    $existingProduct = [
        'id'          => $id,
        'name'        => 'Existing Test Product',
        'description' => 'Existing product description.',
        'slug'        => 'existing-product-slug',
        'price'       => 300,
        'active'      => true,
    ];

    $updatedProduct = array_merge($existingProduct, $validated);

    return response()->json([
        'msg'  => 'Product updated successfully.',
        'data' => $updatedProduct
    ], 200);
});

Route::delete('products/{id}', function ($id) {

    return response()->json([
        'msg' => "Product with id {$id} deleted successfully."
    ], 200);
});
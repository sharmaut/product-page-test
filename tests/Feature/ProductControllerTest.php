<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductImage;
use App\Models\ProductDiscount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user and use Sanctum to simulate an authenticated request
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);
    }

    public function test_can_retrieve_product_by_slug()
    {
        // Create a product record
        $product = Product::factory()->create([
            'name'        => 'Test Product',
            'slug'        => 'test-product',
            'price'       => 100,
            'description' => 'Short description'
        ]);

        // Create two image records
        ProductImage::factory()->create([
            'product_id' => $product->id,
            'path'       => 'image-path-1.png'
        ]);
        ProductImage::factory()->create([
            'product_id' => $product->id,
            'path'       => 'image-path-2.png'
        ]);

        // Create a discount record
        ProductDiscount::factory()->create([
            'product_id' => $product->id,
            'type'       => 'percent',
            'discount'   => 20
        ]);

        // Send GET request using the product's slug
        $response = $this->getJson("/api/products/{$product->slug}");

        // 20% discount to compute discount
        $response->assertStatus(200)
                 ->assertJson([
                     'name'   => 'Test Product',
                     'slug'   => 'test-product',
                     'price'  => [
                         'full'       => 100,
                         'discounted' => 80,
                     ],
                     'discount' => [
                         'type'   => 'percent',
                         'amount' => 20,
                     ],
                 ])
                 ->assertJsonStructure([
                     'id', 'name', 'description', 'slug', 'price' => ['full', 'discounted'], 'discount' => ['type', 'amount'], 'images'
                 ]);
    }

    public function test_product_creation_validation_error()
    {
        $data = [
            'description' => 'Missing required fields'
        ];

        $response = $this->postJson('/api/products', $data);
        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }

    public function test_can_create_product()
    {
        $data = [
            'name'        => 'New Product',
            'description' => 'A very nice product.',
            'slug'        => 'new-product',
            'price'       => 150,
            'active'      => true,
        ];

        $response = $this->postJson('/api/products', $data);
        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'New Product']);

        $this->assertDatabaseHas('products', [
            'slug' => 'new-product'
        ]);
    }

    public function test_can_update_product()
    {
        // Creating a product record 
        $product = Product::factory()->create([
            'name'        => 'Old Product',
            'slug'        => 'old-product',
            'price'       => 100,
            'description' => 'Old description'
        ]);

        $updateData = [
            'name'  => 'Updated Product',
            'price' => 200,
        ];

        // Since the update route is bound via Product model, we use the product ID
        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Product updated successfully'])
                 ->assertJsonFragment(['name' => 'Updated Product']);

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'Updated Product',
            'price' => 200,
        ]);
    }

    public function test_can_delete_product()
    {
        // Create a product record
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Product deleted successfully']);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
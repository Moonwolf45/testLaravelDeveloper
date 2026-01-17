<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products_with_default_params()
    {
        // Arrange
        $category = Category::factory()->create(['name' => 'Electronics']);
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'in_stock' => true,
            'rating' => 4.5,
        ]);

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'items' => [['id', 'name', 'price', 'category_id', 'category', 'in_stock', 'rating', 'created_at', 'updated_at']],
                'total',
                'page',
                'limit',
                'pages'
            ])
            ->assertJsonCount(3, 'items');
    }

    public function test_can_filter_by_name()
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'iPhone 15', 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Samsung Galaxy', 'category_id' => $category->id]);

        // Act
        $response = $this->getJson('/api/products?q=iPhone');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'items')
            ->assertJsonFragment(['name' => 'iPhone 15']);
    }

    public function test_can_filter_by_price_range()
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create(['price' => 300, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 800, 'category_id' => $category->id]);
        Product::factory()->create(['price' => 1200, 'category_id' => $category->id]);

        // Act
        $response = $this->getJson('/api/products?price_from=500&price_to=1000');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'items')
            ->assertJsonFragment(['price' => 800]);
    }

    public function test_can_filter_by_category_id()
    {
        // Arrange
        $cat1 = Category::factory()->create(['name' => 'Phones']);
        $cat2 = Category::factory()->create(['name' => 'Laptops']);

        Product::factory()->create(['category_id' => $cat1->id]);
        Product::factory()->create(['category_id' => $cat2->id]);

        // Act
        $response = $this->getJson("/api/products?category_id={$cat1->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'items')
            ->assertJsonFragment(['category_id' => $cat1->id]);
    }

    public function test_can_filter_by_in_stock()
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'in_stock' => true]);
        Product::factory()->create(['category_id' => $category->id, 'in_stock' => false]);

        // Act
        $response = $this->getJson('/api/products?in_stock=true');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'items')
            ->assertJsonFragment(['in_stock' => true]);
    }

    public function test_can_filter_by_rating()
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'rating' => 3.2]);
        Product::factory()->create(['category_id' => $category->id, 'rating' => 4.8]);

        // Act
        $response = $this->getJson('/api/products?rating_from=4.0');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'items')
            ->assertJsonFragment(['rating' => 4.8]);
    }

    public function test_can_sort_by_price_asc()
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'price' => 1000]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 500]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 800]);

        // Act
        $response = $this->getJson('/api/products?sort=price_asc');

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('items.0.price', 500)
            ->assertJsonPath('items.1.price', 800)
            ->assertJsonPath('items.2.price', 1000);
    }

    public function test_can_sort_by_rating_desc()
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'rating' => 2.1]);
        Product::factory()->create(['category_id' => $category->id, 'rating' => 4.9]);
        Product::factory()->create(['category_id' => $category->id, 'rating' => 3.5]);

        // Act
        $response = $this->getJson('/api/products?sort=rating_desc');

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('items.0.rating', 4.9)
            ->assertJsonPath('items.1.rating', 3.5)
            ->assertJsonPath('items.2.rating', 2.1);
    }

    public function test_can_sort_by_newest()
    {
        // Arrange
        $category = Category::factory()->create();

        // Создаем продукты с явными датами
        $product1 = Product::factory()->create([
            'category_id' => $category->id,
            'created_at' => now()->subDays(2),
        ]);

        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'created_at' => now(),
        ]);

        $product3 = Product::factory()->create([
            'category_id' => $category->id,
            'created_at' => now()->subDays(1),
        ]);

        // Act
        $response = $this->getJson('/api/products?sort=newest');

        // Assert
        $response->assertStatus(200)
            ->assertJsonPath('items.0.id', $product2->id) // newest
            ->assertJsonPath('items.1.id', $product3->id)
            ->assertJsonPath('items.2.id', $product1->id);
    }

    public function test_pagination_works()
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->count(25)->create(['category_id' => $category->id]);

        // Act
        $response = $this->getJson('/api/products?limit=10&page=2');

        // Assert
        $response->assertStatus(200)
            ->assertJson(['limit' => 10, 'page' => 2, 'pages' => 3, 'total' => 25])
            ->assertJsonCount(10, 'items');
    }

    public function test_invalid_sort_returns_422()
    {
        // Act
        $response = $this->getJson('/api/products?sort=invalid_sort');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors('sort');
    }

    public function test_limit_exceeds_max_100()
    {
        // Act
        $response = $this->getJson('/api/products?limit=150');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors('limit');
    }
}

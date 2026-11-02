<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_errors_are_json_even_without_accept_header(): void
    {
        $this->get('/api/me')->assertUnauthorized()->assertHeader('Content-Type', 'application/json');
        $this->post('/api/register', [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'password']);
        $this->get('/api/products/99999')->assertNotFound()->assertHeader('Content-Type', 'application/json');
        $this->get('/api/unknown')->assertNotFound()->assertHeader('Content-Type', 'application/json');
    }

    public function test_product_numbers_fit_mysql_columns(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
        $category = Category::create(['name' => 'Numbers']);
        $brand = Brand::create(['name' => 'Numbers']);
        $payload = ['name' => 'Boundary', 'price' => '100000000.00', 'stock' => 2147483648,
            'category_id' => $category->id, 'brand_id' => $brand->id];
        $this->postJson('/api/products', $payload)->assertUnprocessable()
            ->assertJsonValidationErrors(['price', 'stock']);
        $payload['price'] = '99999999.99';
        $payload['stock'] = 2147483647;
        $id = $this->postJson('/api/products', $payload)->assertCreated()->json('data.id');
        $this->patchJson('/api/products/'.$id, ['price' => '100000000.00', 'stock' => 2147483648])
            ->assertUnprocessable()->assertJsonValidationErrors(['price', 'stock']);
        $this->assertDatabaseHas('products', ['id' => $id, 'stock' => 2147483647]);
    }

    public function test_zero_keyword_is_a_real_search(): void
    {
        $category = Category::create(['name' => 'Search']);
        $brand = Brand::create(['name' => 'Search']);
        foreach (['Mouse 0', 'Keyboard'] as $name) {
            Product::create(['name' => $name, 'price' => 1, 'stock' => 1,
                'category_id' => $category->id, 'brand_id' => $brand->id]);
        }
        $this->getJson('/api/products?keyword=0')->assertOk()
            ->assertJsonPath('total', 1)->assertJsonPath('data.0.name', 'Mouse 0');
    }

    public function test_unvalidated_fields_cannot_change_ids_or_leak_passwords(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $category = Category::create(['name' => 'Safe']);
        $brand = Brand::create(['name' => 'Safe']);
        $response = $this->postJson('/api/products', ['id' => 12345, 'name' => 'Safe product',
            'price' => 1, 'stock' => 1, 'category_id' => $category->id, 'brand_id' => $brand->id,
            'created_at' => '2000-01-01', 'is_admin' => true])->assertCreated();
        $this->assertNotSame(12345, $response->json('data.id'));
        $this->getJson('/api/me')->assertOk()->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.remember_token');
    }

    public function test_non_string_credentials_cannot_bypass_sanctum(): void
    {
        User::factory()->create();
        $this->getJson('/api/me?api_token%5B%5D=0')->assertUnauthorized();
        $this->getJson('/api/me?api_token=0')->assertUnauthorized();
        $this->postJson('/api/login', ['email' => ['unexpected'], 'password' => 'wrong'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_product_image_url_matches_the_teacher_mockup(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
        $category = Category::create(['name' => 'Images']);
        $brand = Brand::create(['name' => 'Images']);
        $payload = ['name' => 'Image product', 'price' => 100, 'stock' => 3,
            'category_id' => $category->id, 'brand_id' => $brand->id,
            'image_url' => 'https://example.test/product.jpg'];
        $response = $this->postJson('/api/products', $payload)->assertCreated()
            ->assertJsonPath('data.image_url', $payload['image_url']);
        $id = $response->json('data.id');
        $this->getJson('/api/products/'.$id)->assertOk()
            ->assertJsonPath('data.image_url', $payload['image_url']);
        $this->patchJson('/api/products/'.$id, ['image_url' => 'javascript:alert(1)'])
            ->assertUnprocessable()->assertJsonValidationErrors('image_url');
        $this->patchJson('/api/products/'.$id, ['image_url' => null])->assertOk()
            ->assertJsonPath('data.image_url', null);
    }
}

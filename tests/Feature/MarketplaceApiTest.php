<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MarketplaceApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(Category $category, Brand $brand, array $changes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Mouse Wireless',
            'description' => 'Produk untuk test',
            'price' => 120000,
            'stock' => 10,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ], $changes));
    }

    private function authToken(): string
    {
        $user = User::create([
            'name' => 'Tester API',
            'email' => 'tester@example.test',
            'password' => Hash::make('SecretPass123!'),
        ]);
        return $user->createToken('test-api')->plainTextToken;
    }

    public function test_suite_uses_an_isolated_sqlite_database(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_register_login_profile_logout_and_revocation(): void
    {
        $payload = [
            'name' => 'Siswa Timedoor',
            'email' => 'siswa@example.test',
            'password' => 'SecretPass123!',
            'password_confirmation' => 'SecretPass123!',
        ];

        $this->postJson('/api/register', $payload)
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'siswa@example.test')
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertTrue(Hash::check('SecretPass123!', User::firstOrFail()->password));

        $login = $this->postJson('/api/login', [
            'email' => 'siswa@example.test',
            'password' => 'SecretPass123!',
        ])->assertOk();
        $token = $login->json('data.token');
        $this->assertIsString($token);
        $this->withToken($token)->getJson('/api/me')
            ->assertOk()->assertJsonPath('data.email', 'siswa@example.test');
        $this->withToken($token)->getJson('/api/user')
            ->assertOk()->assertJsonPath('email', 'siswa@example.test');
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 1);
        // Laravel's in-process test guard caches a user across HTTP calls.
        // Actual requests use a new guard; reset it here before reusing revoked token.
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/me')->assertUnauthorized();
    }

    public function test_invalid_or_duplicate_registration_and_bad_login(): void
    {
        $this->postJson('/api/register', [
            'name' => 'A', 'email' => 'not-an-email', 'password' => 'abc',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);

        $user = User::create([
            'name' => 'Existing User',
            'email' => 'taken@example.test',
            'password' => Hash::make('SecretPass123!'),
        ]);
        $this->postJson('/api/register', [
            'name' => 'Another',
            'email' => $user->email,
            'password' => 'SecretPass123!',
            'password_confirmation' => 'SecretPass123!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-pass',
        ])->assertUnauthorized();
    }

    public function test_public_read_and_protected_writes(): void
    {
        $this->getJson('/api/products')->assertOk();
        $this->getJson('/api/categories')->assertOk();
        $this->getJson('/api/brands')->assertOk();
        $this->getJson('/api/me')->assertUnauthorized();

        $this->postJson('/api/products', [
            'name' => 'Blocked', 'price' => 100, 'stock' => 1,
            'category_id' => 1, 'brand_id' => 1,
        ])->assertUnauthorized();
        $this->postJson('/api/categories', ['name' => 'Blocked'])->assertUnauthorized();
        $this->postJson('/api/brands', ['name' => 'Blocked'])->assertUnauthorized();
    }

    public function test_product_crud_and_partial_update(): void
    {
        $category = Category::create(['name' => 'Elektronik']);
        $brand = Brand::create(['name' => 'TeknoID']);
        $token = $this->authToken();

        $create = $this->withToken($token)->postJson('/api/products', [
            'name' => 'Wireless Keyboard',
            'price' => 250000,
            'stock' => 8,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ])->assertCreated()->assertJsonPath('data.name', 'Wireless Keyboard');

        $id = $create->json('data.id');
        $this->getJson('/api/products/' . $id)
            ->assertOk()->assertJsonPath('data.category.name', 'Elektronik');

        $this->withToken($token)->putJson('/api/products/' . $id, [
            'name' => 'Mechanical Keyboard',
            'description' => 'Update lewat API',
            'price' => 275000,
            'stock' => 5,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ])->assertOk()->assertJsonPath('data.name', 'Mechanical Keyboard');

        $this->withToken($token)->patchJson('/api/products/' . $id, [
            'stock' => 3,
        ])->assertOk()->assertJsonPath('data.stock', 3);

        $this->withToken($token)->deleteJson('/api/products/' . $id)->assertNoContent();
        $this->getJson('/api/products/' . $id)->assertNotFound();
    }

    public function test_product_validation_and_not_found(): void
    {
        $token = $this->authToken();

        $this->withToken($token)->postJson('/api/products', [
            'name' => '', 'price' => -2, 'stock' => -1, 'category_id' => 999999,
            'brand_id' => 999999,
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['name', 'price', 'stock', 'category_id', 'brand_id']);

        $this->withToken($token)->putJson('/api/products/99999', [
            'name' => 'Lost', 'price' => 1, 'stock' => 1,
            'category_id' => 999, 'brand_id' => 999,
        ])->assertNotFound();

        $this->getJson('/api/products/99999')->assertNotFound();
        $this->getJson('/api/products?per_page=101')->assertUnprocessable();
        $this->getJson('/api/products?sort=DROP_TABLE')->assertUnprocessable();
        $this->getJson('/api/products?category_id=999999')->assertUnprocessable();
    }

    public function test_search_filter_sort_and_pagination_combined(): void
    {
        $cat1 = Category::create(['name' => 'Elektronik']);
        $cat2 = Category::create(['name' => 'Pakaian']);
        $brand1 = Brand::create(['name' => 'TeknoID']);
        $brand2 = Brand::create(['name' => 'NusaWear']);
        $this->makeProduct($cat1, $brand1, ['name' => 'Mouse Basic', 'price' => 50000]);
        $this->makeProduct($cat1, $brand1, ['name' => 'Mouse Pro', 'price' => 300000]);
        $this->makeProduct($cat1, $brand1, ['name' => 'Mouse Lite', 'price' => 90000]);
        $this->makeProduct($cat2, $brand2, ['name' => 'Mousepad', 'price' => 20000]);

        $url = '/api/products?keyword=Mouse&category_id='.$cat1->id
            .'&brand_id='.$brand1->id.'&sort=price_desc&per_page=2&page=1';
        $first = $this->getJson($url)->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('current_page', 1);
        $this->assertSame(['Mouse Pro', 'Mouse Lite'], array_column($first->json('data'), 'name'));

        $second = $this->getJson(str_replace('page=1', 'page=2', $url))
            ->assertOk()->assertJsonPath('current_page', 2);
        $this->assertSame(['Mouse Basic'], array_column($second->json('data'), 'name'));

        $ascending = $this->getJson('/api/products?keyword=Mouse&sort=price_asc')
            ->assertOk();
        $this->assertSame(['Mousepad', 'Mouse Basic', 'Mouse Lite', 'Mouse Pro'],
            array_column($ascending->json('data'), 'name'));
    }

    public function test_category_and_brand_crud_and_relation_conflict(): void
    {
        $token = $this->authToken();

        $categoryId = $this->withToken($token)
            ->postJson('/api/categories', ['name' => 'Elektronik'])
            ->assertCreated()->json('data.id');
        $brandId = $this->withToken($token)
            ->postJson('/api/brands', ['name' => 'TeknoID'])
            ->assertCreated()->json('data.id');

        $this->getJson('/api/categories/' . $categoryId)
            ->assertOk()->assertJsonPath('data.name', 'Elektronik');
        $this->getJson('/api/brands/' . $brandId)
            ->assertOk()->assertJsonPath('data.name', 'TeknoID');

        $this->withToken($token)->putJson('/api/categories/' . $categoryId, [
            'name' => 'Komputer',
        ])->assertOk()->assertJsonPath('data.name', 'Komputer');
        $this->withToken($token)->patchJson('/api/brands/' . $brandId, [
            'name' => 'Tekno Baru',
        ])->assertOk()->assertJsonPath('data.name', 'Tekno Baru');

        $cat = Category::findOrFail($categoryId);
        $brand = Brand::findOrFail($brandId);
        $product = $this->makeProduct($cat, $brand);

        $this->withToken($token)->deleteJson('/api/categories/' . $categoryId)
            ->assertStatus(409);
        $this->withToken($token)->deleteJson('/api/brands/' . $brandId)
            ->assertStatus(409);

        $product->delete();
        $this->withToken($token)->deleteJson('/api/categories/' . $categoryId)
            ->assertNoContent();
        $this->withToken($token)->deleteJson('/api/brands/' . $brandId)
            ->assertNoContent();

        $this->getJson('/api/categories/' . $categoryId)->assertNotFound();
        $this->getJson('/api/brands/' . $brandId)->assertNotFound();
    }

    public function test_repeatable_seeder_creates_five_categories_five_brands_and_25_products(): void
    {
        $this->seed();
        $this->assertSame(5, Category::count());
        $this->assertSame(5, Brand::count());
        $this->assertSame(25, Product::count());
        $this->assertTrue(Category::withCount('products')->get()
            ->every(fn ($category) => $category->products_count === 5));

        $this->seed();
        $this->assertSame(25, Product::count());
    }
}

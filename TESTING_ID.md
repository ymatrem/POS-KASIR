# ✅ DOKUMENTASI TESTING & BEST PRACTICES

**Bahasa**: Bahasa Indonesia  
**Framework**: Laravel 11 dengan PHPUnit

---

## 📋 DAFTAR ISI

1. [Testing Overview](#testing-overview)
2. [Feature Testing](#feature-testing)
3. [Unit Testing](#unit-testing)
4. [Best Practices](#best-practices)
5. [Ujian Kompetensi Tips](#ujian-kompetensi-tips)

---

## 🧪 Testing Overview

### Mengapa Testing Penting?

Testing memastikan:
- ✅ Code berfungsi sesuai spec
- ✅ Tidak ada regression (fitur lama error)
- ✅ Code quality terjaga
- ✅ Mudah refactor tanpa takut

### Tipe Testing

| Tipe | Scope | Contoh | Kecepatan |
|------|-------|--------|----------|
| **Unit** | Single function/method | Model method, validation logic | ⚡ Sangat cepat |
| **Feature** | API/Route endpoint | Create order via POST, login | ⚡ Cepat |
| **Integration** | Multiple components | Order creation + stock update | ⚡⚡ Sedang |
| **E2E** | Full user flow | User login → order → checkout | 🐢 Lambat |

Project ini fokus pada **Feature Testing** (testing via HTTP requests).

---

## 🧬 Feature Testing

### Lokasi Files

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegisterTest.php
│   ├── Menu/
│   │   ├── MenuCreateTest.php
│   │   ├── MenuUpdateTest.php
│   │   └── MenuDeleteTest.php
│   └── Order/
│       └── OrderCreateTest.php
└── TestCase.php (Base class)
```

---

### Setup TestCase

**File**: `tests/TestCase.php`

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;  // Reset database setelah setiap test
}
```

**`RefreshDatabase` trait**:
- Migrate database fresh sebelum test
- Rollback setelah test
- Database test isolated dan clean

---

### Example 1: Login Feature Test

**File**: `tests/Feature/Auth/LoginTest.php`

```php
<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        // Arrange: Buat user untuk testing
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Act: Submit login form
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert: Check hasil
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_password()
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_email()
    {
        // Act
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
```

**Penjelasan**:

1. **Arrange**: Setup data untuk test
   - `User::factory()->create()`: Buat user dummy menggunakan factory

2. **Act**: Jalankan action yang ditest
   - `$this->post()`: Simulate POST request ke `/login`

3. **Assert**: Verify hasil
   - `assertRedirect()`: Check redirect to `/`
   - `assertAuthenticatedAs()`: Check user authenticated
   - `assertGuest()`: Check user not authenticated

---

### Example 2: Menu CRUD Test

**File**: `tests/Feature/Menu/MenuCreateTest.php`

```php
<?php

namespace Tests\Feature\Menu;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuCreateTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup: Buat user & category untuk semua test
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_create_menu()
    {
        // Act: POST /menus dengan data
        $response = $this->actingAs($this->user)
            ->post('/menus', [
                'name' => 'Cheese Burger',
                'description' => 'Burger lezat',
                'price' => 35000,
                'category_id' => $this->category->id,
            ]);

        // Assert: Check redirect & database
        $response->assertRedirect('/menus');
        $this->assertDatabaseHas('menus', [
            'name' => 'Cheese Burger',
            'price' => 35000,
        ]);
    }

    /** @test */
    public function menu_name_is_required()
    {
        // Act: POST tanpa name
        $response = $this->actingAs($this->user)
            ->post('/menus', [
                'description' => 'Burger lezat',
                'price' => 35000,
                'category_id' => $this->category->id,
            ]);

        // Assert: Check validation error
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function menu_price_must_be_numeric()
    {
        // Act
        $response = $this->actingAs($this->user)
            ->post('/menus', [
                'name' => 'Burger',
                'price' => 'bukan-angka',  // Invalid
                'category_id' => $this->category->id,
            ]);

        // Assert
        $response->assertSessionHasErrors('price');
    }

    /** @test */
    public function unauthenticated_user_cannot_create_menu()
    {
        // Act: POST tanpa login
        $response = $this->post('/menus', [
            'name' => 'Burger',
            'price' => 35000,
        ]);

        // Assert: Redirect ke login
        $response->assertRedirect('/login');
    }
}
```

**Common Assertions**:

```php
// Response checks
$response->assertOk();                    // Status 200
$response->assertStatus(201);             // Status 201
$response->assertRedirect('/menus');      // Redirect to URL
$response->assertSessionHasErrors('name'); // Validation error

// Database checks
$this->assertDatabaseHas('menus', [
    'name' => 'Cheese Burger',
]);
$this->assertDatabaseMissing('menus', [
    'name' => 'Deleted Menu',
]);

// Auth checks
$this->assertAuthenticatedAs($user);     // User authenticated
$this->assertGuest();                    // User not authenticated

// Content checks
$response->assertSee('Menu berhasil');   // Check HTML contains text
$response->assertDontSee('Error');       // Check HTML doesn't contain
```

---

### Example 3: Order Creation Test

**File**: `tests/Feature/Order/OrderCreateTest.php`

```php
<?php

namespace Tests\Feature\Order;

use Tests\TestCase;
use App\Models\User;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_order_with_items()
    {
        // Arrange
        $user = User::factory()->create();
        $menu1 = Menu::factory()->create(['price' => 35000]);
        $menu2 = Menu::factory()->create(['price' => 45000]);

        // Act
        $response = $this->actingAs($user)
            ->post('/orders', [
                'total_amount' => 115000,
                'total_quantity' => 3,
                'payment_method' => 'cash',
                'items' => [
                    [
                        'menu_id' => $menu1->id,
                        'quantity' => 1,
                        'price' => 35000,
                        'discount' => 0,
                    ],
                    [
                        'menu_id' => $menu2->id,
                        'quantity' => 2,
                        'price' => 45000,
                        'discount' => 5000,
                    ],
                ],
            ]);

        // Assert
        $response->assertRedirect('/orders');
        
        // Check order created
        $this->assertDatabaseHas('orders', [
            'total_amount' => 115000,
            'payment_method' => 'cash',
        ]);

        // Check order items created
        $order = Order::latest()->first();
        $this->assertCount(2, $order->items);
        
        // Check sold_quantity updated
        $menu1->refresh();
        $this->assertEquals(1, $menu1->sold_quantity);
    }

    /** @test */
    public function order_requires_at_least_one_item()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->post('/orders', [
                'total_amount' => 0,
                'total_quantity' => 0,
                'payment_method' => 'cash',
                'items' => [],  // Empty items
            ]);

        // Assert
        $response->assertSessionHasErrors('items');
    }
}
```

---

## 🔬 Unit Testing

### Contoh 1: Model Method Test

**File**: `tests/Unit/Menu/MenuTest.php`

```php
<?php

namespace Tests\Unit\Menu;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function menu_belongs_to_category()
    {
        // Arrange
        $category = Category::factory()->create();
        $menu = Menu::factory()->create(['category_id' => $category->id]);

        // Act & Assert
        $this->assertTrue($menu->category->is($category));
    }

    /** @test */
    public function menu_price_is_cast_to_decimal()
    {
        // Arrange
        $menu = Menu::factory()->create(['price' => 35000.50]);

        // Assert: Check cast to decimal
        $this->assertIsString($menu->price);  // Decimal casted to string
        $this->assertEquals('35000.50', $menu->price);
    }

    /** @test */
    public function sold_quantity_increments_correctly()
    {
        // Arrange
        $menu = Menu::factory()->create(['sold_quantity' => 10]);

        // Act
        $menu->increment('sold_quantity', 5);

        // Assert
        $this->assertEquals(15, $menu->sold_quantity);
    }
}
```

---

## 📚 Best Practices

### 1. Test Naming Convention

```php
// ✅ GOOD: Deskriptif, jelas apa yang ditest
/** @test */
public function user_can_create_menu_with_valid_data() {}

/** @test */
public function menu_creation_fails_when_name_is_missing() {}

/** @test */
public function authenticated_user_can_view_dashboard() {}

// ❌ BAD: Tidak jelas
/** @test */
public function test1() {}

/** @test */
public function testMenu() {}
```

---

### 2. AAA Pattern (Arrange-Act-Assert)

```php
/** @test */
public function example()
{
    // ARRANGE: Setup test data
    $user = User::factory()->create();
    $menu = Menu::factory()->create();

    // ACT: Perform action
    $response = $this->actingAs($user)
        ->post('/orders', [...]);

    // ASSERT: Verify result
    $response->assertOk();
    $this->assertDatabaseHas('orders', [...]);
}
```

---

### 3. Test Isolation

Setiap test harus independent:

```php
// ❌ BAD: Dependent on previous test
public function test_first() {
    $user = User::create([...]);  // Global state
}

public function test_second() {
    $user = User::first();  // Depends on test_first
}

// ✅ GOOD: Each test is independent
public function test_first() {
    $user = User::factory()->create();
    // ...
}

public function test_second() {
    $user = User::factory()->create();
    // ...
}
```

---

### 4. Factory Usage

```php
// Use factories untuk test data
$user = User::factory()->create();
$menu = Menu::factory()->create(['price' => 50000]);
$orders = Order::factory()->count(10)->create();

// With relationships
$order = Order::factory()
    ->has(OrderItem::factory()->count(3))
    ->create();
```

---

### 5. Database Assertions

```php
// Assert data exists
$this->assertDatabaseHas('menus', [
    'name' => 'Burger',
    'price' => 35000,
]);

// Assert data doesn't exist
$this->assertDatabaseMissing('orders', [
    'id' => 999,
]);

// Assert count
$this->assertDatabaseCount('menus', 5);
```

---

### 6. Mocking External Services

```php
// Mock Payment Gateway
$this->mock(PaymentGateway::class, function ($mock) {
    $mock->shouldReceive('process')
        ->with(150000)
        ->andReturn(['status' => 'success']);
});

// Mock Email
Mail::fake();
// ... perform action
Mail::assertSent(OrderConfirmation::class);
```

---

## 🎯 Ujian Kompetensi Tips

### 1. Testing Checklist

Saat ujian, pastikan Anda test:

```
✅ Authentication
  - Login dengan valid credentials
  - Login dengan invalid credentials
  - Register user baru
  - Logout user
  
✅ Authorization
  - Admin bisa akses admin features
  - Cashier tidak bisa akses admin features
  - Unauthenticated user redirect ke login

✅ CRUD Operations
  - Create: Valid & invalid data
  - Read: List & single item
  - Update: Valid & invalid data
  - Delete: Success & constraints

✅ Validation
  - Required fields
  - Format validation (email, numeric)
  - Unique constraints
  - Foreign key constraints

✅ Business Logic
  - Order items increment sold_quantity
  - Discount calculation correct
  - Payment method recorded
  - Cart session management
```

---

### 2. Common Testing Patterns

```php
// Pattern 1: Feature - Happy Path
public function user_can_perform_action() {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/action', [valid data]);
    $response->assertRedirect();
}

// Pattern 2: Feature - Validation Error
public function action_fails_with_invalid_data() {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/action', [invalid data]);
    $response->assertSessionHasErrors('field');
}

// Pattern 3: Feature - Authorization
public function unauthenticated_user_cannot_access() {
    $response = $this->post('/protected-action', []);
    $response->assertRedirect('/login');
}

// Pattern 4: Unit - Model Relationship
public function model_relationship_works() {
    $parent = Parent::factory()->create();
    $child = Child::factory()->create(['parent_id' => $parent->id]);
    $this->assertTrue($child->parent->is($parent));
}
```

---

### 3. Debugging Tests

```bash
# Run single test
php artisan test tests/Feature/Auth/LoginTest.php::LoginTest::user_can_login_with_valid_credentials

# Run with output
php artisan test --verbose

# Run specific test method
php artisan test tests/Feature/Auth/LoginTest.php -p user_can_login

# Stop on first failure
php artisan test --stop-on-failure
```

---

### 4. Coverage Report

```bash
# Generate code coverage
php artisan test --coverage

# With detailed report
php artisan test --coverage --coverage-html coverage/
```

---

## 📋 Testing Template

Gunakan template ini saat menulis test:

```php
<?php

namespace Tests\Feature\FeatureName;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeatureNameTest extends TestCase
{
    use RefreshDatabase;

    // Setup data yang digunakan di semua test
    private $user;
    private $data;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->data = [
            'name' => 'Test Data',
            'description' => 'Test description',
        ];
    }

    /** @test */
    public function happy_path_scenario()
    {
        // Arrange
        // ...

        // Act
        $response = $this->actingAs($this->user)
            ->post('/route', $this->data);

        // Assert
        $response->assertRedirect('/expected-route');
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('table', $this->data);
    }

    /** @test */
    public function validation_error_scenario()
    {
        // Arrange
        $invalidData = array_merge($this->data, ['name' => '']);

        // Act
        $response = $this->actingAs($this->user)
            ->post('/route', $invalidData);

        // Assert
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function authorization_scenario()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'cashier']);

        // Act
        $response = $this->actingAs($user)
            ->post('/admin-route', $this->data);

        // Assert
        $response->assertStatus(403);  // Forbidden
    }
}
```

---

## 🚀 Running Tests

```bash
# Run all tests
php artisan test

# Run tests di folder tertentu
php artisan test tests/Feature/

# Run single test class
php artisan test tests/Feature/Auth/LoginTest.php

# Run dengan filter
php artisan test --filter=LoginTest

# Run dengan parallel (faster)
php artisan test --parallel

# Run with specific testbench
php artisan test --configuration=phpunit.xml
```

---

**Selesai! Testing documentation lengkap. 🧪✅**


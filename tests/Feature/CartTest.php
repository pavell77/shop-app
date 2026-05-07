<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['id' => 1, 'name' => 'admin']);
        Role::create(['id' => 2, 'name' => 'user']);
    }

    #[Test]
    public function a_user_can_add_product_to_cart_session()
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role_id' => 2]);
        $product = Product::factory()->createOne(['name' => 'Тестовий товар']);

        $response = $this->actingAs($user)
            ->post(route('cart.add', $product));

        $response->assertStatus(302);
        $this->assertEquals(1, session("cart.{$product->id}.quantity"));
    }

    #[Test]
    public function adding_same_product_twice_increments_quantity()
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role_id' => 2]);
        $product = Product::factory()->createOne();

        $this->actingAs($user);

        $this->post(route('cart.add', $product));
        $this->post(route('cart.add', $product));

        $this->assertEquals(2, session("cart.{$product->id}.quantity"));
    }

    #[Test]
    public function user_can_remove_item_from_cart_session()
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role_id' => 2]);
        $product = Product::factory()->createOne();

        $this->actingAs($user)->withSession([
            'cart' => [
                $product->id => ['name' => 'Test', 'price' => 10, 'quantity' => 1]
            ]
        ]);

        $response = $this->post(route('cart.remove', $product->id));

        $response->assertStatus(302);
        $this->assertFalse(session()->has("cart.{$product->id}"));
    }

    #[Test]
    public function checkout_redirects_guest_to_login()
    {
        $response = $this->post(route('cart.checkout'));
        $response->assertRedirect(route('login'));
    }
}
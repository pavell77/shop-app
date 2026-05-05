<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use App\Services\WayForPayService;
use App\Mail\OrderPaidMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PaymentCallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Створюємо необхідні ролі для SQLite :memory:
        Role::create(['id' => 1, 'name' => 'admin']);
        Role::create(['id' => 2, 'name' => 'user']);
    }

    #[Test]
    public function it_updates_order_status_to_paid_on_successful_callback()
    {
        Mail::fake();

        // 1. Створюємо замовлення (переконайся, що в Order моделі є HasFactory)
        $user = User::factory()->create(['role_id' => 2]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => 1000
        ]);

        // 2. Імітуємо (Mock) WayForPayService
        $this->mock(WayForPayService::class, function ($mock) use ($order) {
            $mock->shouldReceive('isSignatureValid')->once()->andReturn(true);
            $mock->shouldReceive('getSuccessResponse')->once()->andReturn(['status' => 'accept']);
        });

        // 3. Відправляємо фейковий запит від WayForPay
        $response = $this->postJson(route('payment.callback'), [
            'orderReference' => $order->id . '_123456789',
            'transactionStatus' => 'Approved',
            'amount' => 1000,
            'currency' => 'UAH'
        ]);

        // 4. Перевірки
        $response->assertStatus(200);
        $response->assertJson(['status' => 'accept']);
        
        // Перевіряємо, що статус у базі оновився
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid'
        ]);

        // Перевіряємо, що лист було відправлено
        Mail::assertQueued(OrderPaidMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    #[Test]
    public function it_rejects_callback_with_invalid_signature()
    {
        // 1. Створюємо замовлення
        $user = User::factory()->create(['role_id' => 2]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // 2. Імітуємо невалідний підпис
        $this->mock(WayForPayService::class, function ($mock) {
            $mock->shouldReceive('isSignatureValid')->once()->andReturn(false);
        });

        // 3. Відправляємо запит
        $response = $this->postJson(route('payment.callback'), [
            'orderReference' => $order->id . '_123456789',
            'transactionStatus' => 'Approved'
        ]);

        // 4. Перевірки
        $response->assertJson(['status' => 'declined']);
        
        // Статус замовлення НЕ повинен змінитись
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending'
        ]);
    }
}
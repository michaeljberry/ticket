<?php

namespace Tests\Feature;

use App\Concert;
use Tests\TestCase;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_customer_can_purchase_concert_tickets()
    {

        $paymentGateway = new FakePaymentGateway;

        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250
        ]);
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $this->json('POST', "/concerts/{$concert->id}/orders", [
        'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken()
        ]);

        $this->assertEquals(9750, $paymentGateway->totalCharges());

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);

        $this->assertEquals(3, $order->tickets()->count());

    }
}

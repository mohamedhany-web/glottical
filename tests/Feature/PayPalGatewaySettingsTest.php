<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\PayPalService;
use App\Services\PayPalSettings;
use Illuminate\Support\Facades\Http;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class PayPalGatewaySettingsTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
    }

    public function test_paypal_settings_save_enable_and_credentials(): void
    {
        PayPalSettings::save([
            'enabled' => true,
            'mode' => 'sandbox',
            'client_id' => 'test-client-id',
            'client_secret' => 'test-secret-value',
            'webhook_id' => 'WH-123',
            'currency' => 'USD',
        ]);

        $this->assertTrue(PayPalSettings::isEnabled());
        $this->assertTrue(PayPalSettings::isConfigured());
        $this->assertTrue(PayPalSettings::isReady());
        $this->assertSame('sandbox', PayPalSettings::mode());
        $this->assertSame('test-client-id', PayPalSettings::clientId());
        $this->assertSame('test-secret-value', PayPalSettings::clientSecret());
        $this->assertSame('WH-123', PayPalSettings::webhookId());
        $this->assertSame('USD', PayPalSettings::currency());
        $this->assertNotSame('test-secret-value', Setting::getValue(PayPalSettings::SECRET_KEY));
    }

    public function test_paypal_create_order_posts_to_sandbox_api(): void
    {
        PayPalSettings::save([
            'enabled' => true,
            'mode' => 'sandbox',
            'client_id' => 'client',
            'client_secret' => 'secret',
            'currency' => 'USD',
        ]);

        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'tok_test',
                'expires_in' => 300,
            ], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id' => 'PAYPAL-ORDER-1',
                'status' => 'CREATED',
                'links' => [
                    ['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL-ORDER-1'],
                ],
            ], 201),
        ]);

        $created = app(PayPalService::class)->createOrder(
            25.5,
            'USD',
            'https://glottical.test/checkout/paypal/return',
            'https://glottical.test/checkout/paypal/cancel',
            '99',
            'Test course'
        );

        $this->assertSame('PAYPAL-ORDER-1', $created['id']);
        $this->assertStringContainsString('checkoutnow', $created['approve_url']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api-m.sandbox.paypal.com/v2/checkout/orders'
                && ($request['purchase_units'][0]['custom_id'] ?? null) === '99'
                && ($request['purchase_units'][0]['amount']['value'] ?? null) === '25.50';
        });
    }
}

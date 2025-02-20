<?php

declare(strict_types=1);

namespace Tests\Unit;

use AbetaIO\Laravel\Facades\ReturnCart;
use AbetaIO\Laravel\Models\Product;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReturnCartTest extends TestCase
{
    use RefreshDatabase;

    public string $return_url;

    /**
     * Test setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();

        $this->return_url = $this->faker->url();

        $this->user = config('abeta.customerModel')::factory()->create([
            'email' => $this->username = $this->faker->email(),
            'password' => $this->password = $this->faker->password(12, 16),
        ]);
    }

    /**
     * Test if a user can add products and submit them back to Abeta
     */
    public function test_user_can_add_products_and_submit_back_to_abeta(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'abeta_punchout.user_id' => $this->user->id,
                'abeta_punchout.return_url' => $this->return_url,
            ]);

        $cart = ReturnCart::builder()->setGeneral([
            'cart_id' => '12345',
            'total' => 150.00,
            'currency' => 'EUR',
            'delivery_datetime' => '2024-11-10 20:29',
        ]);

        $product = new Product(
            id: 1,
            sku: 'PROD-001',
            name: 'Product Name',
            description: 'Product description here',
            price_ex_vat: 50.00,
            price_inc_vat: 60.00,
            vat_percentage: 20.00,
            quantity: 2,
            price_unit: 30,
            unit: 'pcs',
            brand: 'Brand Name',
            weight: 1.5,
            manufacturer_number: 'MANUF-001',
            category_codes: ['47101501'],
            image_url: 'http://example.com/image.jpg',
            categories: ['Category 1', 'Category 2'],
        );

        // Add product to cart
        $cart->addProduct($product);

        // Create the faker
        Http::fake([
            $this->return_url => Http::response(['success' => true], 200),
        ]);

        // Submit the cart
        ReturnCart::execute();

        // Validate if correct information was send
        Http::assertSent(function ($request) {
            return $request->url() === $this->return_url &&
                   $request->method() === 'POST';
            //    &&
            //    $request->data() === [
            //        'name' => 'John Doe',
            //        'email' => 'john@example.com',
            //    ]
        });

    }

    /**
     * Test if the user can be send back to the Abeta One Time URL
     */
    public function test_if_the_user_can_be_send_back_to_abeta_one_time_url(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'abeta_punchout.user_id' => $this->user->id,
                'abeta_punchout.return_url' => $this->return_url,
            ]);

        $return = ReturnCart::returnCustomer();

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $return);
        $this->assertEquals($this->return_url, $return->getTargetUrl());
    }

    /**
     * Test if the is_abeta_punchout_user() helper works as expected
     */
    public function test_is_abeta_punchout_user_helper(): void
    {
        // Test for normal user
        $this->actingAs($this->user)->get('/');
        $this->assertEquals(is_abeta_punchout_user(), false);

        // Test for PunchOut User
        $this->withSession([
            'abeta_punchout.user_id' => $this->user->id,
            'abeta_punchout.return_url' => $this->return_url,
        ])->get('/');
        $this->assertEquals(is_abeta_punchout_user(), true);

        // Test for reset PunchOut user
        session()->forget('abeta_punchout');
        $this->assertEquals(is_abeta_punchout_user(), false);
    }
}

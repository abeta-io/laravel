<?php

declare(strict_types=1);

namespace Tests\Unit;

use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PunchOutTest extends TestCase
{
    use RefreshDatabase;

    private string $api_key;

    private string $auth;

    private string $username;

    private string $password;

    private $faker;

    private $user;

    /**
     * Test setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();

        $this->api_key = env('ABETA_API_KEY') ?? Str::random(64);
        $this->username = $this->faker->email();
        $this->password = $this->faker->password(12, 16);

        $customerModel = config('abeta.customerModel');

        $this->user = $customerModel::factory()->create([
            'email' => $this->username,
            'password' => $this->password,
        ]);
    }

    /**
     * Test if a user can login, assuming correct credentials and a correct API key.
     */
    public function test_user_can_login_via_setup_request_with_correct_credentials(): void
    {
        $return_url = $this->faker->url();

        $response = $this->post(route('abeta.setupRequest'), [
            'username' => $this->username,
            'password' => $this->password,
            'api_key' => $this->api_key,
            'return_url' => $return_url,
        ]);

        $response->assertStatus(200);

        $one_time_url = $response->json('one_time_url');

        $this->assertIsString($one_time_url);

        $expected_redirect_url = config('abeta.routes.redirectTo');

        $response = $this->get($one_time_url)
            ->assertSessionHas('abeta_punchout.user_id', $this->user->id)
            ->assertSessionHas('abeta_punchout.return_url', $return_url);

        $response->assertRedirect(url($expected_redirect_url))->assertStatus(302);
    }

    /**
     * Test to make sure a user cannot login with incorrect password
     */
    public function test_user_cannot_login_via_setup_request_with_incorrect_password(): void
    {
        $response = $this->post(route('abeta.setupRequest'), [
            'username' => $this->username,
            'password' => 'salted'.$this->password,
            'api_key' => $this->api_key,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Credentials seem to be invalid',
            ]);
    }

    /**
     * Test to make sure a user cannot login with incorrect username
     */
    public function test_user_cannot_login_via_setup_request_with_incorrect_username(): void
    {
        $response = $this->post(route('abeta.setupRequest'), [
            'username' => 'salted'.$this->username,
            'password' => $this->password,
            'api_key' => $this->api_key,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Credentials seem to be invalid',
            ]);
    }

    /**
     * Test to make sure a user cannot login with an incorrect API key
     */
    public function test_user_cannot_login_via_setup_request_with_incorrect_api_key(): void
    {
        $response = $this->post(route('abeta.setupRequest'), [
            'username' => $this->username,
            'password' => $this->password,
            'api_key' => 'salted'.$this->api_key,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Api key is invalid',
            ]);
    }
}

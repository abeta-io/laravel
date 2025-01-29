<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use AbetaIO\Laravel\AbetaPunchOut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;


class PunchOutTest extends TestCase
{
    use RefreshDatabase;

    private string $api_key;
    private string $auth;
    private string $username;
    private string $password;
    private $user;

    /**
     * Test setup
     *
     * @return void
     */

    protected function setUp(): void
    {
        parent::setUp();
    
        $this->api_key = env('ABETA_API_KEY') ?? 'FallbackApiKey123!';
        $this->username = 'test123@example.com';
        $this->password = 'example@test123.com';

        $customerModel = config('abeta.customerModel');

        $this->user = $customerModel::factory()->create([
            'email' => $this->username,
            'password' => $this->password
        ]);
    }

    /**
     * Test if a user can login, assuming correct credentials and a correct API key.
     *
     * @return void
     */
    public function test_user_can_login_via_setup_request_with_correct_credentials() : void
    {   
        $response = $this->post(route('abeta.setupRequest'), [
            'username' => $this->username,
            'password' => $this->password,
            'api_key' => $this->api_key,
        ]);

        $response->assertStatus(200);

        $one_time_url = $response->json('one_time_url'); 

        $this->assertIsString($one_time_url);
        
        $expected_redirect_url = config('abeta.routes.redirectTo');

        $response = $this->get($one_time_url);
        
        $response->assertRedirect(url($expected_redirect_url))->assertStatus(302);
    }

     /**
     * Test to make sure a user cannot login with incorrect password
     *
     * @return void
     */
    public function test_user_cannot_login_via_setup_request_with_incorrect_password() : void
    {   
        $response = $this->post(route('abeta.setupRequest'), [
            'username' => $this->username,
            'password' => 'salted' . $this->password,
            'api_key' => $this->api_key,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Credentials seem to be invalid'
            ]);
    }

     /**
     * Test to make sure a user cannot login with incorrect username
     *
     * @return void
     */
    public function test_user_cannot_login_via_setup_request_with_incorrect_username() : void
    {   
        $response = $this->post(route('abeta.setupRequest'), [
            'username' => 'salted' . $this->username,
            'password' => $this->password,
            'api_key' => $this->api_key,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Credentials seem to be invalid'
            ]);
    }

    /**
     * Test to make sure a user cannot login with an incorrect API key
     *
     * @return void
     */
    public function test_user_cannot_login_via_setup_request_with_incorrect_api_key() : void
    {   
        $response = $this->post(route('abeta.setupRequest'), [
            'username' => $this->username,
            'password' => $this->password,
            'api_key' => 'salted' . $this->api_key,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Api key is invalid'
            ]);
    }
}
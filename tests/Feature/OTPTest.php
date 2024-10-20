<?php
use Tests\TestCase;
use Illuminate\Support\Facades\Http; // To mock external HTTP calls
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class OTPTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function otp_is_generated_successfully()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        // Mock external OTP service provider
        Http::fake([
            'otp-provider.com/api/send' => Http::response(['status' => 'success', 'otp' => '123456'], 200),
        ]);

        $response = $this->postJson('/api/send-otp', ['email' => 'john@example.com']);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'OTP sent successfully']);
    }

    /** @test */
    public function otp_verification_works_correctly()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        // Store OTP in the database or a cache (Redis, etc.)
        $this->postJson('/api/send-otp', ['email' => 'john@example.com']);

        // Mock OTP verification endpoint
        $response = $this->postJson('/api/verify-otp', [
            'email' => 'john@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'OTP verified successfully']);
    }
}

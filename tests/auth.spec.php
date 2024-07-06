<?php

namespace Tests;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterUserSuccessfully()
    {
        $response = $this->postJson('/api/auth/register', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'accessToken',
                'user' => [
                    'userId',
                    'firstName',
                    'lastName',
                    'email',
                ]
            ]
        ]);
    }

    public function testLoginUserSuccessfully()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'accessToken',
                'user' => [
                    'userId',
                    'firstName',
                    'lastName',
                    'email',
                ]
            ]
        ]);
    }

    public function testRegisterFailsWithMissingFields()
    {
        $response = $this->postJson('/api/auth/register', [
            'firstName' => 'John',
            // 'lastName' => 'Doe', // missing
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                [
                    'field',
                    'message',
                ]
            ]
        ]);
    }

    public function testRegisterFailsWithDuplicateEmail()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'status',
            'message'
        ]);
    }
}

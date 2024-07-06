<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenTest extends TestCase
{
    public function testTokenGenerationAndExpiry()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $token = Auth::attempt(['email' => $user->email, 'password' => 'password']);

        $decodedToken = JWTAuth::setToken($token)->getPayload();

        $expiryTime = $decodedToken['exp'];
        $this->assertNotNull($expiryTime);
    }


    public function testCorrectUserDetailsInToken()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);


        $token = Auth::attempt(['email' => $user->email, 'password' => 'password']);

        $decodedToken = JWTAuth::setToken($token)->getPayload();

        $this->assertEquals($user->userId, $decodedToken['sub']);
    }

}

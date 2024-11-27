<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;
    public function login(LoginRequest $request) {
        $request->validated();

        if (!Auth::attempt($request->only('email','password'))) {
            return $this->error([],'Validation Failed Fuck Off!',401);
        }

        $user = User::firstWhere('email',request('email'));

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken('Api Token for '. $user->email,
            ['*'],
            now()->addMinutes(5))->plainTextToken
            ]
        );
    }

    public function register() {

        return $this->ok('Register',[]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('Goodbye');
    }
}
<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiController
{
    public function register(RegisterRequest $request){
        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password)
        ]);

        return $this->success(['token' => $user->createToken("user token")->plainTextToken]);
    }

    public function login(Request $request){
        if(!Auth::attempt($request->only(['name','password']))){
            return $this->failure("Неправильный логин или пароль", 401, ApiException::UNAUTHORIZED);
        }

        $user = User::where('name', $request->name)->first();

        return $this->success(['token' => $user->createToken("user token")->plainTextToken]);
    }

    public function users(Request $request){

        $user = User::all();

        return $this->success(['users' => json_encode($user)]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success("Logged out successfuly");
    }
}

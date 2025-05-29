<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\RegisterRequest;
use App\Models\Chat;
use App\Models\ChatRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        return $this->success(['requests' => json_encode($user)]);
    }

    public function requests(Request $request){

        $user = ChatRequest::all();

        return $this->success(['requests' => json_encode($user)]);
    }

    public function chats(Request $request){

        $user = Chat::all();

        return $this->success(['chats' => json_encode($user)]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        Log::info("currentAcessToken: " . json_encode($token));
        $token->delete();

        return $this->success("Logged out successfuly");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => '請輸入暱稱',
            'email.required' => '請輸入電子郵件',
            'email.email' => '電子郵件格式不正確',
            'email.unique' => '這個電子郵件已經註冊過了',
            'password.required' => '請輸入密碼',
            'password.min' => '密碼至少需要 8 個字元',
            'password.confirmed' => '兩次輸入的密碼不一致',
        ]);

        $user = User::create($data);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('web')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => '請輸入電子郵件',
            'email.email' => '電子郵件格式不正確',
            'password.required' => '請輸入密碼',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => '電子郵件或密碼錯誤',
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('web')->plainTextToken,
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => '已登出']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // 📌 Registration

    public function register(Request $request)
    {
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:100',
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // ✅ Create user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // ✅ Create JWT token
            $token = JWTAuth::fromUser($user);

            // ✅ Return response
            return response()->json([
                'status'  => true,
                'message' => 'Registration successful',
                'token'   => $token,
                'user'    => $user
            ], 201);
        } catch (\Exception $e) {
            // 🔥 Catch any unexpected errors
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // 📌 Login
    public function login(Request $request){
        // dd("login");
        $credentials = $request->only('email','password');

        if(!$token = auth()->attempt($credentials)){
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => auth()->user()
        ]);
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'status'=> true,
            'message'=> 'Logout Successfully!'
        ]);        
    }
}

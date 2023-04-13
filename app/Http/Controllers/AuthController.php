<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) { 
        $request->validate([
            "firstname" => "string|min:2|max:50|required",
            "lastname" => "string|max:20|required",
            "email" => "required|email|unique:users,email",
            "password" => Password::min(8)
                                        ->mixedCase()
                                        ->numbers()
                                        ->symbols()
                                        ->required()
        ]);
        User::create([
           "firstname" => $request->input("firstname"),
           "lastname" => $request->input("lastname"),
           "email" => $request->input("email"),
           "password" => bcrypt($request->input("password"))
        ]);

       return response()->json([
          "message" => "successfully registered"  
       ],201);
    }
     
    public function me() {
        return response()->json(auth()->user());
    }

    public function login(Request $request) {
        $credintials = $request->only(["email", "password"]);

        if(!$token = auth()->attempt($credintials)) {
            return response()->json([
                "error" => "Unauthorized"
            ], 401);
        }

        return $this->respondWithToken($token);
    }
    
    public function logout() {
        auth()->logout();
        return response()->json([
            "message" => "Logout Successfully"
        ]);
    }

    private function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 86400
        ]);
    }
}

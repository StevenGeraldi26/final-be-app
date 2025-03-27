<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    //
    public function register(Request $request)
    {
        $input =  $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);
        // create user
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
        ]);
        $token = $user->createToken('Miku Miku Beam')->plainTextToken;
        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => "User successfully created",
            'data' => $user,
            'token' => $token,
            'type' => 'Bearer'
        ];
        return response()->json($data, Response::HTTP_CREATED);
    }
    public function login(Request $request)
    {
        $input =  $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $input['email'])->first();
        if (!$user || !Hash::check($input['password'], $user->password)) {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => "Invalid credentials",
            ], Response::HTTP_UNAUTHORIZED);
        }
        $token = $user->createToken('Miku Miku Beam')->plainTextToken;
        $data = [
            'status' => Response::HTTP_OK,
            'message' => "User successfully logged in",
            'data' => $user,
            'token' => $token,
            'type' => 'Bearer'
        ];
        return response()->json($data, Response::HTTP_OK);
    }
    public function getDetails()
    {
        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Detail User',
            'data' => auth()->user(),
        ];
        return response()->json($data, Response::HTTP_OK);
    }
    public function logout()
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });
        $data = [
            "status" => Response::HTTP_OK,
            "message" => "Logged Out",
        ];
        return response()->json($data, Response::HTTP_OK);
    }
}

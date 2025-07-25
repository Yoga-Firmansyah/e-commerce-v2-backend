<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $customer = Customer::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ]);

        $token = JWTAuth::fromUser($customer);

        if ($customer) {
            return response()->json([
                'success' => true,
                'user'    => $customer,
                'token'   => $token
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ], 401);
        }
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api')->user(),
            'token'   => $token
        ], 201);
    }

    public function getUser()
    {
        return response()->json([
            'success' => true,
            'user'    => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], 200);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('api')->refresh());
        // return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60
        ]);
    }


    public function logout()
    {
        $logout = auth()->logout();
        if ($logout) {
            return response()->json([
                'success' => true,
                'message'    => "Successfully logged out",
            ], 200);
        }
    }


    public function checkTokenExpired(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $token = $request->input('token');
            $payload = JWTAuth::setToken($token)->getPayload();
            $exp = $payload->get('exp');
            $remainingTime = $exp - now()->timestamp;

            return response()->json([
                'Token Remaining Time' => $remainingTime,
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token Expired'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to verify token'], 400);
        }
    }
}

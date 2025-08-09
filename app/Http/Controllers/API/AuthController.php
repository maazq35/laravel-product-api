<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use JWTAuth;

class AuthController extends Controller {
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if($validator->fails()) return response()->json($validator->errors(), 422);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'), 201);
    }

    public function login(Request $request){
        $credentials = $request->only('email','password');
        if(!$token = auth('api')->attempt($credentials)){
            return response()->json(['error'=>'Invalid credentials'], 401);
        }
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function me(){ return response()->json(auth('api')->user()); }

    public function logout(){ auth('api')->logout(); return response()->json(['message'=>'Logged out']); }
}

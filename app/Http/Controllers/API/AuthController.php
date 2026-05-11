<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:100',

                'email' =>
                    'required|email|unique:users,email',

                'password' =>
                    'required|min:6',
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,

            'email' => $request->email,

            'password' => Hash::make(
                $request->password
            ),
        ]);

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([
            'status' => true,

            'message' =>
                'Register berhasil',

            'token' => $token,

            'user' => $user,
        ], 201);
    }

  
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',

                'password' => 'required',
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            !$user ||
            !Hash::check(
                $request->password,
                $user->password
            )
        ) {

            return response()->json([
                'status' => false,

                'message' =>
                    'Email atau password salah',
            ], 401);
        }

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([
            'status' => true,

            'message' =>
                'Login berhasil',

            'token' => $token,

            'user' => $user,
        ]);
    }


    public function user(Request $request)
    {
        return response()->json(
            $request->user()
        );
    }


    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'status' => true,

            'message' =>
                'Logout berhasil',
        ]);
    }
}

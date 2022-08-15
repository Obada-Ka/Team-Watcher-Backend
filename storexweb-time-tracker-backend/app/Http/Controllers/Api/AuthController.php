<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Api\ApiController;
use Validator;
use App\Models\Role;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if( $validator->fails()){
            $result = $validator->errors();

            $message = "failed";
            return $this->errorResponse($result,$message, 404);


        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token = $user->createToken('authtoken');

        $dev_role = Role::where('slug','user')->first();


        $user->roles()->attach($dev_role);

        $result = ['token' => $token->plainTextToken, 'user' => $user];
        $message = 'User Registered';


        // return response()->json(
        //     [
        //         'message'=>'User Registered',
        //         'data'=> ['token' => $token->plainTextToken, 'user' => $user]
        //     ]
        // );
        return $this->successResponse($result, $message);

    }


    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $token = $request->user()->createToken('authtoken');
        $user = $request->user();
        $message = "Logged in";
        $role = $user->roles()->pluck('name');
        $result = ['token' => $token->plainTextToken, 'user' => $user, 'role' => $role];

        return $this->successResponse($result, $message);
    }

    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();
        $message = "Logged out";
        $result = null;

        return $this->successResponse($result, $message);

        // return response()->json(
        //     [
        //         'message' => 'Logged out'
        //     ]
        // );

    }

}

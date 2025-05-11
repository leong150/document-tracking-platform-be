<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->only('email', 'password'), [
            'email' => 'required|email',
        ]); 

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()],400);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && $user->status === 'SUSPENDED') {
            return response()->json(['message'=> 'Your account has been suspended'],status: 403);
        }

        if ($user &&
            (
                Hash::check($request->password, $user->password) ||
                (empty($user->password) && empty($request->password))
            )
        ) {
            if ($user instanceof User) {
                // Hinting here for $user will be specific to the User object
                $token = $user->generateApiToken();
            }

            return response()->json([
                'message' => 'Success',
                'data' => [
                    'token'=> $token,
                    'code'=> $user->code,
                    'name'=> $user->name,
                    'contact_no'=> $user->contact_no,
                    'type'=> $user->type,
                    'address'=> $user->address,
                ], 
            ], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', $token)->first();
        $user->api_token = null;
        $user->save();

        return response()->json(['message'=> 'OK'],200);
    }

    public function updatePassword(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', $token)->first();

        if (empty($user->password) && 
            !empty($request->new_password) &&
            strlen($request->new_password) >= 6
        ) {
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json(['message'=> 'Password saved'],200);
        }

        if (!empty($user->password) &&
            Hash::check($request->original_password, $user->password) &&
            !empty($request->new_password) &&
            strlen($request->new_password) >= 6
        ) {
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json(['message'=> 'Password updated'],200);
        }

        return response()->json(['message'=> 'Invalid request'],400);
    }

    public function resetPassword(Request $request)
    {
        // $token = $request->bearerToken();
        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return response()->json(['message' => 'Username not found'], 400);
        }

        $user->password = null;
        $user->save();

        return response()->json(['message'=> 'Your password has been reset', 'data'=> (object)([])], 200);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function createUpdateUser(Request $request)
    {
        if (empty($request->code)) {
            if ($request->user()->type !== 'STAFF') {
                return response()->json(['message' => 'Forbidden'],403);
            }

            if (
                empty($request->name) ||
                empty($request->email) ||
                empty($request->contact_no) ||
                !filter_var($request->email, FILTER_VALIDATE_EMAIL) ||
                User::where("email", $request->email)->exists() ||
                // empty($request->password) ||
                // strlen($request->password) < 6 ||
                !($request->type === "STAFF" || $request->type === "DISPATCH")
                )
            {
                return response()->json(['message' => 'Invalid entries'],400);
            }

            User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'contact_no'=> $request->contact_no,
                'code'=> uniqid($request->type[0]),
                'type'=> $request->type,
                'status'=> 'ACTIVE',
                'address'=> $request->type === 'STAFF'
                            ? 'TRX Tower - Gallery Lobby | Menara 106 Exchange, Imbi, Kuala Lumpur, Federal Territory of Kuala Lumpur, Malaysia'
                            : null,
                ]);

            return response()->json(['message'=> 'User has been created'],200);
        }

        if (User::where('code', $request->code)->exists() && 
            ($request->status === 'ACTIVE' || $request->status === 'SUSPENDED')
        ) {
            $user = User::where('code', $request->code)->first();
            $user->status = $request->status;
            $user->save();

            return response()->json(['message'=> 'User has been updated'],200);
        }

        return response()->json(['message'=> 'Invalid request'],400);
    }

    public function getUsers()
    {
        $users = User::all();

        return response()->json(
            [
                'message'=> 'OK',
                'data'=> [
                    'list'=> collect($users)->map(function ($user) {
                        return [
                            'code'=> $user->code,
                            'name'=> $user->name,
                            'email'=> $user->email,
                            'contact_no'=> $user->contact_no,
                            'type'=> $user->type,
                            'status'=> $user->status,
                        ];
                })],
            ],200);
    }

    public function getUser(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', $token)->first();

        if ($user) {
            return response()->json(
                [
                    'message'=> 'OK',
                    'data'=> [
                        'code'=> $user->code,
                        'name'=> $user->name,
                        'email'=> $user->email,
                        'contact_no'=> $user->contact_no,
                        'type'=> $user->type,
                        'status'=> $user->status,
                        'address'=> $user->address,
                        'has_password'=> !empty($user->password),
                    ],
                ],200);
        }

        return response()->json(['message'=> 'Invalid code'],400);
    }
}
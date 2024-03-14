<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        if (!$users) {
            return response()->json()->setStatusCode(204);
        }
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $userAlready = UserService::alreadyEmailOrDocument($request->get('email'),$request->get('document'));
            if($userAlready) {
                return response()->json([
                    'message' => 'User already'
                ], 409);
            }
            return response()->json(User::create($request->all()), 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::where('id', $id)->get()->first();
            if(!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }
            return $user;
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, int $id)
    {
        try {
            $user = User::where('id', $id)->get()->first();
            if(!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }
            $userAlready = UserService::alreadyEmailOrDocument($request->get('email'),$request->get('document'), $id);
            if($userAlready) {
                return response()->json([
                    'message' => 'Email or Document already'
                ], 409);
            }            
            $user->update($request->all());
            return response()->json($user, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            return User::where('id', $id)->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }
}

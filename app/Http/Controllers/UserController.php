<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserStoreRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $already = User::where('email', $request->get('email'))->orWhere('document', $request->get('document'))->get()->first();
            if(!$already) {
                return User::create($request->all());
            }
            return response()->json([
                'message' => 'User already'
            ], 403);
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
    public function update(Request $request, string $id)
    {
        try {
            $user = User::where('id', $id)->get()->first();
            if(!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }            
            $already = User::where('email', $request->get('email'))->orWhere('document', $request->get('document'))->get()->first();
            if($already && $already->id != $id) {
                return response()->json([
                    'message' => 'Email or Document already'
                ], 403);
            }            
            $user->update($request->all());
            return $user;
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

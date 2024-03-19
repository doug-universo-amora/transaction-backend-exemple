<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/transaction-backend-exemple/public/api/user",
     *      tags={"User"},
     *      summary="Get list user",
     *      description="Returns list user",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="User(s) not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     *     )
     */
    public function index()
    {
        $users = User::all();
        if (!$users) {
            return response()->json([
                'message' => 'User(s) not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return $users;
    }

    /**
     * @OA\Post(
     *      path="/transaction-backend-exemple/public/api/user",
     *      tags={"User"},
     *      summary="Create a user",
     *      description="Create a user",
     *      @OA\RequestBody(
     *      @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="User(s) not found"
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Conflict"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     *     )
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $userAlready = UserService::alreadyEmailOrDocument($request->get('email'), $request->get('document'));
            if ($userAlready) {
                return response()->json([
                    'message' => 'User already'
                ], Response::HTTP_CONFLICT);
            }
            return response()->json(User::create($request->all()), Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *      path="/transaction-backend-exemple/public/api/user/{id}",
     *      tags={"User"},
     *      summary="Get a user",
     *      description="Get a user",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Get a user",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="User(s) not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     *     )
     */
    public function show(string $id)
    {
        try {
            $user = User::where('id', $id)->get()->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return $user;
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *      path="/transaction-backend-exemple/public/api/user/{id}",
     *      tags={"User"},
     *      summary="Update a user",
     *      description="Update a user",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id to update user",
     *         required=true,
     *      ),
     *      @OA\RequestBody(
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="User(s) not found"
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Conflict"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     *     )
     */
    public function update(UserUpdateRequest $request, int $id)
    {
        try {
            $user = User::where('id', $id)->get()->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $userAlready = UserService::alreadyEmailOrDocument($request->get('email'), $request->get('document'), $id);
            if ($userAlready) {
                return response()->json([
                    'message' => 'Email or Document already'
                ], Response::HTTP_CONFLICT);
            }
            $user->update($request->all());
            return response()->json($user, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *      path="/transaction-backend-exemple/public/api/user/{id}",
     *      tags={"User"},
     *      summary="Delete a user",
     *      description="Delete a user",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id to delete user",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     *     )
     */
    public function destroy(string $id)
    {
        try {
            if (!$id) {
                return response()->json([
                    'message' => 'Bad request'
                ], Response::HTTP_NOT_FOUND);
            }
            return User::where('id', $id)->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

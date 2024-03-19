<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Http\Requests\TransactionStoreRequest;

class TransactionController extends Controller
{
     /**
     * @OA\Get(
     *      path="/transaction-backend-exemple/public/api/transaction",
     *      tags={"Transaction"},
     *      summary="Get list of transaction",
     *      description="Returns list of transaction",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Transaction(s) not found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     *     )
     */
    public function index()
    {
        try {
            $transaction = Transaction::all();
            if (!$transaction) {
                return response()->json([
                    'message' => 'Transaction(s) not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return $transaction;
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *      path="/transaction-backend-exemple/public/api/transaction",
     *      tags={"Transaction"},
     *      summary="Create a transaction",
     *      description="Create a transaction",
     *      @OA\RequestBody(
     *      @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Transaction(s) not found"
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
    public function store(TransactionStoreRequest $request)
    {
        try {
            $alreadyPayee = User::where('id', $request->get('payee'))->get()->first();
            if (!$alreadyPayee) {
                return response()->json([
                    'message' => 'Payee not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $alreadyPayer = User::where('id', $request->get('payer'))->get()->first();
            if (!$alreadyPayer) {
                return response()->json([
                    'message' => 'Payer not found'
                ], Response::HTTP_NOT_FOUND);
            }
            if ($alreadyPayer->id == $request->get('payee')) {
                return response()->json([
                    'message' => 'Payer not send transaction yourself'
                ], Response::HTTP_BAD_REQUEST);
            }

            return TransactionService::handle($request->all());
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *      path="/transaction-backend-exemple/public/api/transaction/{id}",
     *      tags={"Transaction"},
     *      summary="Get a transaction",
     *      description="Get a transaction",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Get a transaction",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Transaction(s) not found"
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
            $transaction = Transaction::where('id', $id)->get()->first();
            if (!$transaction) {
                return response()->json([
                    'message' => 'Transaction not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return $transaction;
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *      path="/transaction-backend-exemple/public/api/transaction/{id}",
     *      tags={"Transaction"},
     *      summary="Update a transaction",
     *      description="Update a transaction",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id to update transaction",
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
     *          description="Transaction(s) not found"
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
    public function update(TransactionStoreRequest $request, string $id)
    {
        try {
            $transaction = Transaction::where('id', $id)->get()->first();
            if (!$transaction) {
                return response()->json([
                    'message' => 'Transaction not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return TransactionService::handle($request->all(), $id);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *      path="/transaction-backend-exemple/public/api/transaction/{id}",
     *      tags={"Transaction"},
     *      summary="Delete a transaction",
     *      description="Delete a transaction",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id to delete transaction",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     *     )
     */
    public function destroy(string $id)
    {
        try {
            return Transaction::where('id', $id)->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Http\Requests\TransactionStoreRequest;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaction = Transaction::all();
        return $transaction;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreRequest $request)
    {
        try {
            $alreadyPayee = User::where('id', $request->get('payee'))->get()->first();
            if(!$alreadyPayee) {
                return response()->json([
                    'message' => 'Payee not found'
                ], 404);
            }

            $alreadyPayer = User::where('id', $request->get('payer'))->get()->first();
            if(!$alreadyPayer) {
                return response()->json([
                    'message' => 'Payer not found'
                ], 404);
            }
            if($alreadyPayer->id == $request->get('payee')) {
                return response()->json([
                    'message' => 'Payer not send transaction yourself'
                ], 400);
            }

            return TransactionService::handle($request->all());
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
            $transaction = Transaction::where('id', $id)->get()->first();
            if(!$transaction) {
                return response()->json([
                    'message' => 'Transaction not found'
                ], 404);
            }
            return $transaction;
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionStoreRequest $request, string $id)
    {
        try {
            $transaction = Transaction::where('id', $id)->get()->first();
            if(!$transaction) {
                return response()->json([
                    'message' => 'Transaction not found'
                ], 404);
            }
            return TransactionService::handle($request->all(), $id);
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
            return Transaction::where('id', $id)->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }
}

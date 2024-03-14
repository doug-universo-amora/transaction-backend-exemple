<?php

namespace App\Services;

use App\Models\User;
use App\Services\UserService;
use App\Services\NotifyService;
use App\Models\Transaction;

class TransactionService {

    static public function handle(array $arrData, int $id = null)
    {
        try {
            $userPayer = User::where('id', $arrData['payer'])->get()->first();
            if(!UserService::makeTransaction($userPayer)) {
                return response()->json([
                    'message' => 'Payer can not transaction'
                ], 400);
            }

            if ($userPayer->balance < $arrData['value']) {
                return response()->json([
                    'message' => 'Payer not sufficient balance'
                ], 400);
            }

            $userPayee = User::where('id', $arrData['payee'])->get()->first();
            
            \DB::beginTransaction();

            $transaction = self::execute($userPayer, $userPayee, $arrData, $id);
            
            if (!self::authorization()) {
                \DB::rollBack();
                return response()->json([
                    'message' => 'Not authorized transaction'
                ], 403);
            }

            \DB::commit();
            
            NotifyService::send();

            return response()->json($transaction, 201);

        } catch (\Throwable $th) {
            throw $th;
        }

    }

    static private function execute($userPayer, $userPayee, $arrData, int $id = null)
    {  
        try {
            if (is_null($id)) {
                $transaction = Transaction::create($arrData);
            } else {
                $transaction = Transaction::where('id', $id)->get()->first();
                $currentUserPayer = User::where('id', $transaction->payer)->get()->first();
                $currentUserPayee = User::where('id', $transaction->payee)->get()->first();
                $currentUserPayer->balance += $transaction->value;
                $currentUserPayee->balance -= $transaction->value;
            }
            $userPayer->balance -= $arrData['value'];
            $userPayee->balance += $arrData['value'];
    
            $userPayer->save();
            $userPayee->save();
            return $transaction;
        } catch (\Throwable $th) {
            throw $th;
        }      
    }

    static private function authorization()
    {
        try {
            $json = file_get_contents('https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc');
            $permission = json_decode($json);
            return $permission->message == 'Autorizado' ? true : false;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
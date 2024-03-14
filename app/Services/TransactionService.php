<?php

namespace App\Services;

use App\Models\User;
use App\Services\UserService;
use App\Models\Transaction;

class TransactionService {

    static public function handle(array $arrData)
    {
        $userPayer = User::where('id', $arrData['payer'])->get()->first();
        if(!in_array($userPayer->type, UserService::MAKETRANSACTION)) {
            return response()->json([
                'message' => 'Payer can not transaction'
            ], 403);
        }

        if ($userPayer->balance < $arrData['value']) {
            return response()->json([
                'message' => 'Payer not sufficient balance'
            ], 403);
        }

        $userPayee = User::where('id', $arrData['payee'])->get()->first();
        
        \DB::beginTransaction();
        $transaction = self::execute($userPayer, $userPayee, $arrData);
        
        if (!self::authorization()) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Not authorized transaction'
            ], 403);
        }

        \DB::commit();

        return $transaction;
        
    }

    static private function execute($userPayer, $userPayee, $arrData)
    {        
        $transaction = Transaction::create($arrData);
        $userPayer->balance -= $arrData['value'];
        $userPayee->balance += $arrData['value'];
        $userPayer->save();
        $userPayee->save();
        return $transaction;
    }

    static private function authorization()
    {
        $json = file_get_contents('https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc');
        $permission = json_decode($json);
        return $permission->message == 'Autorizado' ? true : false;
    }

}
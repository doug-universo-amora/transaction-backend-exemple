<?php

namespace App\Services;

use App\Models\User;

class UserService {

    public const MAKETRANSACTION = [
        1,
    ];

    /**
     * Verify email or document user exists. Option ignored $id param if not null
     *
     * @param App\Models\User
     */
    static public function alreadyEmailOrDocument(string $email, int $document, int $id = null)
    {
        try {
            $where = User::where('email', $email)->orWhere('document', $document);
            if (!is_null($id)) {
                $userAlready = $where->where('id', '<>',$id)->get()->first();
                if (!$userAlready) {
                    return false;
                }
                return $userAlready->id != $id ? true : false;
            }

            $userAlready = $where->get()->first();
            return $userAlready ? true : false;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
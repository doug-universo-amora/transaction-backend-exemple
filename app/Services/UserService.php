<?php

namespace App\Services;

use App\Models\User;

class UserService
{

    /**
     * Verify user can make transaction
     *
     * @param App\Models\User
     */
    public static function makeTransaction(User $user)
    {
        return $user->type == 1 ? true : false;
    }

    /**
     * Verify email or document user exists. Option ignored $id param if not null
     * 
     */
    public static function alreadyEmailOrDocument(string $email, int $document, int $id = null)
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

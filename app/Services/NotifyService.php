<?php

namespace App\Services;

class NotifyService
{
    public static function send()
    {
        try {
            if (self::authorization()) {
                //send notify
                return true;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return false;
    }

    private static function authorization()
    {
        try {
            $json = file_get_contents('https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc');
            $permission = json_decode($json);
            return $permission->message;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

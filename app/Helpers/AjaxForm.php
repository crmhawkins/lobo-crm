<?php

namespace App\Helpers;

class AjaxForm
{
    public static function custom($data)
    {
        return response()->json($data);
    }

    public static function errorMessage($message)
    {
        return response()->json([
            'error' => true,
            'message' => $message
        ]);
    }
}

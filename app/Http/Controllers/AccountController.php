<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;


class AccountController extends Controller
{
    public const CustomHeaders = [
        'Content-type' => "application/json",
        'Authorization' => 'Bearer 6|ZthSahx1pyD43uc3ulyCvxvdl5FEL2gvp3w0s1Heabedd750',
        'X-TIMESTAMP' => '2025-03-27',
        'X-SIGNATURE' => 'Maybank2025',
        'ORIGIN' => 'www.maybank.com',
        'X-PARTNER-ID' => '123456',
        'X-EXTERNAL-ID' => '78910',
        'CHANNEL-ID' => '95221'
    ];
    public function getAccountInfo()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Account Info found',
            'data' => ['balance' => 1200000, 'account number' => '12312345'],
        ], Response::HTTP_OK);
    }
}

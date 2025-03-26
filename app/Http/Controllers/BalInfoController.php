<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BalInfoController extends Controller
{
    public function index()
    {
        // Get data from env
        $clientId = env('CLIENT_ID');
        $clientSecret = env('CLIENT_SECRET');
        $publicKey = env('PUBLIC_KEY');
        $privateKey = env('PRIVATE_KEY');

        // Generate timestamp
        $timestamp = $this->getTimestamp();

        // Step 1: Get Signature Auth
        $signatureAuth = $this->getSignatureAuth($timestamp, $clientId, $privateKey);

        // Step 2: Get Access Token
        $accessToken = $this->getAccessToken($timestamp, $clientId, $signatureAuth);

        // Step 3: Get Signature Service
        $signatureService = $this->getSignatureService($timestamp, $clientSecret, $accessToken);

        // Step 4: Perform Balance Inquiry
        $balanceInfo = $this->getBalanceInfo($timestamp, $clientId, $accessToken, $signatureService);

        return response()->json($balanceInfo);
    }

    private function getTimestamp()
    {
        return (new \DateTime('now', new \DateTimeZone('+07:00')))->format('Y-m-d\TH:i:sP');
    }

    private function getSignatureAuth($timestamp, $clientId, $privateKey)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-TIMESTAMP' => $timestamp,
            'X-CLIENT-KEY' => $clientId,
            'Private_Key' => $privateKey,
        ];

        $response = Http::withHeaders($headers)->post(
            'https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/utilities/signature-auth'
        );

        return $this->handleResponse($response, 'Failed to get signature auth')['signature'];
    }

    private function getAccessToken($timestamp, $clientId, $signatureAuth)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-TIMESTAMP' => $timestamp,
            'X-CLIENT-KEY' => $clientId,
            'X-SIGNATURE' => $signatureAuth,
        ];

        $response = Http::withHeaders($headers)->post(
            'https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/access-token/b2b',
            [
                'grantType' => 'client_credentials',
                'additionalInfo' => new \stdClass(),
            ]
        );

        return $this->handleResponse($response, 'Failed to get access token')['accessToken'];
    }

    private function getSignatureService($timestamp, $clientSecret, $accessToken)
    {
        $headers = [
            'accept' => 'application/json',
            'X-TIMESTAMP' => $timestamp,
            'X-CLIENT-SECRET' => $clientSecret,
            'HttpMethod' => 'POST',
            'EndpoinUrl' => '/api/v1.0/balance-inquiry',
            'AccessToken' => $accessToken,
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->post(
            'https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/utilities/signature-service',
            [
                'partnerReferenceNo' => '2020102900000000000001',
                'bankCardToken' => '6d7963617264746f6b656e',
                'accountNo' => '2000100101',
                'balanceTypes' => ['Cash', 'Coins'],
                'additionalInfo' => [
                    'deviceId' => '12345679237',
                    'channel' => 'mobilephone',
                ],
            ]
        );

        return $this->handleResponse($response, 'Failed to get signature service')['signature'];
    }

    private function getBalanceInfo($timestamp, $clientId, $accessToken, $signatureService)
    {
        $headers = [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signatureService,
            'X-PARTNER-ID' => $clientId,
            'X-EXTERNAL-ID' => '41807553358950093184162180797837',
            'CHANNEL-ID' => '95221',
        ];

        $response = Http::withHeaders($headers)->post(
            'https://apidevportal.aspi-indonesia.or.id:44310/api/v1.0/balance-inquiry',
            [
                'partnerReferenceNo' => '2020102900000000000001',
                'bankCardToken' => '6d7963617264746f6b656e',
                'accountNo' => '2000100101',
                'balanceTypes' => ['Cash', 'Coins'],
                'additionalInfo' => [
                    'deviceId' => '12345679237',
                    'channel' => 'mobilephone',
                ],
            ]
        );

        return $this->handleResponse($response, 'Failed to get balance info');
    }

    private function handleResponse($response, $errorMessage)
    {
        if ($response->status() != 200) {
            return response()->json([
                'error' => $errorMessage,
                'details' => $response->json(),
            ], $response->status());
        }

        return $response->json();
    }
}

<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Google\Auth\Credentials\ServiceAccountCredentials;

trait HasFcmToken
{
    protected string $firebaseFilePath;
    protected string $firebaseString;
    protected array $firebaseArray;

    private function init()
    {
        $this->firebaseFilePath = storage_path('app/firebase.json');
        $this->firebaseString = file_get_contents($this->firebaseFilePath);
        $this->firebaseArray = json_decode($this->firebaseString, true);
    }
    
    public function getGoogleAccessToken()
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $expiration = $now + 3600; // 1 hour expiration
        $payload = json_encode([
            'iss' => $this->firebaseArray['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $expiration,
            'iat' => $now
        ]);

        // Encode to base64
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create the signature
        $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
        openssl_sign($signatureInput, $signature, $this->firebaseArray['private_key'], 'sha256');
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Create the JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        // Exchange JWT for an access token

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]));

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true)['access_token'];
    }
    public function sendFirebaseMessage($token, $title, $body)
    {
        $this->init() ;
        
        //cache for 50 min 
        $accessToken = cache()->remember('google-oauth2-access-token',  60 * 50, function () {
            return $this->getGoogleAccessToken();
        });

        // Define the notification payload
        $notification = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ]
            ],
        ];

        // Send the push notification
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/' . $this->firebaseArray['project_id'] . '/messages:send'); // Replace with your Firebase project ID
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ;
    }
}

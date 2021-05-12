<?php

declare(strict_types=1);

use Firebase\JWT\JWT;

require_once __DIR__.'/../../vendor/autoload.php';

class AuthException extends Exception
{
    public $message;
    public $code;
    public $data;

    public function __construct($message, $code, $data)
    {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }
}

class JWTHelper
{
    private $jwtSecretKey = 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=';

    public function generateTokenForUser($user)
    {
        $secretKey = $this->jwtSecretKey;
        $tokenId = base64_encode(random_bytes(16));
        $issuedAt = new DateTimeImmutable();
        $expire = $issuedAt->modify('+1 week')->getTimestamp();      // customize the expiry time
        $serverName = 'your.domain.name';                            // customize domain name (optional)

        // Create the token as an array
        $data = [
            'iat' => $issuedAt->getTimestamp(),    // Issued at: time when the token was generated
            'jti' => $tokenId,                     // Json Token Id: an unique identifier for the token
            'iss' => $serverName,                  // Issuer
            'nbf' => $issuedAt->getTimestamp(),    // Not before
            'exp' => $expire,                      // Expire
            'data' => [                             // Data related to the signer user
                'userName' => $user->name,          // User name
                'userId' => $user->id,              // User id -> these two data will be used to later
            ]
        ];

        // Encode the array to a JWT string.
        $token = JWT::encode(
            $data,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );

        return ['token' => $token, 'user' => $user];
    }

    public function validate()
    {
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (!$authorizationHeader || !preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            throw new AuthException('Unauthenticated! Please login.', 401, ['info' => 'Bearer header missing.']);
        }

        $jwt = $matches[1];
        if (!$jwt) {
            throw new AuthException('Unauthenticated! Please login.', 401, ['info' => 'Bearer Token missing.']);
        }

        $secretKey = $this->jwtSecretKey;
        try {
            $payload = JWT::decode($jwt, $secretKey, ['HS512']);
        } catch (Exception $e) {
            throw new AuthException('Unauthenticated! Please login.', 401, ['info' => $e->getMessage()]);
        }
        $now = new DateTimeImmutable();
        $serverName = 'your.domain.name';

        if ($payload->iss !== $serverName ||
            $payload->nbf > $now->getTimestamp() ||
            $payload->exp < $now->getTimestamp()) {
            throw new AuthException('Unauthorized! Please login.', 401, ['info' => 'Invalid Bearer Token provided.']);
        }

        return $payload->data;
    }
}

$jwtHelper = new JWTHelper();

// Show the page

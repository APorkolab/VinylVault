<?php

class JWTEncoder
{
    public function __construct(private string $secretKey)
    {
    }

    public function encode(array $payload): string
    {
        $header = json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]);
        $header = $this->base64UrlEncode($header);

        $payload = json_encode($payload);
        $payload = $this->base64UrlEncode($payload);

        $signature = hash_hmac("sha256", $header . "." . $payload, $this->secretKey, true);
        $signature = $this->base64UrlEncode($signature);

        return $header . "." . $payload . "." . $signature;
    }

    public function decode(string $token): array
    {
        $parts = explode(".", $token);
        if (count($parts) !== 3) {
            throw new InvalidArgumentException("Invalid token format");
        }

        list($header, $payload, $signature) = $parts;

        $signatureFromToken = $this->base64UrlDecode($signature);
        $data = $header . "." . $payload;
        $expectedSignature = hash_hmac("sha256", $data, $this->secretKey, true);

        if (!hash_equals($expectedSignature, $signatureFromToken)) {
            throw new InvalidSignatureException;
        }

        $payload = json_decode($this->base64UrlDecode($payload), true);

        if (isset($payload["exp"]) && $payload["exp"] < time()) {
            throw new TokenExpiredException;
        }

        return $payload;
    }

    private function base64UrlEncode(string $text): string
    {
        return rtrim(strtr(base64_encode($text), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $text): string
    {
        return base64_decode(strtr($text, '-_', '+/'));
    }
}
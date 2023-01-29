<?php

declare(strict_types=1);

$accessPayload = [
    'sub' => $user["id"],
    'name' => $user["name"],
    'exp' => time() + 3600 // expires in 1 hour
];

$accessToken = $codec->encode($accessPayload);

$refreshTokenExpiry = time() + 432000;

$refreshPayload = [
    'sub' => $user["id"],
    'exp' => $refreshTokenExpiry
];

$refreshToken = $codec->encode($refreshPayload);

echo json_encode([
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken
]);

?>
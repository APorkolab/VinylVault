<?php

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['token'])) {
    http_response_code(400);
    echo json_encode(["message" => "missing token"]);
    exit;
}

$codec = new JWTEncoder($_ENV["SECRET_KEY"]);

try {
    $payload = $codec->decode($data["token"]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["message" => "invalid token"]);
    exit;
}

$user_id = $payload["sub"];

$database = new Database(
    $_ENV["DB_HOST"],
    $_ENV["DB_NAME"],
    $_ENV["DB_USER"],
    $_ENV["DB_PASS"]
);

$refreshTokenModel = new RefreshTokenModel($database, $_ENV["SECRET_KEY"]);

$refresh_token = $refreshTokenModel->getByToken($data["token"]);

if ($refresh_token === false) {
    http_response_code(400);
    echo json_encode(["message" => "The token is not valid"]);
    exit;
}

$userModel = new UserModel($database);

$user = $userModel->getByIdentifier('user_id', $user_id);

if ($user === false) {
    http_response_code(401);
    echo json_encode(["message" => "The user does not exist"]);
    exit;
}

require __DIR__ . "/tokens.php";

$refresh_token_gateway->delete($data["token"]);
$refresh_token_gateway->create($user_id, $refresh_token_expiry);

?>
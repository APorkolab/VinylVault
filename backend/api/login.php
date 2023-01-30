<?php

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);
    header('HTTP/1.1 200 OK');
    header("Allow: POST");
    exit;
}

// if ($_SERVER["REQUEST_METHOD"] === "OPTIONS" || $_SERVER["REQUEST_METHOD"] === "POST" ) {
//     header("Access-Control-Allow-Origin: *");
//     header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, 
//     Authorization, HTTP-STATUS, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Methods, Access-Control-Allow-Credentials, Access-Control-Max-Age, Access-Control-Expose-Headers');
//     header('HTTP/1.1 200 OK');
//     die();
// }
// $data = (array) json_decode(file_get_contents("php://input"), true);
// $data = json_decode(file_get_contents("php://input"), true);
$data = (array) json_decode(file_get_contents("php://input"), true);



if (
    !array_key_exists("username", $data) ||
    !array_key_exists("password", $data)
) {

    http_response_code(400);
    echo json_encode(["message" => "missing login credentials"]);
    exit;
}

$database = new Database(
    $_ENV["DB_HOST"],
    $_ENV["DB_NAME"],
    $_ENV["DB_USER"],
    $_ENV["DB_PASS"]
);

$user_gateway = new UserModel($database);

$user = $user_gateway->getByIdentifier($data["username"], 'username');

if ($user === false) {

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

if (!password_verify($data["password"], $user["password_hash"])) {

    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}

$access_token_expiry = time() + (60 * 60); // expires in 1 hour
$refresh_token_expiry = time() + (30 * 24 * 60 * 60); // expires in 30 days

$codec = new JWTEncoder($_ENV["SECRET_KEY"]);

require __DIR__ . "/tokens.php";

$access_token = $codec->encode(["sub" => $user["id"], "name" => $user["username"], "exp" => $access_token_expiry]);
$refresh_token = $codec->encode(["sub" => $user["id"], "exp" => $refresh_token_expiry]);

$refreshTokenModel = new RefreshTokenModel($database, $_ENV["SECRET_KEY"]);

$refreshTokenModel->create($refresh_token, $refresh_token_expiry, $user["id"]);


// echo json_encode([
//     "access_token" => $access_token,
//     "refresh_token" => $refresh_token

// ]);

exit;

?>
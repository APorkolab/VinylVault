<?php
declare(strict_types=1);
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS" && $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"] === "POST" || $_SERVER["REQUEST_URI"] === '/api/login.php' || $_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: include");
    header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization, HTTP-STATUS, Access-Control-Allow-Headers, Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Origin');
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header('HTTP/1.1 200 OK');
    die();
}

require __DIR__ . "/api/bootstrap.php";

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", $path);

$resource = $parts[2];
$id = $parts[3] ?? null;

$database = new Database(
    $_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"],
    $_ENV["DB_PASS"]
);

$codec = new JWTEncoder($_ENV["SECRET_KEY"]);
$auth = new Auth(new UserModel($database), $codec);


if (!$auth->authenticateAccessToken()) {
    exit;
}

$user_id = $auth->getUserID();
$controller = new ProductController(new ProductModel($database), $user_id);
$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
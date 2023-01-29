<?php

declare(strict_types=1);

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
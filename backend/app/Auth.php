<?php

class Auth
{
    private int $user_id;

    private userModel $userModel;
    private JWTEncoder $codec;

    public function __construct(userModel $userModel, JWTEncoder $codec)
    {
	$this->userModel = $userModel;
        $this->codec = $codec;
    }

    public function authenticateAPIKey(): bool
    {
        if (!isset($_SERVER["HTTP_X_API_KEY"])) {

            http_response_code(400);
            echo json_encode(["message" => "API key is missing"]);
            return false;
        }

        $api_key = $_SERVER["HTTP_X_API_KEY"];

	$user = $this->userModel->getByIdentifier($api_key, 'api_key') ?: false;

        if ($user === false) {

            http_response_code(401);
            echo json_encode(["message" => "API key is invalid"]);
            return false;
        }

        $this->user_id = $user["id"];

        return true;
    }

    public function getUserID(): int
    {
        return $this->user_id;
    }

    public function authenticateAccessToken(): bool
    {
        if (!preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"] ?? "", $matches)) {
            http_response_code(400);
            echo json_encode(["message" => "HTTP Authorization header is missing"]);
            return false;
        }

        try {
            $data = $this->codec->decode($matches[1]);

        } catch (InvalidSignatureException $e) {

            http_response_code(401);
            echo json_encode(["message" => "Token signature is invalid"]);
            return false;

        } catch (TokenExpiredException $e) {

            http_response_code(401);
            echo json_encode(["message" => "Token has been expired. Please refresh your token!"]);
            return false;

        } catch (Exception $e) {

            http_response_code(400);
            echo json_encode(["message" => $e->getMessage()]);
            return false;
        }

        $this->user_id = $data["sub"];

        return true;
    }
}
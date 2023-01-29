<?php

header("Access-Control-Allow-Origin: *");
class ProductController
{
    private ProductModel $model;
    private int $user_id;

    public function __construct(ProductModel $model, int $user_id)
    {
        $this->model = $model;
        $this->user_id = $user_id;
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id === null) {
            if ($method === "GET") {
                echo json_encode($this->model->getAllForUser($this->user_id));
            } elseif ($method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $errors = $this->validate($data);

                if (!empty($errors)) {
                    $this->respondUnprocessableEntity($errors);
                    return;
                }

                $id = $this->model->createForUser($this->user_id, $data);
                $this->respondCreated($id);
            } else {
                $this->respondMethodNotAllowed("GET, POST");
            }
        } else {
            $product = $this->model->getForUser($this->user_id, $id);

            if ($product === false) {
                $this->respondNotFound($id);
                return;
            }

            switch ($method) {
                case "GET":
                    echo json_encode($product);
                    break;
                case "PATCH":
                    $data = json_decode(file_get_contents("php://input"), true);
                    $errors = $this->validate($data, false);

                    if (!empty($errors)) {
                        $this->respondUnprocessableEntity($errors);
                        return;
                    }

                    $this->model->updateForUser($this->user_id, $id, $data);
                    $this->respondSuccess("Product updated");
                    break;
                case "DELETE":
                    $this->model->deleteForUser($this->user_id, $id);
                    $this->respondSuccess("Product deleted");
                    break;
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
    }

    private function validate(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }

        if (!empty($data["priority"]) && !is_int($data["priority"])) {
            $errors[] = "priority must be an integer";
        }

        return $errors;
    }

    private function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    private function respondSuccess(string $message, array $data = []): void
    {
        http_response_code(200);
        echo json_encode(["message" => $message, "data" => $data]);
    }


    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function respondNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(["message" => "Product with ID $id not found"]);
    }

    private function respondCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Product created", "id" => $id]);
    }

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new && empty($data["name"])) {
            $errors[] = "Name is required";
        }

        if (empty($data["description"])) {
            $errors[] = "Description is required";
        }

        if (empty($data["price"])) {
            $errors[] = "Price is required";
        }

        if (!empty($data["price"]) && !is_numeric($data["price"])) {
            $errors[] = "Price must be a number";
        }

        if (!empty($data["is_available"]) && !in_array($data["is_available"], [0, 1])) {
            $errors[] = "Is_available must be 0 or 1, because it's a boolean.";
        }

        return $errors;
    }
}
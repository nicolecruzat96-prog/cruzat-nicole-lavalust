<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('api');
        $this->call->model('UserModel');
        $this->call->model('ProductModel');
    }

    public function login()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit('login:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 10, 60);

        $body = $this->request_body();
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $this->api->respond_error('Valid email and password are required.', 422);
        }

        $user = $this->UserModel->find_by_email($email);
        $passwordMatches = $user && (
            password_verify($password, $user['password']) ||
            hash_equals((string) $user['password'], $password)
        );

        if (!$passwordMatches) {
            $this->api->respond_error('Invalid email or password.', 401);
        }

        $tokens = $this->api->issue_tokens([
            'id' => (int) $user['id'],
            'role' => $user['role'],
            'scopes' => ['products:read', 'products:write'],
        ]);

        $this->api->respond([
            'user' => [
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
            'tokens' => $tokens,
        ]);
    }

    public function logout()
    {
        $this->api->require_method('POST');
        $this->require_authentication();

        $body = $this->request_body();
        if (!empty($body['refresh_token'])) {
            $this->api->revoke_refresh_token((string) $body['refresh_token']);
        }

        $this->api->respond(['message' => 'Logged out.']);
    }

    public function products()
    {
        $this->require_authentication();
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        if ($method === 'GET') {
            $this->api->respond(['products' => $this->ProductModel->read() ?: []]);
        }

        if ($method === 'POST') {
            $product = $this->validated_product($this->request_body());
            $this->ProductModel->create(
                $product['product_name'],
                $product['description'],
                $product['price'],
                $product['quantity']
            );
            $this->api->respond(['message' => 'Product created.'], 201);
        }

        $this->api->respond_error('Method Not Allowed', 405);
    }

    public function product($id)
    {
        $this->require_authentication();

        if (!ctype_digit((string) $id) || (int) $id < 1) {
            $this->api->respond_error('Invalid product id.', 400);
        }

        $id = (int) $id;
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $existing = $this->ProductModel->find($id);

        if (!$existing) {
            $this->api->respond_error('Product not found.', 404);
        }

        if ($method === 'GET') {
            $this->api->respond(['product' => $existing]);
        }

        if ($method === 'DELETE') {
            $this->ProductModel->delete($id);
            $this->api->respond(['message' => 'Product deleted.']);
        }

        if ($method === 'PUT' || $method === 'PATCH') {
            $body = $this->request_body();
            if ($method === 'PATCH') {
                $body = array_merge($existing, $body);
            }
            $product = $this->validated_product($body);
            $this->ProductModel->update(
                $id,
                $product['product_name'],
                $product['description'],
                $product['price'],
                $product['quantity']
            );
            $this->api->respond(['product' => $this->ProductModel->find($id)]);
        }

        $this->api->respond_error('Method Not Allowed', 405);
    }

    private function require_authentication()
    {
        $this->api->require_jwt();
    }

    private function request_body()
    {
        $body = json_decode(file_get_contents('php://input'), true);
        if (is_array($body)) {
            return $body;
        }

        return $this->api->body();
    }

    private function validated_product($body)
    {
        $name = trim((string) ($body['product_name'] ?? ''));
        $description = trim((string) ($body['description'] ?? ''));
        $price = (string) ($body['price'] ?? '');
        $quantity = (string) ($body['quantity'] ?? '');
        $nameLength = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);

        if ($name === '' || $nameLength > 100) {
            $this->api->respond_error('Product name is required and must be at most 100 characters.', 422);
        }

        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $price)) {
            $this->api->respond_error('Price must be a non-negative number with up to two decimal places.', 422);
        }

        if (!preg_match('/^\d+$/', $quantity)) {
            $this->api->respond_error('Quantity must be a non-negative whole number.', 422);
        }

        return [
            'product_name' => $name,
            'description' => $description,
            'price' => $price,
            'quantity' => (int) $quantity,
        ];
    }
}
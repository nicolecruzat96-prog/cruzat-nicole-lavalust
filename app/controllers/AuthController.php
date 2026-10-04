<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model('UserModel');
    }

    public function login()
    {
        $data['error'] = '';

        if ($this->form_validation->submitted()) {
            $email = trim($this->io->post('email'));
            $password = $this->io->post('password');
            $role = $this->io->post('role');
            $user = $this->UserModel->find_by_email($email);

            $passwordMatches = $user && (
                $user['password'] === $password ||
                password_verify($password, $user['password'])
            );

            if ($user && $passwordMatches && $user['role'] === $role) {
                $this->session->regenerate_on_login();
                $this->session->set_userdata([
                    'user_id' => $user['id'],
                    'user_email' => $user['email'],
                    'user_role' => $user['role']
                ]);
                header('Location: ' . site_url('/products'));
                exit;
            }

            $data['error'] = 'Invalid email, password, or role.';
        }

        $this->call->view('auth/login', $data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        header('Location: ' . site_url('/login'));
        exit;
    }

}
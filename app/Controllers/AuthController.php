<?php

class AuthController {
    private $service;

    public function __construct(AuthService $service) { 
        $this->service = $service;
    }

    public function loginForm() { 
        if (isset($_SESSION['user_id'])) {
            redirect('/dashboard');
        }
        view('auth.login');
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $pass  = $_POST['password'] ?? '';

        if ($this->service->login($email, $pass)) {
            redirect('/dashboard');
        }
        $_SESSION['error'] = "Invalid login";
        redirect('/');
    }

    public function logout() {
        $this->service->logout();
        redirect('/');
    }
    public function forgotForm()
    { 
        view('auth.forgot_password');
    }

    public function sendResetLink()
    {
        $email = $_POST['email'] ?? '';
        $this->service->sendPasswordReset($email);
        redirectWithFlash(
            'success',
            'If the email exists, a reset link has been sent.',
            BASE_PATH . '/forgot-password'
        );
    }

    public function resetForm()
    {
        view('auth.reset_password');
    }

    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$this->service->resetPassword($token, $password)) {
            $error = "Invalid or expired link";
            view('auth.reset_password', ['error' => $error]);
            return;
        }

        redirect('/');
    }
}

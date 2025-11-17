<?php
declare(strict_types=1);

namespace MySportsApp\Controllers;

use PDO;

class AuthController
{
    public function showLogin(): void
    {
        render('pages/login');
    }

    public function loginSubmit(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = db()->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
            render('pages/login', ['error' => $error, 'email' => $email]);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        header('Location: /index.php?route=dashboard');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /index.php?route=login');
        exit;
    }
}

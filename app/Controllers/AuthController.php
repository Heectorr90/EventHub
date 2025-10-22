<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';


class AuthController
{
    public static function login()
    {
        global $pdo;
        $userModel = new User($pdo);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validaciones básicas
            if (empty($email) || empty($password)) {
                $error = "Por favor, completa todos los campos.";
            } else {
                $user = $userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    // Redirigir usando la función helper
                    header("Location: " . url('dashboard'));
                    exit;
                } else {
                    $error = "Correo o contraseña incorrectos.";
                }
            }
        }

        // Mostrar vista de login
        include __DIR__ . '/../../views/auth/login.php';
    }

    public static function register()
    {
        global $pdo;
        $userModel = new User($pdo);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');

            // Validaciones
            if (empty($name) || empty($email) || empty($password)) {
                $error = "Por favor, completa todos los campos.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El correo electrónico no es válido.";
            } elseif (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
            } elseif ($password !== $confirm_password) {
                $error = "Las contraseñas no coinciden.";
            } elseif ($userModel->findByEmail($email)) {
                $error = "Ya existe una cuenta con ese correo.";
            } else {
                // Crear usuario
                if ($userModel->create($name, $email, $password)) {
                    $_SESSION['register_success'] = "Cuenta creada exitosamente. ¡Inicia sesión!";
                    header("Location: " . url('login'));
                    exit;
                } else {
                    $error = "Error al crear la cuenta. Intenta nuevamente.";
                }
            }
        }

        // Mostrar vista de registro
        include __DIR__ . '/../../views/auth/register.php';
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }

        header("Location: " . url('login'));
        exit;
    }

    public static function dashboard()
    {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . url('login'));
            exit;
        }

        // Cargar el dashboard
        include __DIR__ . '/../../views/dashboard.php';
    }
}
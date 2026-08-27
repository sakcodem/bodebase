<?php

class Auth {
    /**
     * Inicia la sesión de forma segura si no se ha iniciado antes.
     */
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            
            session_start();
        }
    }

    /**
     * Intenta iniciar sesión verificando el correo y la contraseña.
     */
    public static function login(PDO $pdo, string $email, string $password): bool {
        self::initSession();
        
        try {
            $sql = "SELECT id, nombre, email, password FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => trim($email)]);
            $usuario = $stmt->fetch();

            // Verificar si el usuario existe y si la contraseña coincide con el hash guardado
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Guardar datos esenciales en la sesión
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nombre'];
                $_SESSION['last_activity'] = time();
                
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            error_log("Error en Auth::login -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * Middleware para proteger rutas. Redirige al login si no estás autenticado.
     */
    public static function protegerRuta(): void {
        self::initSession();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Destruye la sesión por completo (Cerrar sesión).
     */
    public static function logout(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
<?php
// public/login.php
require_once '../config/conexion.php';
require_once '../src/Models/Auth.php';

Auth::initSession();

// Si ya tiene sesión iniciada, lo mandamos directo al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = null;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);

    if ($email && $password) {
        if (Auth::login($pdo, $email, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Credenciales incorrectas. Inténtalo de nuevo.";
        }
    } else {
        $error = "Por favor, introduce un correo y contraseña válidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Bodebase</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm bg-white p-6 md:p-8 rounded-2xl shadow-xl space-y-6">
        
        <div class="text-center space-y-2">
            <span class="text-4xl"></span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bodebase</h1>
            <p class="text-xs text-slate-400 font-medium">Ingresa tus credenciales para administrar tu negocio</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-100 text-red-600 text-xs font-semibold p-3 rounded-xl text-center">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Correo Electrónico</label>
                <input type="email" name="email" required placeholder="tu@correo.com"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Contraseña</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
            </div>

            <button type="submit" 
                    class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl shadow-md cursor-pointer transition text-center block text-sm">
                Ingresar
            </button>
        </form>
    </div>

</body>
</html>
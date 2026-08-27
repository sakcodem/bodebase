<?php
require_once '../config/conexion.php';

$nombre = "MIAÜ";
$email = "admin@gmail.com"; // Pon tu correo real aquí
$password_plana = "miau130902"; // Pon tu contraseña real aquí

// Encriptado profesional e inquebrantable
$password_encriptada = password_hash($password_plana, PASSWORD_BCRYPT);

try {
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $email, $password_encriptada]);
    echo "¡Usuario administrador creado con éxito! BORRA ESTE ARCHIVO INMEDIATAMENTE de tu servidor.";
} catch (Exception $e) {
    echo "Error o el usuario ya existe: " . $e->getMessage();
}
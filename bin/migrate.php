<?php
// 1. Requerir tu archivo de conexión existente
require_once __DIR__ . '/../config/conexion.php'; 

try {
    // Crear la tabla de migraciones si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Obtener migraciones ya aplicadas
    $applied = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

    // Ruta a tu carpeta de migraciones 
    $migrationsDir = __DIR__ . '/../migrations';
    
    if (!is_dir($migrationsDir)) {
        echo "La carpeta de migraciones no existe: $migrationsDir\n";
        exit(1);
    }

    $files = glob($migrationsDir . '/*.sql');
    sort($files); // Ordenar alfabéticamente/cronológicamente los archivos

    $executedCount = 0;

    foreach ($files as $file) {
        $fileName = basename($file);

        // Si la migración aún no ha sido aplicada
        if (!in_array($fileName, $applied)) {
            $sql = file_get_contents($file);
            
            // Ejecutar la consulta SQL
            $pdo->exec($sql);
            
            // Registrar el archivo en la tabla migrations
            $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$fileName]);

            echo "✅ Migración ejecutada: $fileName\n";
            $executedCount++;
        }
    }

    if ($executedCount === 0) {
        echo "ℹ️ No hay migraciones pendientes por ejecutar.\n";
    } else {
        echo "🚀 Se aplicaron $executedCount migración(es) correctamente.\n";
    }

} catch (PDOException $e) {
    echo "❌ Error en la ejecución de migraciones: " . $e->getMessage() . "\n";
    exit(1);
}
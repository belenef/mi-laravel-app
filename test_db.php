<?php
// Script de prueba para verificar configuración

require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = app();

echo "=== VERIFICANDO CONFIGURACIÓN ===\n\n";

// 1. Verificar conexión a BD
echo "1. Verificando conexión a BD:\n";
try {
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    if ($pdo) {
        echo "   ✓ Conexión a BD OK\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error de conexión: " . $e->getMessage() . "\n";
}

// 2. Verificar tabla users
echo "\n2. Verificando tabla users:\n";
$columns = \Illuminate\Support\Facades\Schema::getColumns('users');
$columnNames = array_map(fn($col) => $col['name'], $columns);
echo "   Columnas: " . implode(', ', $columnNames) . "\n";

if (in_array('avatar', $columnNames)) {
    echo "   ✓ Columna 'avatar' existe\n";
} else {
    echo "   ✗ Columna 'avatar' NO existe\n";
}

// 3. Verificar modelo User
echo "\n3. Verificando modelo User:\n";
$userModel = new \App\Models\User();
echo "   Tabla: " . $userModel->getTable() . "\n";
echo "   Fillable: " . implode(', ', $userModel->getFillable()) . "\n";

if (in_array('avatar', $userModel->getFillable())) {
    echo "   ✓ 'avatar' está en fillable\n";
} else {
    echo "   ✗ 'avatar' NO está en fillable\n";
}

// 4. Prueba de actualización
echo "\n4. Probando actualización de usuario:\n";
try {
    $user = \App\Models\User::first();
    if ($user) {
        echo "   Usuario encontrado: " . $user->name . "\n";
        
        $updated = $user->update(['avatar' => 'test/image.jpg']);
        echo "   Resultado de update(): " . ($updated ? 'true' : 'false') . "\n";
        
        $userReloaded = \App\Models\User::find($user->id);
        echo "   Avatar en BD: " . $userReloaded->avatar . "\n";
        
        // Restaurar
        $user->update(['avatar' => $user->getOriginal('avatar')]);
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEL TEST ===\n";
?>

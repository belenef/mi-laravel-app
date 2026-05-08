<?php
// Script de prueba para verificar si podemos escribir archivos

echo "=== VERIFICANDO SISTEMA DE ALMACENAMIENTO ===\n\n";

$basePath = __DIR__;
$storagePath = $basePath . '/storage/app/public/avatars';

echo "1. Verificando rutas:\n";
echo "   Base: $basePath\n";
echo "   Storage: $storagePath\n\n";

echo "2. Verificando directorios:\n";
echo "   ¿Existe storage? " . (is_dir($basePath . '/storage') ? '✓' : '✗') . "\n";
echo "   ¿Existe storage/app? " . (is_dir($basePath . '/storage/app') ? '✓' : '✗') . "\n";
echo "   ¿Existe storage/app/public? " . (is_dir($basePath . '/storage/app/public') ? '✓' : '✗') . "\n";
echo "   ¿Existe storage/app/public/avatars? " . (is_dir($storagePath) ? '✓' : '✗') . "\n\n";

if (!is_dir($storagePath)) {
    echo "3. Creando directorio avatars...\n";
    if (@mkdir($storagePath, 0777, true)) {
        echo "   ✓ Directorio creado\n\n";
    } else {
        echo "   ✗ No se pudo crear el directorio\n\n";
    }
}

echo "4. Probando escritura de archivo:\n";
$testFile = $storagePath . '/test.txt';
if (@file_put_contents($testFile, 'Test file')) {
    echo "   ✓ Archivo de prueba creado\n";
    @unlink($testFile);
    echo "   ✓ Archivo de prueba eliminado\n";
} else {
    echo "   ✗ No se pudo escribir el archivo de prueba\n";
    echo "   Verifica los permisos de la carpeta\n";
}

echo "\n=== FIN DEL TEST ===\n";
?>

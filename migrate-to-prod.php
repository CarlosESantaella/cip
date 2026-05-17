<?php
/**
 * Script de migración: adapta el SQL dump local para producción.
 *
 * Maneja correctamente datos serializados de PHP (ajusta contadores s:N:"...")
 *
 * Reemplazos:
 *   http://cip-wordpress.test  →  https://cip.org.uy
 *   http://cip.org.uy          →  https://cip.org.uy
 *   Base de datos: cip_wordpress → u449780709_8LoRF
 */

$inputFile  = __DIR__ . '/cip_wordpress.sql';
$outputFile = __DIR__ . '/cip_wordpress_prod.sql';

$replacements = [
    // Orden importante: primero el más largo/específico
    // Variantes JSON-in-SQL escaped (\\/ = JSON-escaped slash inside SQL-escaped string)
    'http:\\\\/\\\\/cip-wordpress.test' => 'https:\\\\/\\\\/cip.org.uy',
    'http:\\\\/\\\\/cip.org.uy'         => 'https:\\\\/\\\\/cip.org.uy',
    // Variantes normales
    'http://cip-wordpress.test'         => 'https://cip.org.uy',
    'http://cip.org.uy'                 => 'https://cip.org.uy',
];

$dbNameOld = 'cip_wordpress';
$dbNameNew = 'u449780709_8LoRF';

// --- Funciones auxiliares ---

/**
 * Busca y reemplaza dentro de datos serializados de PHP,
 * recalculando los contadores de longitud de strings.
 */
function serialized_search_replace(string $data, string $search, string $replace): string
{
    $searchLen  = strlen($search);
    $replaceLen = strlen($replace);
    $diff       = $replaceLen - $searchLen;

    if ($diff === 0) {
        // Misma longitud: reemplazo simple, no rompe serialización
        return str_replace($search, $replace, $data);
    }

    // Patrón para encontrar strings serializados que contienen la cadena a buscar
    // Matches: s:NUMBER:"...search_string...";
    // Soporta tanto \" (escaped en SQL) como " (sin escapar)
    $result = preg_replace_callback(
        '/s:(\d+):(\\\\?")(.*?)(\\2);/s',
        function ($matches) use ($search, $replace, $diff) {
            $originalLen = (int) $matches[1];
            $quote       = $matches[2];
            $content     = $matches[3];

            if (strpos($content, $search) === false) {
                return $matches[0]; // No contiene la cadena, no tocar
            }

            $occurrences = substr_count($content, $search);
            $newContent  = str_replace($search, $replace, $content);
            $newLen      = $originalLen + ($diff * $occurrences);

            return 's:' . $newLen . ':' . $quote . $newContent . $quote . ';';
        },
        $data
    );

    // También reemplazar ocurrencias fuera de datos serializados
    // (URLs en texto plano SQL, valores no serializados, etc.)
    // Las ocurrencias dentro de serializados ya fueron manejadas arriba,
    // pero necesitamos cubrir las que están fuera.
    // Como el callback ya hizo el replace dentro de serializados,
    // solo queda reemplazar lo que quedó fuera.
    // Estrategia: buscar cadenas que NO estén precedidas por el patrón s:N:"
    // Más simple: el preg_replace_callback ya manejó todos los s:N:"..." bloques,
    // ahora hacemos str_replace global para el resto (que no son serializados)
    // Pero ojo: si ya reemplazamos dentro de serializados, str_replace no haría daño
    // porque la cadena vieja ya no existe ahí.
    $result = str_replace($search, $replace, $result);

    return $result;
}

// --- Proceso principal ---

echo "=== Migración SQL: Local → Producción ===\n\n";
echo "Input:  $inputFile\n";
echo "Output: $outputFile\n\n";

if (!file_exists($inputFile)) {
    die("ERROR: No se encontró el archivo $inputFile\n");
}

$sql = file_get_contents($inputFile);
$originalSize = strlen($sql);

echo "Archivo leído: " . number_format($originalSize / 1024, 1) . " KB\n\n";

// 1. Reemplazar nombre de base de datos (solo en sentencias SQL, no en datos)
$sql = str_replace("`$dbNameOld`", "`$dbNameNew`", $sql);
echo "[OK] Base de datos: `$dbNameOld` → `$dbNameNew`\n";

// 2. Aplicar reemplazos de URLs con manejo de serialización
foreach ($replacements as $search => $replace) {
    if ($search === $replace) {
        continue;
    }
    $countBefore = substr_count($sql, $search);
    $sql = serialized_search_replace($sql, $search, $replace);
    $countAfter = substr_count($sql, $search);
    echo "[OK] '$search' → '$replace' ($countBefore ocurrencias reemplazadas)\n";
}

// 3. Verificación: no deben quedar URLs locales
$remaining = substr_count($sql, 'cip-wordpress.test');
if ($remaining > 0) {
    echo "\n⚠ ADVERTENCIA: Quedan $remaining referencias a 'cip-wordpress.test'\n";
} else {
    echo "\n✓ No quedan referencias al dominio local\n";
}

// 4. Verificación: no deben quedar http://cip.org.uy (todo debe ser https)
$httpRemaining = substr_count($sql, 'http://cip.org.uy');
if ($httpRemaining > 0) {
    echo "⚠ ADVERTENCIA: Quedan $httpRemaining referencias a 'http://cip.org.uy' (sin HTTPS)\n";
} else {
    echo "✓ Todas las URLs de cip.org.uy usan HTTPS\n";
}

// 5. Escribir archivo de salida
file_put_contents($outputFile, $sql);
$newSize = strlen($sql);

echo "\nArchivo generado: $outputFile\n";
echo "Tamaño: " . number_format($newSize / 1024, 1) . " KB\n";
echo "\n=== Migración completada ===\n";

<?php
/**
 * WordPress Migration Script: Search & Replace con soporte para datos serializados.
 *
 * Reemplaza https://cip.org.uy -> http://cip-wordpress.test
 * en toda la base de datos, incluyendo datos serializados de PHP.
 *
 * USO: Abrir en el navegador http://cip-wordpress.test/migrate-to-local.php
 * IMPORTANTE: Eliminar este archivo despues de usarlo.
 */

// Configuracion local
$db_host = '127.0.0.1';
$db_name = 'cip_wordpress';
$db_user = 'root';
$db_pass = '';

$search  = 'https://cip.org.uy';
$replace = 'http://cip-wordpress.test';

// --------------------------------------------------

set_time_limit(300);
header('Content-Type: text/html; charset=utf-8');

echo "<h1>WordPress Migration: Search &amp; Replace</h1>";
echo "<p><b>Buscando:</b> <code>$search</code><br><b>Reemplazando por:</b> <code>$replace</code></p>";
echo "<hr>";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("<p style='color:red;'>Error de conexion: " . htmlspecialchars($e->getMessage()) . "</p>");
}

// Obtener todas las tablas
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$total_changes = 0;

foreach ($tables as $table) {
    // Obtener columnas de la tabla
    $cols_stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
    $columns = $cols_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Detectar la primary key
    $primary_keys = [];
    foreach ($columns as $col) {
        if ($col['Key'] === 'PRI') {
            $primary_keys[] = $col['Field'];
        }
    }

    if (empty($primary_keys)) {
        continue; // Saltar tablas sin PK (no podemos hacer UPDATE seguro)
    }

    // Filtrar solo columnas de texto
    $text_columns = [];
    foreach ($columns as $col) {
        if (preg_match('/char|text|blob|varchar|longtext|mediumtext/i', $col['Type'])) {
            $text_columns[] = $col['Field'];
        }
    }

    if (empty($text_columns)) {
        continue;
    }

    // Buscar filas que contengan la URL original
    $where_parts = [];
    foreach ($text_columns as $col) {
        $where_parts[] = "`$col` LIKE " . $pdo->quote("%$search%");
    }
    $where = implode(' OR ', $where_parts);

    $pk_select = implode(', ', array_map(function($k) { return "`$k`"; }, $primary_keys));
    $all_cols = implode(', ', array_map(function($c) { return "`$c`"; }, $text_columns));

    $sql = "SELECT $pk_select, $all_cols FROM `$table` WHERE $where";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $table_changes = 0;

    foreach ($rows as $row) {
        $updates = [];
        foreach ($text_columns as $col) {
            if (empty($row[$col]) || strpos($row[$col], $search) === false) {
                continue;
            }

            $old_value = $row[$col];
            $new_value = recursive_unserialize_replace($search, $replace, $old_value);

            if ($new_value !== $old_value) {
                $updates[$col] = $new_value;
            }
        }

        if (!empty($updates)) {
            $set_parts = [];
            $params = [];
            foreach ($updates as $col => $val) {
                $set_parts[] = "`$col` = ?";
                $params[] = $val;
            }

            $where_pk = [];
            foreach ($primary_keys as $pk) {
                $where_pk[] = "`$pk` = ?";
                $params[] = $row[$pk];
            }

            $update_sql = "UPDATE `$table` SET " . implode(', ', $set_parts) . " WHERE " . implode(' AND ', $where_pk);
            $stmt = $pdo->prepare($update_sql);
            $stmt->execute($params);

            $table_changes += count($updates);
        }
    }

    if ($table_changes > 0) {
        echo "<p><b>$table</b>: $table_changes cambio(s)</p>";
        $total_changes += $table_changes;
    }
}

echo "<hr>";
echo "<h2>Migracion completada: $total_changes cambio(s) total(es)</h2>";
echo "<p style='color:green;'><b>El sitio ahora deberia funcionar en:</b> <a href='$replace'>$replace</a></p>";
echo "<p style='color:red;'><b>IMPORTANTE:</b> Elimina este archivo (migrate-to-local.php) por seguridad.</p>";

// --------------------------------------------------
// Funciones auxiliares
// --------------------------------------------------

/**
 * Reemplaza un string dentro de datos que pueden estar serializados.
 * Maneja recursivamente arrays y objetos serializados de PHP.
 */
function recursive_unserialize_replace($search, $replace, $data, $serialised = false) {
    if (is_string($data)) {
        $unserialized = @unserialize($data);

        if ($unserialized !== false || $data === 'b:0;') {
            // Es un string serializado, procesarlo recursivamente
            $data = serialize(recursive_unserialize_replace($search, $replace, $unserialized, true));
        } else {
            // String normal, hacer reemplazo directo
            $data = str_replace($search, $replace, $data);
        }
    } elseif (is_array($data)) {
        $new_data = [];
        foreach ($data as $key => $value) {
            $new_data[$key] = recursive_unserialize_replace($search, $replace, $value, false);
        }
        $data = $new_data;
    } elseif (is_object($data)) {
        $props = get_object_vars($data);
        foreach ($props as $key => $value) {
            $data->$key = recursive_unserialize_replace($search, $replace, $value, false);
        }
    }

    return $data;
}

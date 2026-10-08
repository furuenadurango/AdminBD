<?php
// ==========================================================================
// Helper para Exportar e Importar la Base de Datos (100% PHP / Fallback CLI)
// ==========================================================================

/**
 * Genera el archivo SQL de respaldo completo con todas las tablas y datos.
 */
function exportar_base_datos($pdo, $output_file) {
    // Intentar primero con mysqldump si está disponible
    $mysqldump_path = 'c:\\xampp\\mysql\\bin\\mysqldump.exe';
    if (!file_exists($mysqldump_path)) {
        $mysqldump_path = 'mysqldump';
    }

    $command = "\"$mysqldump_path\" -u root quehaypahacer > \"$output_file\" 2>&1";
    @exec($command, $output, $return_var);

    if ($return_var === 0 && file_exists($output_file) && filesize($output_file) > 100) {
        return [
            'status' => true,
            'metodo' => 'mysqldump',
            'mensaje' => 'Exportado exitosamente usando mysqldump.'
        ];
    }

    // Fallback nativo 100% PHP usando PDO (no depende de ningún comando del sistema)
    try {
        $tables = [];
        $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sql = "-- ==========================================================\n";
        $sql .= "-- COPIA DE SEGURIDAD AUTOMÁTICA - QUEHAYPAHACER\n";
        $sql .= "-- Generada el: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- ==========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $sql .= "-- ----------------------------------------------------------\n";
            $sql .= "-- Estructura y datos para la tabla `$table`\n";
            $sql .= "-- ----------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";

            $createStmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $createRow = $createStmt->fetch(PDO::FETCH_NUM);
            $sql .= $createRow[1] . ";\n\n";

            $dataStmt = $pdo->query("SELECT * FROM `$table`");
            $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($rows) > 0) {
                $columnNames = array_map(function($col) { return "`$col`"; }, array_keys($rows[0]));
                $columnsStr = implode(", ", $columnNames);

                $sql .= "INSERT INTO `$table` ($columnsStr) VALUES\n";
                $inserts = [];
                foreach ($rows as $r) {
                    $values = [];
                    foreach ($r as $val) {
                        if (is_null($val)) {
                            $values[] = "NULL";
                        } else {
                            $values[] = $pdo->quote($val);
                        }
                    }
                    $inserts[] = "  (" . implode(", ", $values) . ")";
                }
                $sql .= implode(",\n", $inserts) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        if (file_put_contents($output_file, $sql) !== false) {
            return [
                'status' => true,
                'metodo' => 'PDO Nativo PHP',
                'mensaje' => 'Exportado exitosamente usando PDO nativo.'
            ];
        } else {
            return [
                'status' => false,
                'mensaje' => 'No se pudo escribir en el archivo de destino.'
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => false,
            'mensaje' => 'Error al exportar: ' . $e->getMessage()
        ];
    }
}

/**
 * Restaura la base de datos a partir del archivo SQL.
 */
function importar_base_datos($pdo, $input_file) {
    if (!file_exists($input_file)) {
        return [
            'status' => false,
            'mensaje' => 'El archivo de respaldo no existe.'
        ];
    }

    try {
        $sql = file_get_contents($input_file);
        if ($sql === false || empty(trim($sql))) {
            return [
                'status' => false,
                'mensaje' => 'El archivo de respaldo está vacío o no se pudo leer.'
            ];
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        $pdo->exec($sql);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");

        return [
            'status' => true,
            'mensaje' => 'Base de datos restaurada correctamente.'
        ];
    } catch (PDOException $e) {
        return [
            'status' => false,
            'mensaje' => 'Error al importar: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtiene metadatos del archivo de respaldo actual.
 */
function obtener_info_backup($file_path) {
    if (!file_exists($file_path)) {
        return [
            'existe' => false,
            'fecha' => 'Nunca generado',
            'tamano' => '0 KB'
        ];
    }

    $tamano_bytes = filesize($file_path);
    $tamano_kb = round($tamano_bytes / 1024, 2);
    $fecha_mod = date('d/m/Y h:i:s A', filemtime($file_path));

    return [
        'existe' => true,
        'fecha' => $fecha_mod,
        'tamano' => $tamano_kb . ' KB'
    ];
}

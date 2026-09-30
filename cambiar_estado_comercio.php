<?php
session_start();
require 'includes/db.php';

// Validar permisos (Solo Superadministrador puede aprobar/rechazar)
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: comercios.php?error=No tienes permisos para realizar esta acción.");
    exit();
}

if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id_comercio = intval($_GET['id']);
    $nuevo_estado = $_GET['estado'];

    if (in_array($nuevo_estado, ['Aprobado', 'Rechazado'])) {
        try {
            $pdo->beginTransaction();

            // Actualizar el estado del comercio
            $stmt = $pdo->prepare("UPDATE Comercios SET estado = ? WHERE id_comercio = ?");
            $stmt->execute([$nuevo_estado, $id_comercio]);

            // Registrar en la tabla de Auditoría
            $accion = "Cambio de estado a " . $nuevo_estado;
            $auditoria = $pdo->prepare("INSERT INTO Auditoria (id_usuario_admin, accion, tabla_afectada, registro_id) VALUES (?, ?, 'Comercios', ?)");
            $auditoria->execute([$_SESSION['usuario_id'], $accion, $id_comercio]);

            $pdo->commit();

            header("Location: comercios.php?mensaje=El comercio ha sido " . strtolower($nuevo_estado) . " exitosamente.");
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            header("Location: comercios.php?error=Error al procesar la solicitud.");
            exit();
        }
    } else {
        header("Location: comercios.php?error=Estado inválido.");
        exit();
    }
} else {
    header("Location: comercios.php");
    exit();
}
?>

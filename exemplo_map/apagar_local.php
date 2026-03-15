<?php
/**
 * @file apagar_local.php
 * @brief Apaga um local após verificação de permissões.
 * @author Marco
 * @date 2026-03-15
 */

require __DIR__ . '/auth.php';
require_login();
require __DIR__ . '/db.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID não fornecido.']);
    exit;
}

try {
    // Verificar permissões
    $stmt = $pdo->prepare("SELECT criado_por FROM locais WHERE id = ?");
    $stmt->execute([$id]);
    $local = $stmt->fetch();

    if (!$local) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Local não encontrado.']);
        exit;
    }

    if (!is_admin() && $local['criado_por'] != get_logged_user_id()) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Sem permissões para apagar este local.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM locais WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['status' => 'ok', 'mensagem' => 'Local apagado com sucesso.']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro BD: ' . $e->getMessage()]);
}

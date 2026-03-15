<?php
/**
 * @file api_categorias.php
 * @brief Devolve as categorias disponíveis em JSON.
 * @author Marco
 * @date 2026-03-15
 */

require __DIR__ . '/db.php';

$stmt = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($categorias, JSON_UNESCAPED_UNICODE);

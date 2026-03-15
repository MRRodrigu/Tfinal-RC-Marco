<?php
/**
 * @file api_locais.php
 * @brief Devolve a lista de locais suportando filtros de cidade e categoria.
 */

require __DIR__ . '/db.php';

$cidade = $_GET['cidade'] ?? '';
$pais = $_GET['pais'] ?? '';
$categoria_id = $_GET['categoria_id'] ?? '';

$sql = "
    SELECT 
        l.id,
        l.nome,
        l.cidade,
        l.pais,
        l.morada,
        l.telefone,
        l.email,
        l.descricao,
        l.latitude,
        l.longitude,
        l.criado_por,
        c.nome AS categoria,
        c.cor,
        c.letras,
        c.id AS categoria_id,
        GROUP_CONCAT(f.ficheiro) AS fotos
    FROM locais l
    JOIN categorias c ON l.categoria_id = c.id
    LEFT JOIN fotos f ON l.id = f.local_id
    WHERE l.latitude IS NOT NULL 
      AND l.longitude IS NOT NULL
";

$params = [];

if ($pais !== '') {
    $sql .= " AND l.pais LIKE :pais";
    $params[':pais'] = '%' . $pais . '%';
}

if ($cidade !== '') {
    $sql .= " AND l.cidade LIKE :cidade";
    $params[':cidade'] = '%' . $cidade . '%';
}

if ($categoria_id !== '') {
    $sql .= " AND l.categoria_id = :categoria_id";
    $params[':categoria_id'] = $categoria_id;
}

$sql .= " GROUP BY l.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$locais = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($locais, JSON_UNESCAPED_UNICODE);

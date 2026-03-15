<?php
require __DIR__ . '/db.php';

// Recebe dados do formulário
$nome      = $_POST['nome'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$pais      = $_POST['pais'] ?? '';
$cidade    = $_POST['cidade'] ?? '';
$morada    = $_POST['morada'] ?? '';
$telefone  = $_POST['telefone'] ?? '';
$email     = $_POST['email'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$latitude  = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;

// Valida campos obrigatórios
if (!$nome || !$categoria || !$pais || !$cidade || !$latitude || !$longitude) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Campos obrigatórios em falta.'
    ]);
    exit;
}

try {
    // Procura o ID da categoria
    $stmt = $pdo->prepare("SELECT id FROM categorias WHERE nome = ?");
    $stmt->execute([$categoria]);
    $categoria_id = $stmt->fetchColumn();

    if (!$categoria_id) {
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Categoria inválida.'
        ]);
        exit;
    }

    // Insere o local
    $stmt = $pdo->prepare("
        INSERT INTO locais 
        (nome, categoria_id, criado_por, pais, cidade, morada, telefone, email, descricao, latitude, longitude)
        VALUES 
        (:nome, :categoria_id, :criado_por, :pais, :cidade, :morada, :telefone, :email, :descricao, :latitude, :longitude)
    ");

    $stmt->execute([
        ':nome'         => $nome,
        ':categoria_id' => $categoria_id,
        ':criado_por'   => 1, // podes alterar para o utilizador logado
        ':pais'         => $pais,
        ':cidade'       => $cidade,
        ':morada'       => $morada,
        ':telefone'     => $telefone,
        ':email'        => $email,
        ':descricao'    => $descricao,
        ':latitude'     => $latitude,
        ':longitude'    => $longitude
    ]);

    echo json_encode([
        'status' => 'ok',
        'mensagem' => 'Local inserido com sucesso.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao inserir local: ' . $e->getMessage()
    ]);
}

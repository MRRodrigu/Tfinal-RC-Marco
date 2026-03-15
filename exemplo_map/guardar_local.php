<?php
/**
 * @file guardar_local.php
 * @brief Adiciona ou edita um local, lidando com permissões.
 * @author Marco
 * @date 2026-03-15
 */

require __DIR__ . '/auth.php';
require_login();
require __DIR__ . '/db.php';

$acao      = $_POST['acao'] ?? 'inserir';
$id        = $_POST['id'] ?? null;
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

if (!$nome || !$categoria || !$pais || !$cidade || !$latitude || !$longitude) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Campos obrigatórios em falta.']);
    exit;
}

try {
    // Se "categoria" for uma string, ir buscar o ID atual da categoria
    // NOTA: Como mudei o JS para enviar o text em vez do value se quiseres manter string.
    // Mas é melhor assumir que o select da categoria envia o nome_da_categoria.
    $stmt = $pdo->prepare("SELECT id FROM categorias WHERE nome = ?");
    $stmt->execute([$categoria]);
    $categoria_id = $stmt->fetchColumn();

    if (!$categoria_id) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Categoria não encontrada.']);
        exit;
    }

    if ($acao === 'editar' && $id) {
        // Verificar permissões
        $stmt = $pdo->prepare("SELECT criado_por FROM locais WHERE id = ?");
        $stmt->execute([$id]);
        $local = $stmt->fetch();

        if (!$local) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Local não encontrado.']);
            exit;
        }
        
        if (!is_admin() && $local['criado_por'] != get_logged_user_id()) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Sem permissão para editar.']);
            exit;
        }

        $sql = "UPDATE locais SET 
            nome = :nome, categoria_id = :cat, pais = :pais, cidade = :cidade, 
            morada = :morada, telefone = :telefone, email = :email, 
            descricao = :desc, latitude = :lat, longitude = :lng
            WHERE id = :id";
            
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome, ':cat' => $categoria_id, ':pais' => $pais,
            ':cidade' => $cidade, ':morada' => $morada, ':telefone' => $telefone,
            ':email' => $email, ':desc' => $descricao, ':lat' => $latitude,
            ':lng' => $longitude, ':id' => $id
        ]);
        
        $local_id = $id;
        echo json_encode(['status' => 'ok', 'mensagem' => 'Editado com sucesso']);

    } else {
        // Inserir novo
        $sql = "INSERT INTO locais 
        (nome, categoria_id, criado_por, pais, cidade, morada, telefone, email, descricao, latitude, longitude)
        VALUES 
        (:nome, :cat, :criado_por, :pais, :cidade, :morada, :telefone, :email, :desc, :lat, :lng)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome, ':cat' => $categoria_id, ':criado_por' => get_logged_user_id(),
            ':pais' => $pais, ':cidade' => $cidade, ':morada' => $morada, 
            ':telefone' => $telefone, ':email' => $email, ':desc' => $descricao, 
            ':lat' => $latitude, ':lng' => $longitude
        ]);
        
        $local_id = $pdo->lastInsertId();
        echo json_encode(['status' => 'ok', 'mensagem' => 'Inserido com sucesso']);
    }

    // Processamento da Foto (se enviada)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('foto_') . '.' . $ext;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
            $stmt = $pdo->prepare("INSERT INTO fotos (local_id, ficheiro) VALUES (?, ?)");
            $stmt->execute([$local_id, $filename]);
        }
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro BD: ' . $e->getMessage()]);
}

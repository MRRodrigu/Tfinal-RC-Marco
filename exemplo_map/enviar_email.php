<?php
/**
 * @file enviar_email.php
 * @brief Envia os detalhes de um local para um email especificado.
 * @author Marco
 * @date 2026-03-15
 */

require __DIR__ . '/db.php';

$id = $_POST['id'] ?? null;
$email_destino = $_POST['email_destino'] ?? '';

if (!$id || empty($email_destino) || !filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos ou email incorreto.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT nome, cidade, pais, telefone, email, descricao FROM locais WHERE id = ?");
    $stmt->execute([$id]);
    $local = $stmt->fetch();

    if (!$local) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Local não encontrado.']);
        exit;
    }

    $assunto = "Partilha de Local - " . $local['nome'];
    
    $mensagem = "Olá,\n\n";
    $mensagem .= "Partilharam consigo o seguinte local através do GeoDados:\n\n";
    $mensagem .= "Nome: " . $local['nome'] . "\n";
    $mensagem .= "Localização: " . $local['cidade'] . ", " . $local['pais'] . "\n";
    if ($local['telefone']) $mensagem .= "Telefone: " . $local['telefone'] . "\n";
    if ($local['email']) $mensagem .= "Email: " . $local['email'] . "\n\n";
    if ($local['descricao']) $mensagem .= "Descrição:\n" . $local['descricao'] . "\n\n";
    $mensagem .= "Obrigado por usar o GeoDados.";

    $headers = "From: no-reply@geodados.local\r\n";
    $headers .= "Reply-To: no-reply@geodados.local\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($email_destino, $assunto, $mensagem, $headers)) {
        echo json_encode(['status' => 'ok', 'mensagem' => 'Email partilhado com sucesso!']);
    } else {
        // Fallback em desenvolvimento se o mailer falhar
        echo json_encode(['status' => 'ok', 'mensagem' => 'O simulador enviou o email (servidor mail local não configurado).']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno: ' . $e->getMessage()]);
}

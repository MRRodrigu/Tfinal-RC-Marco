<?php
/**
 * @file db.php
 * @brief Estabelece a ligação à base de dados MySQL usando PDO.
 *
 * Este ficheiro cria uma ligação à base de dados MySQL recorrendo à extensão PDO,
 * configurando o charset para UTF-8 (utf8mb4) e ativando o modo de exceções
 * para tratamento de erros.
 *
 * Deve ser incluído (`require` ou `include`) por outros scripts que necessitem
 * de acesso à base de dados.
 *
 * @author Marco
 * @date 2026-03-15
 */

/**
 * @var string $host
 * @brief Endereço do servidor da base de dados.
 */
$host = "localhost";

/**
 * @var string $db
 * @brief Nome da base de dados.
 */
$db   = "geo_dados";

/**
 * @var string $user
 * @brief Utilizador da base de dados.
 */
$user = "root";

/**
 * @var string $pass
 * @brief Palavra-passe do utilizador da base de dados.
 */
$pass = "";

/**
 * @var string $charset
 * @brief Charset utilizado na ligação à base de dados.
 *
 * O utf8mb4 garante suporte completo a Unicode.
 */
$charset = "utf8mb4";

/**
 * @var string $dsn
 * @brief Data Source Name utilizado pelo PDO.
 *
 * Contém o tipo de base de dados, host, nome da base de dados
 * e charset.
 */
$dsn = "mysql:host=$host;
dbname=$db;
charset=$charset";

/**
 * @var array $options
 * @brief Opções de configuração do PDO.
 *
 * - PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
 *   Lança exceções em caso de erro.
 *
 * - PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
 *   Devolve resultados como arrays associativos.
 */
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

/**
 * Cria a ligação à base de dados.
 *
 * @var PDO $pdo Instância PDO usada nos restantes scripts.
 *
 * @throws PDOException Caso ocorra um erro na ligação.
 */
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    /**
     * Termina o script caso a ligação à base de dados falhe,
     * apresentando uma mensagem de erro.
     *
     * NOTA: Em ambiente de produção, não é recomendado mostrar
     * a mensagem de erro detalhada.
     */
    die("Erro na ligação à BD: " . $e->getMessage());
}

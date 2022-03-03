<?php
// Pendente de validação de erros !!! 
// Iniciar utilização de sessão:
session_start();
// Verificar se o usuário não está logado:
if (!isset($_SESSION['infosusuario'])) {
    // Redirecionar de volta à tela de login:
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Cadastro realizado com sucesso!";
    $status["status"] = 1;
    echo json_encode($status);
}
// Puxar o arquivo de conexão com o banco de dados:
include('db/banco.php');

$pdo = Banco::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obter as informações do produto e verificar se ele pertence ao usuário logado:
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT idRespCadastro FROM produtos WHERE codbarras = ?";
$q = $pdo->prepare($sql);
$q->execute(array($_POST['idProduto']));
// Resultado do BD:
$data = $q->fetch(PDO::FETCH_ASSOC);
if ($data['idRespCadastro'] != $_SESSION['infosusuario']['idUsuario']) {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Este produto não te pertence!";
    $status["status"] = 0;
    echo json_encode($status);
    exit();
} else {
    // Definir fuso horário:
    date_default_timezone_set('America/Sao_Paulo');
    $codbarras = $_POST['idProduto'];
    $idproduto = $_POST['idProduto'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $idCategoria = $_POST['idCategoria'];
    // Obter o ID do usuário pela sessão atual:
    $idResp = $_SESSION['infosusuario']['idUsuario'];

    $sql = "UPDATE produtos SET codbarras = ?, nome = ?, preco = ?, estoque = ?, idCategoria = ? WHERE codbarras = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($codbarras, $nome, $preco, $estoque, $idCategoria, $idproduto));
    Banco::desconectar();
    // Devolver o usuário para tela de administração:
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Produto modificado com sucesso !";
    $status["status"] = 1;
    echo json_encode($status);
}
?>


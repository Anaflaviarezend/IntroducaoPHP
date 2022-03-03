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

// Verificar se a pessoa está logada:

// Puxar o arquivo de conexão com o banco de dados:
include('db/banco.php');

// Definir fuso horário:
date_default_timezone_set('America/Sao_Paulo');

// Verificar se nome e/ou código de barras não está vazios:
if ($_POST['codBarras'] != "" && $_POST['nome'] != "" && strlen($_POST['codBarras']) == 5) {
    $codbarras = $_POST['codBarras'];
    $nome = $_POST['nome'];
} else { 
     http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "dados imcompletos!";
    $status["status"] = 0;
    echo json_encode($status);
   
    exit();
}


// Verificar se está chegando um valor inteiro/float pelo post
if (intval($_POST['preco']) != 0) {
    $preco = $_POST['preco'];
} else {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "valor invalido!";
    $status["status"] = 0;
    echo json_encode($status);
    exit();
}

// Verificar se está chegando um valor inteiro pelo post
if (floatval($_POST['qtdEstoque']) != 0) {
    $qtdEstoque = $_POST['qtdEstoque'];
} else {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "valor invalido!";
    $status["status"] = 0;
    echo json_encode($status);
    exit();
}


$categoria = $_POST['categoria'];
// Obter o ID do usuário pela sessão atual:
$idResp = $_SESSION['infosusuario']['idUsuario'];

$foto = "fotos/semfoto.jpg";


// Caso a foto não esteja definida, setar para fotos/semfoto.jpg
try {
    $pdo = Banco::conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO produtos (codbarras, nome, preco, estoque, idCategoria, idRespCadastro, foto) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($codbarras, $nome, $preco, $qtdEstoque, $categoria, $idResp, $foto));
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Cadastro realizado com sucesso!";
    $status["status"] = 1;
    echo json_encode($status);
} catch (PDOException $e) {
    Banco::desconectar();
    if ($e->getCode() == 23000) {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "codigo ja cadastrado!";
        $status["status"] = 0;
        echo json_encode($status);
        exit();
    } else {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "erro desconhecido!";
        $status["status"] = 0;
        echo json_encode($status);
        exit();
    }
}

Banco::desconectar();

// Devolver o usuário para tela de administração:
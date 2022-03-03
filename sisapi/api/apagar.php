<?php
// Iniciar sessão:
session_start();
// Verificar se o usuário não está logado:
if (!isset($_SESSION['infosusuario'])) {
    // Redirecionar de volta à tela de login:
    //header('Location: ../index.php');
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    $status["mensagem"] = "Produto removido com sucesso!";
    $status["status"] = 1;
    echo json_encode($status);
} else {
    // Continar caso o usuário esteja logado:
    // Importar o banco.php
    require 'db/banco.php';
    // Variável para armazenar o CODBarras do produto a ser removido:
    // apagar.php?id=21545
    $item = $_POST['codBarras'];
    //echo 'Você vai apagar o item ' .$item;

    // Antes de apagar, devemos verificar se o usuário é os resp pelo cadastro:
    $pdo = Banco::conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT idRespCadastro FROM produtos WHERE codbarras = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($item));
    // Resultado do BD:
    $data = $q->fetch(PDO::FETCH_ASSOC);
    // Verificar se o banco devolveu algum resultado:
    if (!is_array($data)) {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        $status["mensagem"] = "Este produto não te pertence!";
        $status["status"] = 0;
        echo json_encode($status);
        //header("Location: index.php?msg=3");
        Banco::desconectar();
    } else {
        // Se idUsuario == idRespCadastro e devo apagar o produto:
        if ($_SESSION['infosusuario']['idUsuario'] == $data['idRespCadastro']) {
            $sql = "DELETE FROM produtos WHERE codbarras = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($item));
            Banco::desconectar();
            // Redirecionar de volta ao painel:
            //header("Location: index.php?msg=0");
             http_response_code(200);
             header('Content-Type: application/json; charset=utf-8');
             $status["mensagem"] = "Produto removido com sucesso!";
             $status["status"] = 1;
            echo json_encode($status);
        } else {
            Banco::desconectar();
            //echo "Este produto não te pertence!";
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            $status["mensagem"] = "Este produto não te pertence!";
            $status["status"] = 0;
            echo json_encode($status);
        }
        Banco::desconectar();
    }
}

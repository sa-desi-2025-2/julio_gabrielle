<?php
session_start();

$host = 'localhost'; 
$db   = 'almoxarifado_utilidades';
$usuario = 'root';
$senha = '';

$conn = new mysqli($host, $usuario, $senha, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


if (!isset($_POST['re']) || empty($_POST['re'])) {
    echo "<h3>❌ RE não informado.</h3>";
    echo "<a href='entra_na_conta.html'>Voltar</a>";
    exit();
}

$re = $conn->real_escape_string($_POST['re']);

$sql = "SELECT * FROM funcionarios WHERE RE = '$re'";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    $funcionario = $resultado->fetch_assoc();


    $_SESSION['id_funcionario'] = $funcionario['id_funcionario'];
    $_SESSION['nome'] = $funcionario['nome'];
    $_SESSION['adm'] = $funcionario['adm'];
    $_SESSION['RE'] = $funcionario['RE'];

 
    if ($funcionario['adm'] == 1) {
        header("Location: telaeditor.html");
        exit();
    } else {
        header("Location: telaPrincipal.html");
        exit();
    }

} else {
    echo "<h3>❌ RE não encontrado. Acesso negado.</h3>";
    echo "<a href='entra_na_conta.html'>Voltar</a>";
}

$conn->close();
?>
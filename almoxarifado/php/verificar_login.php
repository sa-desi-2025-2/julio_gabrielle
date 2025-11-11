<?php
session_start();

$host = 'localhost'; 
$db   = 'almoxarifado_utilidades';
$usuario = 'root';
$senha = '';

$conn = new mysqli($host, $usuario, $senha, $db);

if ($conn->connect_error) {

    header("Location: ../html/entrarNaConta.html?erro=3");
    exit();
}


if (!isset($_POST['RE']) || empty(trim($_POST['RE']))) {

    header("Location: ../html/entrarNaConta.html?erro=2");
    exit();
}

$re = $conn->real_escape_string($_POST['RE']);


$sql = "SELECT * FROM funcionarios WHERE RE = ? AND ativo=1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $re);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $funcionario = $resultado->fetch_assoc();

    $_SESSION['id_funcionario'] = $funcionario['id_funcionario'];
    $_SESSION['nome'] = $funcionario['nome'];
    $_SESSION['adm'] = $funcionario['adm'];
    $_SESSION['RE'] = $funcionario['RE'];

    $stmt->close();
    $conn->close();
 
    if ($funcionario['adm'] == 1) {
        header("Location: ../php.front/telaeditor.php");
        exit();
    } else {
        header("Location: ../php.front/telaPrincipal.php");
        exit();
    }

} else {

    $stmt->close();
    $conn->close();
    header("Location: ../html/entrarNaConta.html?erro=1");
    exit();
}
?>
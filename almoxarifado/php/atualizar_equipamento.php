<?php
session_start();
include 'conexao.php';


if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "<script>alert('Acesso negado.'); window.history.back();</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $id_equipamento = (int)($_POST["id_equipamento"] ?? 0);
    $nome = $_POST["nome"];
    $fabricante = $_POST["fabricante"];
    $quantidade = (int)$_POST["quantidade"];
    $descricao = $_POST["descricao"];

    if ($id_equipamento <= 0) {
        echo "<script>alert('Erro: ID do equipamento inválido.'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("UPDATE equipamentos SET nome = ?, fabricante = ?, quantidade = ?, descricao = ? WHERE id_equipamento = ?");
    $stmt->bind_param("ssisi", $nome, $fabricante, $quantidade, $descricao, $id_equipamento);

    if ($stmt->execute()) {
        echo "
        <script>
          alert('Equipamento atualizado com sucesso!');
          // Redireciona de volta para a tela de admin
          window.location.href = '../php.front/telaeditor.php';
        </script>
        ";
    } else {
        echo "
        <script>
          alert('Erro ao atualizar equipamento: " . $stmt->error . "');
          window.history.back();
        </script>
        ";
    }

    $stmt->close();
}

$conn->close();
?>
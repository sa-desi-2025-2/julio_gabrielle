<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    echo "<script>alert('Acesso negado.'); window.history.back();</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $id_funcionario = (int)($_POST["id_funcionario"] ?? 0);
    $nome = $_POST["nome"];
    $cargo = $_POST["cargo"];
    $id_admin_logado = $_SESSION['id_funcionario'] ?? 0; 

    if ($id_funcionario <= 0) {
        echo "<script>alert('Erro: ID do funcionário inválido.'); window.history.back();</script>";
        exit;
    }

    if ($id_funcionario == $id_admin_logado) {
        
        $stmt = $conn->prepare("UPDATE funcionarios SET nome = ?, cargo = ? WHERE id_funcionario = ?");
        $stmt->bind_param("ssi", $nome, $cargo, $id_funcionario);

    } else {
        
        $ehAdmin = (isset($_POST['administrador']) && $_POST['administrador'] == 'on') ? 1 : 0;

      
        $stmt = $conn->prepare("UPDATE funcionarios SET nome = ?, cargo = ?, adm = ? WHERE id_funcionario = ?");
        $stmt->bind_param("ssii", $nome, $cargo, $ehAdmin, $id_funcionario);
    }


    if ($stmt->execute()) {
        echo "
        <script>
          alert('Funcionário atualizado com sucesso!');
          window.location.href = '../php.front/telaeditor.php?view=funcionarios';
        </script>
        ";
    } else {
        echo "<script>alert('Erro ao atualizar: " . $stmt->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}
$conn->close();
?>
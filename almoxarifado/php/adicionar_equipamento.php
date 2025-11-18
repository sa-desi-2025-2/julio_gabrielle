<?php
session_start();
include 'conexao.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    
    $nome = trim($_POST["nome"] ?? '');
    $fabricante = trim($_POST["fabricante"] ?? '');
    $quantidade = (int)($_POST["quantidade"] ?? 0); 
    $descricao = trim($_POST["descricao"] ?? '');

    
    if (empty($nome) || $quantidade <= 0) {
        echo "
        <script>
          alert('Erro: Nome e Quantidade (maior que 0) são obrigatórios.');
          window.history.back();
        </script>
        ";
        exit;
    }

    $sql = "INSERT INTO equipamentos (nome, fabricante, quantidade, descricao) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ssis", $nome, $fabricante, $quantidade, $descricao);

        if ($stmt->execute()) {
            echo "
            <script>
              alert('Equipamento adicionado com sucesso!');
              window.location.href = '../php.front/telaeditor.php';
            </script>
            ";
        } else {
            echo "
            <script>
              alert('Erro ao inserir no banco: " . addslashes($stmt->error) . "');
              window.history.back();
            </script>
            ";
        }
        $stmt->close();
    } else {
        echo "
        <script>
          alert('Erro na preparação da consulta: " . addslashes($conn->error) . "');
          window.history.back();
        </script>
        ";
    }
}

$conn->close();
?>
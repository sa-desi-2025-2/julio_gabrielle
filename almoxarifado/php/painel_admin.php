<?php
session_start();


if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    
  
    if (isset($_SESSION['id_funcionario'])) {
        header("Location: ../php.front/telaPrincipal.php");
    } else {
        
        header("Location: ../html/entrarNaConta.html");
    }
    exit();
}
?>
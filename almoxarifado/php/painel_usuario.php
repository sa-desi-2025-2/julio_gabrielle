<?php
session_start();
if (!isset($_SESSION['id_funcionario'])) {
  header("Location: ../php.front/telaPrincipal.php");
  exit();
}
?>
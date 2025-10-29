<?php
session_start();
if (!isset($_SESSION['id_funcionario'])) {
  header("Location: ../html/telaPrincipal.html");
  exit();
}
?>
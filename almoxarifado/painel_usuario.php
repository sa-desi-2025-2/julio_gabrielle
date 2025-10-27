<?php
session_start();
if (!isset($_SESSION['id_funcionario'])) {
  header("Location: entra_na_conta.html");
  exit();
}
?>
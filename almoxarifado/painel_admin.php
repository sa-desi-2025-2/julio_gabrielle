<?php
session_start();
if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
  header("Location: entra_na_conta.html");
  exit();
}
?>
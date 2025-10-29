<?php
session_start();
if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
  header("Location: ../html/telaeditor.html");
  exit();
}
?>
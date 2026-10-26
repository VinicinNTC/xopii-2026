<?php
include 'conexao.php';
session_destroy();
header("Location: login.php");
exit;
?>
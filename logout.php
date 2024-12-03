<?php 
ob_start();
session_start();
include 'admin/config.php';
unset($_SESSION['seller']);
header("location: login.php"); 
?>
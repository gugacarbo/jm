<?php
//verify if session login is valid
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
} else {
    $user = $_SESSION['user'];
}
?>

<a href="about">Sobre</a>
<a href="banner">Banner</a>
<a href="category">Category</a>
<a href="client">client</a>
<a href="purshases">Vendas</a>
<a href="products">Products</a>
<a href="carousel">Carrossel</a>
<a href="configShipping">configShipp</a>
<a href="material">materiais</a>
<a href="reviewPurshases">review</a>
<a href="logout">Logout</a>

purchases
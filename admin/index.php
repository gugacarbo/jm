<?php
//verify if session login is valid
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    header("Location: api/login/login.php");
    exit;
} else {
    $user = $_SESSION['user'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">
    <link rel="stylesheet" href="admin.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>

<body>
    <div class='adminHeader'>
        <span>Olá <?php echo $user; ?></span>
        <span class="PageTitle" id="TitleHeader"></span>
        <label>
            <span id="dateH"></span>
            <span id="dateT"></span>
        </label>
    </div>
    <div class='adminMenu'>
        <nav class="adminMenu" id="MenuContent">
            <div class="menuToggleBox" id="MenuToggle">
                <div class="menuToggle">
                    <div class="bars"></div>
                    <div class="bars"></div>
                    <div class="bars"></div>
                </div>
            </div>
            <a onclick="changePage('home')">
                <label>Home</label>
                <i class="fas fa-home"></i>
            </a>
            <a onclick="changePage('purchases')">
                <label>Vendas</label>
                <i class="fas fa-shopping-cart"></i>
            </a>
            <a onclick="changePage('products')">
                <label>Produtos</label>
                <i class="fas fa-box-open"></i>
            </a>
            <a onclick="changePage('banner')">
                <label>Banner</label>
                <i class="fa-solid fa-panorama"></i>
            </a>
            <a onclick="changePage('category')">
                <label>Categorias</label>
                <i class="fas fa-list-ul"></i>
            </a>
            <a onclick="changePage('material')">
                <label>Materiais</label>
                <i class="fa-solid fa-gem"></i>
            </a>
            <a onclick="changePage('carousel')">
                <label>Carrossel</label>
                <i class="fa-solid fa-dharmachakra"></i>
            </a>
            <a onclick="changePage('about')">
                <label>Sobre</label>
                <i class="fas fa-info-circle"></i>
            </a>
            <a onclick="changePage('configFree')">
                <label>Cupons e Frete Grátis</label>
                <i class="fa-solid fa-tags"></i>
            </a>

            <a onclick="changePage('reviewPurchases')">
                <label>Pedidos Incompletos</label>
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </a>

            <a onclick="changePage('config')">
                <label>Configurações</label>
                <i class="fas fa-cog"></i>
            </a>
            
            <a href="/admin/api/login/logout.php">
                <label>Sair</label>
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </nav>
    </div>
    <div class="AllContent" id="AllContainer">

    </div>

    <script src="admin.js"></script>
</body>

</html>
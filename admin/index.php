<?php
//verify if session login is valid
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
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
    <script el="preload" as="style" src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <link rel="preload" as="font" type="font/woff2" crossorigin href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/fonts/fontawesome-webfont.woff2?v=4.3.0"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-te/1.4.0/jquery-te.min.js" integrity="sha512-813LH2NdwwzXnVfsmzSuAyyit5bRFdh997hN9Vzm0cdx3LdZV7TZNNb2Ag0dgJPD3J1Xn1Alg2YW70id+RtLrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-te/1.4.0/jquery-te.css" integrity="sha512-YsCGey6C9bmPaAixXc6B7UwLMGW/xQOa0XfZB50ulfXIEOG25W+A2i5GxuYvTN03oX9wOmeN3T22DE/IKdEVcQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.complexify.js/0.4/jquery.complexify.min.js" integrity="sha512-YcnErIfF+yTtHkobQ3KVyvLSx3llHqsS/QjLf9mjf6qs+/DOPwn6Z5rjfoWRyPamrhryj7N9ePbRt1KgSyP1KQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div class='adminHeader'>
        <div class="arrowback" onclick="pageBack()"><i class="fa-solid fa-arrow-left"></i></div>
        <span>Olá <small id="userName"><?php echo $user; ?></small></span>
        <span class="PageTitle" id="TitleHeader"></span>
        <label class="clock">
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
            
            <a onclick="changePage('relatory')">
                <label>Relatórios</label>
                <i class="fas fa-chart-line"></i>
            </a>
            
            <a onclick="changePage('configFree')">
                <label>Cupons e Frete Grátis</label>
                <i class="fa-solid fa-tags"></i>
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
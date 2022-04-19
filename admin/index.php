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

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link rel="stylesheet" href="admin.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <script src="/jquery.mask.js"></script>
</head>

<body>

    <div>
        <h1>Banners</h1>
        <div>
            <span>Home</span>
            <div><img src=""></div>
            <div><img src=""></div>
            <div><img src=""></div>
        </div>
        <div>
            <span>Sobre</span>
            <div><img src=""></div>
            <div><img src=""></div>
            <div><img src=""></div>
        </div>
        <div>
            <span>Produtos</span>
            <div><img src=""></div>
            <div><img src=""></div>
            <div><img src=""></div>
        </div>
    </div>

    <script src="admin.js"></script>
</body>

</html>
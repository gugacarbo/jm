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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="admin.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

</head>

<body>
    <div class="adminContainer">


        <script>
            $(".adminContainer").append($("<div class='adminHeader'>").load("header.html"));
            $(".adminContainer").append($("<div class='adminMenu'>").load("menu.html"));
        </script>
</body>

</html>

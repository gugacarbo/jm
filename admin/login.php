<?php
session_start();
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
if (isset($_GET["error"])) {

    $erro = $_GET["error"];
    if (isset($_GET["time"])) {
        $time = $_GET["time"];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body{
            background-color: #f5f5f5;
            min-height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .loginBox{
            width: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 15px 0;
            background-color: rgba(200, 160, 170, 0.6);
            border-radius: 30px;
            backdrop-filter: blur(2px);

        }
        .loginBox header{
            width: 100%;
            display: flex;
            flex-direction: column;
            row-gap: 30px;
            column-gap: 10px;
            align-items: center;
        }
        .loginBox header h4{
            width: 100%;
            text-align: center;
            font-size: 18pt;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            color: #fff;
        }

        .loginBox header div {
            width: 110px;
            height: 5   0px;
            display: flex;
            justify-content: center;
            overflow: hidden;
            height: 100%;
            position: relative;
        }
        .loginBox header div img {
            width: 100%;
            height: 100%;
        }
        .loginBox input{
            border: none;
            padding: 3px 10px;
            font-size: 13pt;
            border-radius: 20px;
        }
        .loginBox input[type="submit"]{
            padding: 5px 20px;
            width: 40%;
            color: #fff;
            background-color: #b66d76;
            cursor: pointer;
        }
        .loginBox input[type="submit"]:hover{
            filter: brightness(1.1);
        }
        .loginBox input[type="submit"]:active{
            filter: brightness(0.9);
        }
        .loginBox small{
            color: #fff;
            text-shadow: 0 0 2px #66699966;
        }
        </style>
</head>

<body>
    <form method="POST" action="in.php">
    <div class="loginBox">
        <header>
            <h4>Login Administrador</h4>
            <div>

              
                    <img src="logoRosa.png">
                </div>
        </header>
            <input type="text" name="username" placeholder="Usuário" required>
            <input type="password" name="password" placeholder="Senha" required>
            <input type="submit" name="submit" value="Entrar">
            <small>&nbsp<?php if (isset($erro) && $erro == 1) {
                        echo "Excesso de tentativas, tente novamente em " . $time . " segundos";
                    } else if (isset($erro) && $erro == 0) {
                        echo "usuário ou senha incorretos";
                    }   ?></small>
    </div>
</form>
</body>

</html>
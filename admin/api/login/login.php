<?php
session_start();
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    header("Location: ../../index.php");
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
            box-sizing: border-box;
            font-family: 'Roboto', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            
        }

        body {
            min-height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loginBox {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2), 0 0 20px rgba(0, 0, 0, 0.1);
            background-image: radial-gradient(circle at center, #b66d76, #865c66);
        }

        .loginBox header {
            width: 100%;
            display: flex;
            flex-direction: column;
            row-gap: 30px;
            column-gap: 10px;
            align-items: center;
        }

        .loginBox header h4 {
            width: 100%;
            text-align: center;
            font-size: 18pt;
            color: #fff;
            letter-spacing: 2px;
        }

        .loginBox header div {
            width: 400px;
            height: 50px;
            display: flex;
            justify-content: center;
            overflow: hidden;
            height: 100%;
            position: relative;
            gap: 20px;
            align-items: center;
        }

        .loginBox header div img:nth-child(1) {
            width: 50%;
            height: 100%;
        }
        .loginBox header div img:nth-child(2) {
            width: 35%;
            height: 100%
        }

        .loginBox input {
            border: none;
            padding: 4px 15px;
            font-size: 11pt;
            border: 1px solid #b66d76;
            width: 400px;
            text-align: center;
        }

        .loginBox input[type="submit"] {
            padding: 5px 20px;
            width: 200px;
            color: #b66d76;
            border: 1px solid #b66d76;
            background-color: #fff;
            cursor: pointer;
            font-weight: bold;
        }

        .loginBox input[type="submit"]:hover {
            filter: brightness(1.1);
        }

        .loginBox input[type="submit"]:active {
            filter: brightness(0.9);
        }

        .loginBox small {
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
                    <img src="../..//img/logoBranco.png">
                    <img src="../..//img/scudero.png">
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
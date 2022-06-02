
<?php

    
if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 4) {
    //header("Location: ../index.php");
    //die(json_encode(array('status' => 403, 'message' => 'Forbidden')));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin</title>
        <link rel="icon" href="/img/Jm_Logo_Branco.png">

        <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">
        <link rel="preload" as="font" type="font/woff2" crossorigin
            href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/fonts/fontawesome-webfont.woff2?v=4.3.0" />

        <link rel="stylesheet" href="restrict.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script el="preload" as="style" src="https://kit.fontawesome.com/dd47628d23.js"
            crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js"
            integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js"></script>
        <script
            src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    </head>
</head>

<body>
    <div class="restrictContent">


        <label class="box total">
            <input type="radio" name="selectGraph1" >
            <div class="boxContent">
                <span class="boxTitle">
                    Total
                    <i class="fa-solid fa-gem"></i>
                </span>
                <span class="boxValue"><small>R$</small><b id="InfoTotalAmount"></b><small>,</small><b></b></span>
            </div>
        </label>

        <label class="box mes">
            <input type="radio" name="selectGraph1" checked>
            <div class="boxContent">
                <span class="boxTitle">
                    Esse Mês
                    <i class="fa-brands fa-bitcoin"></i>
                </span>
                <span class="boxValue"><small>R$</small><b id="InfoMonth"></b><small>,</small><b></b></span>

            </div>
        </label>

        <label class="box futuro">
            <input type="radio" name="selectGraph2" checked>
            <div class="boxContent">
                <span class="boxTitle">
                    Futuros
                    <i class="fa-solid fa-piggy-bank"></i>
                </span>
                <span class="boxValue"><small>R$</small><b id="InfoFuture"></b><small>,</small><b></b></span>

            </div>
        </label>

        <label class="box cancelado">
            <input type="radio" name="selectGraph2">
            <div class="boxContent">
                <span class="boxTitle">
                    Cancelamentos
                    <i class="fa-solid fa-skull"></i>
                </span>
                <span class="boxValue"><small>R$</small><b id="InfoCanceled"></b><small>,</small><b></b></span>
                
            </div>
        </label>


        <div class="menu">
            <div class="menuContent">

            </div>
        </div>

        <div class="content">
            <div class="chartBox">
                <canvas id="myChart">

                </canvas>
            </div>
            <div class="listBox">
                <span class="listTitle">FUTUROS</span>
                <div class="listHeader">
                    <span class="date">COMPRA</span>
                    <span class="date">RECEBIMENTO</span>
                    <span class="value">VALOR TOTAL</span>
                    <span class="value">CUSTO</span>
                    <span class="netAmount">LUCRO TOTAL</span>
                    <span class="finalNet">LUCRO DS</span>
                </div>
                <div class="listContent" id="totalList">
                   
                </div>
            </div>
        </div>
    </div>
    <script src="restrict.js"></script>
</body>

</html>
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
        <link rel="preload" as="font" type="font/woff2" crossorigin href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/fonts/fontawesome-webfont.woff2?v=4.3.0" />


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script el="preload" as="style" src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery.json-viewer@1.5.0/json-viewer/jquery.json-viewer.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery.json-viewer@1.5.0/json-viewer/jquery.json-viewer.css">


        <link rel="stylesheet" href="restrict.css">

    </head>
</head>

<body>
    <div class="restrictContent">
        <div class="overPage overRelatory">
            <div class="overPageContent">
                <div class="list">
                    <span class="listTitle ">VENDAS RECEBIDAS 05/2022</span>
                    <div class="listHeader">
                        <span class="date">DATA</span>
                        <span class="date">Id</span>
                        <span class="value">PRODUTOS</span>
                        <span class="value">VALOR TOTAL</span>
                        <span class="value">CUSTO TOTAL</span>
                        <span class="value">DESCONTOS</span>
                        <span class="value">FRETE</span>
                        <span class="value">LUCRO TOTAL</span>
                        <span class="value">LUCRO DS</span>
                    </div>
                    <div class="listContent" id="relatoryList">

                    </div>
                </div>
                <div class="overPageInfo">
                    <div class="overInfo">
                        <span>Faturamento</span>
                        <i class="fa-solid fa-money-bill"></i>
                        <b>R$<b id="InfoRelatoryInvoicing"></b><small>,</small><b></b></b>

                    </div>
                    <div class="overInfo">
                        <span>Custos</span>
                        <i class="fa-solid fa-coins"></i>
                        <b>R$<b id="InfoRelatoryCosts"></b><small>,</small><b></b></b>
                    </div>
                    <div class="overInfo">
                        <span>Lucro</span>
                        <i class="fa-solid fa-sack-dollar"></i>

                        <b>R$<b id="InfoRelatoryNet"></b><small>,</small><b></b></b>

                    </div>
                    <div class="overInfo">
                        <span>Lucro DS</span>
                        <i class="fa-solid fa-crown"></i>
                        <b>R$<b id="InfoRelatoryNetDS"></b><small>,</small><b></b></b>

                    </div>
                    <div class="overInfo">
                        <span>Cancelamentos</span>
                        <i class="fa-solid fa-xmark"></i>
                        <b>R$<b id="InfoRelatoryCancel"></b><small>,</small><b></b></b>

                    </div>
                    <div class="overInfo generateButton" id="GenerateRelatory">
                        <span>Gerar Relatório</span>
                        <span class="generatedErrorMessage" id="GeneratedErrorMessage"></span>
                        <div class="gears">
                            <i class="fa-solid fa-gear"></i>
                            <i class="fa-solid fa-gear"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overPage overPageConfig">
            <div class="overPageContent">
                <div class="list">
                    <span class="listTitle">Log de Erros</span>

                    <div class="listHeader">
                        <span>Data</span>
                        <span>Type</span>
                        <span>Status Code</span>
                        <span>Error Code</span>
                        <span>Path</span>
                        <span>Message</span>
                    </div>
                    <div class="listContent" id="errorLogContent">
                    </div>
                </div>
                <div class="errorShow" id="ErrorShow">
                <pre id="json-renderer"></pre>
                </div>
            </div>
        </div>

        <div class="menu">
            <div class="menuContent">
                <label class="menuItem" onclick="changeOverPage()">
                    <i class="fa-brands fa-fort-awesome">
                        <p class="menuItemDescription">
                            Home
                        </p>
                    </i>
                </label>
                <label class="menuItem" onclick="changeOverPage(1)">
                    <!--<i class="fa-solid fa-file-signature">-->
                    <i class="fa-solid fa-book-journal-whills">
                        <p class="menuItemDescription">
                            Gerar&nbsp;Relatório
                        </p>
                    </i>
                </label>
                <label class="menuItem" onclick="changeOverPage(2)">
                    <!--<i class="fa-solid fa-dharmachakra">-->
                    <i class="fa-brands fa-galactic-republic">

                        <p class="menuItemDescription">
                            Erros
                        </p>
                    </i>
                </label>
                <label class="menuItem logoutButton">
                    <p class="backJM" onclick="goJm()">
                        Admin&nbspJM
                    </p>
                    <p class="Logout" onclick="logout()">
                        Logout
                    </p>
                    <div class="ship">
                        <i class="fa-solid fa-shuttle-space spaceship"></i>
                        <i class="fa-solid fa-fire-flame-simple fire"></i>
                    </div>
                </label>

            </div>
        </div>


        <label class=" box total">
            <input type="radio" name="selectHome" value="totalList" checked>
            <div class="boxContent">
                <span class="boxTitle">
                    Total
                    <i class="fa-solid fa-gem"></i>
                </span>
                <span class="boxValue"><small>R$</small><b id="InfoTotalAmount"></b><small>,</small><b></b></span>
            </div>
        </label>

        <label class="box mes">
            <input type="radio" name="selectHome" value="monthList">
            <div class="boxContent">
                <span class="boxTitle">
                    Esse Mês
                    <i class="fa-brands fa-bitcoin"></i>
                </span>
                <span class="boxValue"><small>R$</small><b id="InfoMonth"></b><small>,</small><b></b></span>

            </div>
        </label>

        <label class="box futuro">
            <input type="radio" name="selectHome" value="futureList">
            <div class="boxContent">
                <span class="boxTitle">
                    Futuros
                    <i class="fa-solid fa-piggy-bank"></i>
                </span>
                <span class="boxValue"><small>R$</small><b id="InfoFuture"></b><small>,</small><b></b></span>

            </div>
        </label>

        <label class="box cancelado">
            <input type="radio" name="selectHome" value="cancelList">
            <div class="boxContent">
                <span class="boxTitle">
                    Cancelamentos
                    <i class="fa-solid fa-skull"></i>
                </span>
                <small>+<b id="InfoCanceledMonth"></b> <small>,</small><b></b> Esse Mes</small>
                <span class="boxValue"><small>R$</small><b id="InfoCanceled"></b><small>,</small><b></b></span>

            </div>
        </label>



        <div class="content">
            <div class="chartBox">
                <canvas id="restrictChart">

                </canvas>
            </div>
            <div class="listBox">
                <div class="list totalList" style="display: flex;">
                    <span class="listTitle ">RELATÓRIOS</span>
                    <div class="listHeader">
                        <span class="date">DATA</span>
                        <span class="date">FATURAMENTO</span>
                        <span class="value">CANCELAMENTOS</span>
                        <span class="value">LUCRO TOTAL</span>
                        <span class="value">LUCRO DS</span>
                        <span class="download">
                            <i class="fa-solid fa-download"></i>
                        </span>
                    </div>
                    <div class="listContent" id="TotalList">

                    </div>
                </div>
                <div class="list monthList">
                    <span class="listTitle ">PRÓXIMO MÊS</span>
                    <div class="listHeader">
                        <span class="date">COMPRA</span>
                        <span class="date">RECEBIMENTO</span>
                        <span class="value">VALOR TOTAL</span>
                        <span class="value">CUSTO</span>
                        <span class="netAmount">LUCRO TOTAL</span>
                        <span class="finalNet">LUCRO DS</span>
                    </div>
                    <div class="listContent" id="MonthList">

                    </div>
                </div>
                <div class="list cancelList">
                    <span class="listTitle ">CANCELAMENTOS</span>
                    <div class="listHeader">
                        <span class="date">COMPRA</span>
                        <span class="date">RECEBIMENTO</span>
                        <span class="value">VALOR TOTAL</span>
                        <span class="value">CUSTO</span>
                        <span class="netAmount">LUCRO TOTAL</span>
                        <span class="finalNet">LUCRO DS</span>
                    </div>
                    <div class="listContent" id="CancelList">

                    </div>
                </div>
                <div class="list futureList">

                    <span class="listTitle ">FUTUROS</span>
                    <div class="listHeader">
                        <span class="date">COMPRA</span>
                        <span class="date">RECEBIMENTO</span>
                        <span class="value">VALOR TOTAL</span>
                        <span class="value">CUSTO</span>
                        <span class="netAmount">LUCRO TOTAL</span>
                        <span class="finalNet">LUCRO DS</span>
                    </div>
                    <div class="listContent" id="FutureList">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="restrict.js"></script>
</body>

</html>
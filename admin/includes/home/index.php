<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
';

    //die($content);
}
?>

<div class="adminHomeContainer adminContainer">

    <div class="info">
        <div class="infoHeader">
            <div class="infoHeaderIcon">
                <i class="fa-solid fa-check"></i>

            </div>
            <span>Pedidos Aprovados</span>
        </div>
        <div class="infoContent">
            <b class="infoContentNumber" id="HomeInfoAprovadas"></b>
            <p class="infoContentP">Pedidos aprovados</p>
            <button class="infoContentButton" onclick="goToSearch('status=3');changePage('purchases')"> Ver todos </button>
        </div>
    </div>




    <div class="info">
        <div class="infoHeader">
            <div class="infoHeaderIcon">
                <i class="fa-solid fa-times"></i>

            </div>
            <span>Pedidos Cancelados</span>
        </div>
        <div class="infoContent">
            <b class="infoContentNumber" id="HomeInfoCanceladas"></b>
            <p class="infoContentP">Pedidos Cancelados</p>
            <button class="infoContentButton" onclick="goToSearch('status=9');changePage('purchases')"> Ver todos </button>
        </div>
    </div>
    <div class="info">
        <div class="infoHeader">
            <div class="infoHeaderIcon">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <p class="infoContentP">Aguardando Envio</p>
        </div>
        <div class="infoContent">
            <b class="infoContentNumber" id="HomeInfoAguardandoEnvio"></b>
            <p class="infoContentP">Pedidos Aguardando Envio</p>
            <button class="infoContentButton" onclick="goToSearch('tracking=1');changePage('purchases')"> Ver todos </button>
        </div>
    </div>
    <div class="info">
        <div class="infoHeader">
            <div class="infoHeaderIcon">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <p class="infoContentP">Pedidos Não Finalizados</p>
        </div>
        <div class="infoContent">
            <b class="infoContentNumber" id="HomeInfoNaoFinalizados"></b>
            <p class="infoContentP">Pedidos Não Finalizados</p>
            <button class="infoContentButton" onclick="changePage('unfinalizedPurchases')"> Ver todos </button>
        </div>
    </div>
    <div class="info">
        <div class="infoHeader">
            <div class="infoHeaderIcon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <p class="infoContentP">Aguardando Pagamento</p>
        </div>
        <div class="infoContent">
            <b class="infoContentNumber" id="HomeInfoAguardandoPagamento"></b>
            <p class="infoContentP">Pedidos Aguardando Pagamento</p>
            <button class="infoContentButton" onclick="goToSearch('status=1');changePage('purchases')"> Ver todos </button>
        </div>
    </div>




    <div class="relatory">
        <span class="relatoryTitle">Relatórios</span>
        <div class="graph">
            <div class="graphButtons">
                <span>Faturamento</span>
                <div onclick='getHomeChart("month", this)' id="monthFirst">Mes</div>
                <div onclick='getHomeChart("trimester", this)'>Trimestre</div>
                <div onclick='getHomeChart("year", this)'>Ano</div>
            </div>
            <canvas id="HomeChart"></canvas>
        </div>
        <button>
            Saiba Mais
        </button>
    </div>
</div>

</div>
<script>

</script>
<script src="includes/home/home.js"></script>
<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM" . $_SERVER['REMOTE_ADDR']));
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(include("../../error/403.html"));
} else {
?>

    <div class="adminHomeContainer adminContainer">

        <div class="info infoAprovados">
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
        <div class="info infoNps">
            <div class="infoHeader">
                <div class="infoHeaderIcon">
                    <i class="fa-solid fa-star"></i>
                </div>
                <span>NPS</span>
            </div>
            <div class="infoContent">
                <b class="infoContentNumber"><b id="HomeInfoNps"></b><small>%</small></b>
                <p class="infoContentP ">Avaliação dos Clientes</p>
                <button class="infoContentButton" onclick="changePage('relatory');goToSearch('page=nps');"> Ver Relatórios </button>
            </div>
        </div>

        <div class="info infoEnvio">
            <div class="infoHeader">
                <div class="infoHeaderIcon">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <span>Aguardando Envio</span>
            </div>
            <div class="infoContent">
                <b class="infoContentNumber" id="HomeInfoAguardandoEnvio"></b>
                <p class="infoContentP">Pedidos Aguardando Envio</p>
                <button class="infoContentButton" onclick="goToSearch('tracking=1');changePage('purchases')"> Ver todos </button>
            </div>
        </div>
        <div class="info infoClients">
            <div class="infoHeader">
                <div class="infoHeaderIcon">
                    <i class="fa-solid fa-user-friends"></i>
                </div>
                <span>Clientes</span>
            </div>
            <div class="infoContent">
                <b class="infoContentNumber" id="HomeInfoClients"></b>
                <p class="infoContentP">+ <b></b>&nbspEsse Mês</p>
                <button class="infoContentButton" onclick="changePage('relatory');goToSearch('page=nps');"> Ver Relatórios </button>

            </div>
        </div>



        <div class="info infoAguardando">
            <div class="infoHeader">
                <div class="infoHeaderIcon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <span>Aguardando Pagamento</span>
            </div>
            <div class="infoContent">
                <b class="infoContentNumber" id="HomeInfoAguardandoPagamento"></b>
                <p class="infoContentP">Pedidos Aguardando Pagamento</p>
                <button class="infoContentButton" onclick="goToSearch('status=1');changePage('purchases')"> Ver todos </button>
            </div>
        </div>

        <div class="info infoNaoFinalizadas">
            <div class="infoHeader">
                <div class="infoHeaderIcon">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                </div>
                <span>Carrinho Abandonado</span>
            </div>
            <div class="infoContent">
                <b class="infoContentNumber" id="HomeInfoNaoFinalizados"></b>
                <p class="infoContentP">Pedidos Não Finalizados</p>
                <button class="infoContentButton" onclick="changePage('relatory');goToSearch('page=nps');"> Ver Relatórios </button>
            </div>
        </div>
        <div class="info infoVisitantes">
            <div class="infoHeader">
                <div class="infoHeaderIcon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <span>Visitantes</span>
            </div>
            <div class="infoContent">
                <b class="infoContentNumber" id="HomeInfoVisitas"></b>
                <p class="infoContentP ">Número de visitantes</p>
                <button class="infoContentButton" onclick="changePage('relatory');goToSearch('page=nps');"> Ver Relatórios </button>

            </div>
        </div>
        <div class="info infoCancelados">
            <div class="infoHeader">
                <div class="infoHeaderIcon">
                    <i class="fa-solid fa-times"></i>
                </div>
                <span>Pedidos Cancelados</span>
            </div>
            <div class="infoContent">
                <b class="infoContentNumber" id="HomeInfoCanceladas"></b>
                <p class="infoContentP">+ <span id="HomeInfoCanceling"></span> em andamento</p>
                <button class="infoContentButton" onclick="goToSearch('status=9');changePage('purchases')"> Ver todos </button>
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
            <button onclick="changePage('relatory');goToSearch('page=finances');">
                Ver Mais
            </button>
        </div>
    </div>

    </div>
    <script>

    </script>
    <script src="includes/home/home.js"></script>

<?php
}
die();

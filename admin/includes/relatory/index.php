<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM" . $_SERVER['REMOTE_ADDR']));
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])  || ($_SESSION['admin']) < 1) {
    die(include("../../error/403.html"));

} else {
?>

    <div class="adminRelatoryContainer adminContainer">
        <section class="relatoryMenu">
            <div class="relatoryMenuItem menuSelect" onclick="relelatoryMenu(this, 'finances')">
                <span>Financeiro</span>
            </div>
            <div class="relatoryMenuItem" onclick="relelatoryMenu(this, 'nps')">
                <span>NPS</span>
            </div>
            <div class="relatoryMenuItem" onclick="relelatoryMenu(this, 'orders')">
                <span>Pedidos</span>
            </div>
            <div class="relatoryMenuItem" onclick="relelatoryMenu(this, 'final')">
                <span>Fechamentos</span>
            </div>
        </section>
        <div class="relatoryDisplay">


            <div class="relatoryBox final">
                <div class="relatoryPurchaseList">
                    <div class="listTitle">
                        <span>
                            Relatórios de Fechamento Mensal
                        </span>
                    </div>
                    <div class="listHeader">
                        <span>Data</span>
                        <span>Mês Referencia</span>
                        <span>Faturamento</span>
                        <span>Custos</span>
                        <span>Devoluções</span>
                        <span>Lucro Mês</span>
                        <span>Taxa do Sistema (15%)</span>
                        <span>Lucro Final (15%)</span>
                        <span>Baixar Relatório</span>
                    </div>
                    <div class="relatoryPurchaseListContent" id="RelatoryFinalRelatoryList">


                    </div>
                </div>
            </div>

            <div class="relatoryBox orders">

                <div class="relatoryPurchaseList">
                    <div class="listTitle">
                        <span>
                            Pedidos Realizados Este Mês
                        </span>
                    </div>
                    <div class="listHeader">
                        <span>Id</span>
                        <span>data</span>
                        <span>status</span>
                        <span>Produtos</span>
                        <span>Total</span>
                        <span>Ver</span>
                    </div>
                    <div class="relatoryPurchaseListContent" id="RelatoryOrdersList">


                    </div>
                </div>
                <div class="ordersChart">
                    <canvas id="OrdersChart">

                    </canvas>
                </div>
            </div>

            <div class="relatoryBox nps">



                <div class="relatoryInfo cancelInfo">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-x"></i>
                    </div>
                    <span>Cancelamentos</span>
                    <div class="infoContentNumber" id="relatoryNpsCanceled">

                    </div>
                    <p class="infoContentP">
                        <small>Cancelamentos </small>
                        <b>
                            +<b id="relatoryNpsCanceling"></b> esse mês
                        </b>
                    </p>
                </div>
                <div class="relatoryInfo">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <span>Mailing</span>
                    <div class="infoContentNumber" id="relatoryNpsMailing">

                    </div>
                    <p class="infoContentP">
                        <small>Clientes cadastrados na lista de email.</small>
                        <b>
                            +<b id="relatoryNpsMonthMailing"></b> desde o ultimo mês
                        </b>
                    </p>
                </div>
                <div class="relatoryInfo npsInfo">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <span>NPS</span>
                    <div class="infoContentNumber" id="relatoryNpsNps">
                    </div>
                    <p class="infoContentP">
                        <small>Probabilidade de indicação pelo cliente</small>
                        <b>+<b id="relatoryNpsMonthNps"></b>% desde o último mês</b>
                    </p>
                </div>
                <div class="relatoryInfo clientInfo">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <span>Clientes</span>
                    <div class="infoContentNumber" id="relatoryNpsTotalClient">
                    </div>
                    <p class="infoContentP">
                        <small>Total de clientes cadastrados</small>
                        <b>+<b id="relatoryNpsClientMonth"></b> desde o último mês</b>
                    </p>
                </div>
                <div class="relatoryPurchaseList">
                    <div class="listTitle">
                        <span>
                            Compras canceladas este mês
                        </span>
                    </div>
                    <div class="listHeader">
                        <span>Id</span>
                        <span>data de compra</span>
                        <span>ultima atualização</span>
                        <span>Total</span>
                        <span>Frete</span>
                        <span>Ver</span>
                    </div>
                    <div class="relatoryPurchaseListContent" id="RelatoryNpsCanceledList">


                    </div>
                </div>
                <div class="genderChart">
                    <canvas id="GenderChart">

                    </canvas>
                </div>
                <div class="visitorsChart">
                    <canvas id="VisitorsChart">

                    </canvas>
                </div>

            </div>

            <div class="relatoryBox finances">

                <div class="relatoryPurchaseList">
                    <div class="listTitle">
                        <span>
                            Compras finalizadas este mês
                        </span>
                    </div>
                    <div class="listHeader">
                        <span>Id</span>
                        <span>data de compra</span>
                        <span>data de recebimento</span>
                        <span>Total</span>
                        <span>Custo</span>
                        <span>Frete</span>
                        <span>Lucro</span>
                        <span>Ver</span>
                    </div>
                    <div class="relatoryPurchaseListContent" id="RelatoryPurchaseList">


                    </div>
                </div>



                <div class="info relatoryInfo infoFatM">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-money-bill"></i>
                    </div>
                    <b class="infoContentNumber">R$<b id="relatoryMonthInvoicing"></b></b>
                    <span>Faturamento No Mês</span>
                    <p class="infoContentP">Faturamento total deste mês</p>
                </div>

                <div class="info relatoryInfo infoCredited">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                    </div>
                    <b class="infoContentNumber">R$<b id="relatoryMonthNet"></b></b>
                    <span>Lucro No Mês</span>
                    <p class="infoContentP">Lucro total de suas vendas, descontando todos os custos de venda</p>
                </div>

                <div class="info relatoryInfo infoFatTT">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-money-bills"></i>
                    </div>
                    <b class="infoContentNumber">R$<b id="relatoryTotalInvoicing"></b></b>
                    <span>Faturamento</span>
                    <p class="infoContentP">Faturamento Total</p>
                </div>

                <div class="info relatoryInfo infoMargin">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                    <b class="infoContentNumber"><b id="relatoryMargin"></b>%</b>
                    <span>Margem Média</span>
                    <p class="infoContentP">Margem líquida média de seus produtos</p>
                </div>



                <div class="info relatoryInfo infoTicket">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-basket-shopping"></i>

                    </div>
                    <b class="infoContentNumber">R$<b id="relatoryTicket"></b></b>
                    <span>Ticket Médio</span>
                    <p class="infoContentP">Média do valor das vendas</p>
                </div>


                <div class="info relatoryInfo infoPrice">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <b class="infoContentNumber">R$<b id="relatoryAvPrice"></b></b>
                    <span>Preço Médio</span>
                    <p class="infoContentP">Média do preço de produtos vendidos</p>
                </div>




                <div class="info relatoryInfo infoCost">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <b class="infoContentNumber">R$<b id="relatoryAvCost"></b></b>
                    <span>Custo Médio</span>
                    <p class="infoContentP">Custo médio de suas vendas incluindo taxas e reposição produtos</p>
                </div>




                <div class="info relatoryInfo infoFuture">
                    <div class="infoHeaderIcon">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <b class="infoContentNumber">R$<b id="relatoryFutureReceivments"></b></b>
                    <span>Recebimentos Futuros</span>
                    <p class="infoContentP">Pagamentos passados que serão creditados futuramente em sua conta PagSeguro</p>
                </div>

            </div>
        </div>
    </div>

    <script src="includes/relatory/relatory.js">

    </script>
    </body>

<?php
}
die();

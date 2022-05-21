<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
    <div class="reviewPurchasesContent adminContainer">

        <span class="adminTitle">Compras Não Finalizadas</span>
        <p class="adminDescricao">Nesta são mostradas os pedidos realizados em seu site mas que não chegaram ao checkout. Os produtos adicionados aos Pedidos ainda não voltaram ao seu estoque.
            Para isso, basta clicar no botão "Repor Produtos".
        </p>
        <button id="ReviewButton">
            Repor Produtos
        </button>
        <div class="reviewContainer">

            <span id="totalOrders">Total <b>0</b> Pedidos não Finalizados</span>
            <div class="contentHeader">
                <span>Data</span>
                <span>Cliente</span>
                <span>Qtd de Produtos</span>
                <span>Valor Total</span>
            </div>
            <div class="reviewContent" id="reviewList">
            </div>
        </div>
    </div>

    <script src="includes/reviewPurchases/reviewPurchases.js"></script>';

    die($content);
}

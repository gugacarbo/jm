<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
    <div class="adminCategoryContainer adminContainer">
        <span class="adminTitle">Categorias</span>
        <p class="adminDescricao">
            Nesta aba voce pode inserir, alterar e excluir categorias. Elas serão importantes pois estarão presentes nos filtros de pesquisa de produtors bem como estarão presentes na criação de Carroséis.
        </p>
        <div class="categoryContent">
            <div class="content" id="CatList">

            </div>
            <div class="chart">
                <span>Produtos Por Categoria</span>
                <canvas id="CategoryChart" width="100%" height="100%"></canvas>
            </div>
        </div>
    </div>

    <script src="includes/category/category.js"></script>';
    die($content);
}

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

    <div class="adminMaterialContainer adminContainer">
        <p class="adminDescricao">
            Nesta aba voce pode inserir, alterar e excluir materiais.
        </p>
        <div class="materialContent">
            <div class="content" id="MatList">

            </div>
            <div class="chart">
                <span>Produtos Por Material</span>
                <canvas id="MaterialChart" width="100%" height="100%"></canvas>
            </div>
        </div>
    </div>

    <script src="includes/material/material.js"></script>
<?php
}
die();

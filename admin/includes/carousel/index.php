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
    <div class="adminCarouselContainer adminContainer">
        <p class="adminDescricao">
            Nesta página você pode inserir, alterar e excluir os carrosséis que aparecerão na página inicial do site.
            <br><br>
            Pode-se ser inserido um carrosél de cada categoria.
        </p>
        <button id="OpenCaroselModal">
            Adicionar Carrossél
        </button>
        <div class="carouselContent">

            <div class="carouselList" id="CarouselList">
            </div>
        </div>



        <div class="modalSelectProds modal" id="AdminCarouselModal">
            <span id="closeCarouselModal">
                <i class="fa-solid fa-xmark"></i>
            </span>
            <div class="catSelect">
                <span class="catSelectTitle">Selecione a categoria</span>
                <select id="Categories">
                    <option value="0">Selecionar Categoria</option>
                </select>
            </div>
            <section>
                <label>
                    <input type="radio" name="type" value="auto" id="radioAuto" checked>
                    <i class="fa-solid fa-a"></i>
                    <span>Seleção Automática</span>
                </label>
                <label>
                    <span>Selecionar Por</span>
                    <select id="autoType">
                        <option value="price">Menores Preços</option>
                        <option value="promo">Em Promoção</option>
                    </select>
                </label>
            </section>
            <section>
                <label>
                    <input type="radio" name="type" value="id" id="radioId">
                    <i class="fa-solid fa-hand-pointer cantOpenList" id="selectProdsButton"></i>
                    <span>Selecionar Produtos Manualmente</span>
                    <span id="ProdAddCount" style="display: none;"><b></b> Produtos Selecionados</span>
                </label>

            </section>
            <small class="categoryAlert">Selecione uma categoria</small>
            <button id="addCarousel">Salvar</button>
            <div class="previewBox">
            </div>
        </div>
        <div class="prodList" id="modalProdList">

            <span id="closeProdList">
                <i class="fa-solid fa-check"></i>
            </span>
            <div id="prodList"></div>
        </div>
    </div>
    <script src="includes/carousel/carousel.js"></script>

<?php
}
die();

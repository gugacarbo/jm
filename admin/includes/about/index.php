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
    <div class="aboutAdminContainer adminContainer">
        <p class="adminDescricao">Nesta aba voce pode alterar o texto de apresentação do seu negócio, bem como inserir o código com a localização fisica do google maps e editar o banner presente na pagina.
            (Caso não queira usar o banner ou o mapa, basta deixar os campos em branco).<br>
            A area visivel está destacada no campo de texto abaixo em pontilhado
            <br>
            Extensão da imagem somente .jpg
        </p>
        <button id="SaveAdminAbout">Salvar</button>
        <div class="content">
            <textarea></textarea>
            <section>
                <div class="aboutSelectHeader">
                    <span>Banner Inferior</span>
                    <label class="hasCheckboxToggle">
                        <input type="checkbox" id="UseBottomBanner">
                        <h3></h3>
                    </label>
                </div>

                <div class="aboutSelectHeader">
                    <span>Google Maps</span>
                    <label class="hasCheckboxToggle">
                        <input type="checkbox" id="UseMaps">
                        <h3></h3>
                        <small>Adicione o link do google maps aqui</small>
                        <input type="text" name="map" id="EditMap" placeholder="Insira o link do mapa">
                    </label>
                    <i class="fas fa-trash-can deleteLink" id="DeleteMapLink"></i>

                </div>
            </section>

            <section>
                <div class="aboutImgBox" id="aboutImg">
                    <i class="fas fa-trash-can" id="DeleteAboutImage"></i>
                    <input type="file" name="imagem" id="AboutImageFile">
                    <img>
                </div>
                <div class="aboutMap" id="MapL">
                </div>
            </section>
        </div>
    </div>


    <script src="includes/about/about.js"></script>
<?php

}
die();

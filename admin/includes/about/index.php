<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
<div class="aboutAdminContainer adminContainer">
    <span class="adminTitle">Página Sobre</span>
    <p class="adminDescricao">Nesta aba voce pode alterar o texto de apresentação do seu negócio, bem como inserir o código com a localização fisica do google maps e editar o banner presente na pagina.
        (Caso não queira usar o banner ou o mapa, basta deixar os campos em branco).<br>
        A area visivel está destacada no campo de texto abaixo em pontilhado
    </p>
    <div class="content">
        <textarea></textarea>
        <div class="addImage">
            <i class="fas fa-trash-can" id="DeleteAboutImage"></i> 
            <input type="file" name="imagem" id="AboutImageFile">
            <img src="/about/aboutImage.jpg">
        </div>
        <label>
            <span>Link do Google Maps</span>
            <p>Adicione o link do google maps aqui</p>
            <input type="text" name="map" id="EditMap" placeholder="Insira o link do mapa">
            <button id="SaveAdminAbout">Salvar</button>
        </label>
    </div>
</div>


<script src="/admin/js/jquery-te-1.4.0.js"></script>
<link rel="stylesheet" href="/admin/js/jquery-te-1.4.0.css">
<script src="includes/about/about.js"></script>
';

    die($content);
}

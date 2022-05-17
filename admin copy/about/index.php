<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link type="text/css" rel="stylesheet" href="jquery-te-1.4.0.css">


    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../admin.css">
</head>
<body>
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
                <button id="save">Salvar</button>
            </label>
        </div>
    </div>


    <script src="jquery-te-1.4.0.js"></script>
    <script src="about.js"></script>
</body>

</html>
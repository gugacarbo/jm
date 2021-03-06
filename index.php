<?php
include "verifyVisitor.php";
$Visitor = new Visitante();
$Visitor->VerificaUsuario();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Jm Acessórios de Luxo</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">
    <meta name="viewport" 
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="css/main.css">

</head>

<body>
    <header>
    </header>

    <div class="content homeContent">
        <div class="Banner">
            <div id="BannerSlider" class="bannerShow">
            </div>
            <div role="tablist" class="dotsBanner"></div>
        </div>

        <div class="carousel" id="Carousel">
        </div>
        <div class="newsletter">
            <div class="newsletterContent">
                <span>Assine nossa Newsletter e fique por dentro de promoções e novidades!</span>
                <p>Para receber todas as nossas novidades e promoções
                    basta cadastrar seu melhor email ao lado.
                </p>
                <form>
                    <input id="NewsletterName" type="text" placeholder="Digite seu nome" required>
                    <input id="NewsletterEmail" type="email" placeholder="Digite seu email" required>
                    <button id="SendNewsletter">Enviar</button>
                    <small id="NewsletterErrorDisplay">Cadastrado com sucesso!</small>
                </form>
            </div>
        </div>
        <div class="contact" id="contactDiv">
            <span>Contato</span>
            <p>Entre em contato conosco para maiores informações</p>
            <form>
                <input type="text" placeholder="Digite Seu Nome" id="contactName" required>
                <input type="tel" placeholder="Digite Seu Telefone" id="contactPhone" required>
                <small>Mensagem</small>
                <textarea id="contactMessage" required></textarea>
                <button id="contactSend">Enviar</button>
            </form>
            <div class="contactImage">
                <img src="https://jbs.com.br/wp-content/uploads/2019/10/icon01_contato_jbs.png">
            </div>
        </div>
    </div>
    <footer>

    </footer>
    <script src="./main.js"></script>
</body>

</html>
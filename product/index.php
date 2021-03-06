<?php
include "../verifyVisitor.php";
$Visitor = new Visitante();
$Visitor->VerificaUsuario();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Jm Acessórios de Luxo</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">
    <link rel="stylesheet" href="../css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>

<body>
    <header>
    </header>
    <div class="productPage">
        <div class="galery">
            <div class="mainImg">
                <img src="" id="MainImage">
            </div>
            <div class="secImg">
            </div>
        </div>
        <div class="info">
            <span class="productName">Prod</span>
            <div class="productPrice">
                <span class="promo ifPromo">De R$</span>
                <span class="price">
                    <p class="ifPromo">Por &nbsp</p>
                    <p>R$</p><span id="intPrice"></span>
                    <p>,</p>
                    <p id="floatPrice"></p>
                </span>
            </div>
            <div class="payments">
                <img src="/img/pag_seguro.png">
            </div>
            <div class="buyBox">
                <button class="cartBtn">Adicionar ao carrinho</button>
                <button class="buyBtn">Comprar Agora</button>
                <div class="prodOptions" id="ProdOptions">
                </div>
            </div>
            <div class="shipping">
                <div class="shipCalc">
                    <input type="text" placeholder="00.000-000" id="cep" required>
                    <div class="calcBtn" id="calcShippingBtn">Calcular</div>
                    <small id="ShipError">Cep Inválido</small>
                </div>
                <div class="shipCost">
                    <div class="sHead preco">
                        <span>Envio</span>
                        <span class="sLocal" id="SLocal"></span>
                        <span>Receba em até</span>
                    </div>
                    <div class="preco">
                        <span>Pac:</span>
                        <span id="PrecoPac"></span>
                        <span id="PrazoPac"></span>
                    </div>
                    <div class="preco">
                        <span>Sedex:</span>
                        <span id="PrecoSedex"></span>
                        <span id="PrazoSedex"></span>
                    </div>
                    <div>
                        <ion-icon class="animatedIconShipping" name="cube-outline"></ion-icon>
                        <ion-icon name="ellipsis-horizontal-outline"></ion-icon>
                        <ion-icon class="animatedIconShipping" name="airplane-outline"></ion-icon>
                        <ion-icon name="ellipsis-horizontal-outline"></ion-icon>
                        <ion-icon class="animatedIconShipping" name="home-outline"></ion-icon>
                    </div>
                </div>
            </div>
            <div class="description">
                <p id="prodDesc"></p>
                <label>
                    <span>Material:</span>
                    <span id="material"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="sizeTable">
    </div>
    <footer>
    </footer>
    <script src="product.js"></script>
</body>

</html>
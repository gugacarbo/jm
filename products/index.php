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
    <title>Produtos - Jm Acessórios de Luxo</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" type="text/css" media="all" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/main.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>


</head>

<body>
    <header>

    </header>


    <div class="Banner">
        <div id="ProdsSlider" class="bannerShow">
        </div>
        <div role="tablist" class="dotsBanner"></div>
    </div>

    <div class="toggleFilter">
        <span>Produtos</span>
        <label id="toggleFilter">Filtros<i class="fas fa-filter"></i></label>
    </div>

    <div class="filterBox toggleFilterOff">
        <div class="filter" id="filterProducts">
            <div class="minMax">
                <label>
                    <span for="min_price">Min.</span>
                    <input type="number" min=0 max="4990" oninput="validity.valid||(value='0');" id="SearchMinVal" class="price-range-field" />
                    <span for="min_price">Max.</span>
                    <input type="number" min=0 max="5000" oninput="validity.valid||(value='5000');" id="SearchMaxVal" class="price-range-field" />
                    <div id="slider-range" class="" name="rangeInput"></div>
                </label>
            </div>
            <div>
                <span>Categoria</span>
                <select id="SearchCategory">
                    <option selected value="">
                        <div class="opt"> - </div>
                    </option>
                </select>
            </div>
            <div>
                <span>Ordenar por</span>
                <select id="SearchOrderBy">
                    <option selected value="price ASC">
                        <div class="opt"> - </div>
                    </option>
                    <option value="price ASC">
                        <div class="opt">Preço Cresc.</div>
                    </option>
                    <option value="price DESC">
                        <div class="opt">Preço Decr.</div>
                    </option>
                </select>
            </div>
            <div class="search" id="Search">
                <input type="text" id="SearchText" placeholder="Pesquise por produtos">
                <i class="fas fa-search"></i>
            </div>
        </div>

    </div>

    <div class="products">
        <div class="showProducts" id="ShowProducts">
        </div>
        <div class="loadingProducts">
            <i class="fa-solid fa-spinner"></i>
        </div>
    </div>

    <footer>

    </footer>
    <script src="price_range_script.js" type="text/javascript"></script>
    <script src="prods.js"></script>
</body>

</html>
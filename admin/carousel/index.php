<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link rel="stylesheet" href="carousel.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <span class="title">Carrosséis</span>
        <p class="descricao">
            Nesta página você pode inserir, alterar e excluir os carrosséis que aaparecerão na página inicial do site. <br>
            Pode-se ser inserido um carrosél de cada categoria.
        </p>
        <div class="content">

            <div class="carouselAdd">
                <section>
                    <label>Adicionar/Editar: </label>
                    <select id="Categories">
                        <option value="0">Selecionar Categoria</option>
                    </select>
                </section>

                <section>
                    <span>
                        <input type="radio" name="type" value="auto" id="radioAuto" checked>
                        Automático
                    </span>
                    <select id="autoType">
                        <option value="">Selecionar Por</option>
                        <option value="price">Menores Preços</option>
                        <option value="promo">Em Promoção</option>
                    </select>
                    <input type="radio" name="type" value="id" id="radioId">
                    <label>Selecionar Manual</label>
                    <i class="fa-solid fa-hand-pointer cantOpenList" id="selectProdsButton"></i>
                </section>
                <button id="addCarousel">Salvar</button>
                <div class="previewBox">

                </div>
                <div class="selectProds">
                    <span id="closeProdList">
                        <i class="far fa-times-circle"></i>
                    </span>
                    <form id="prodList">

                    </form>
                </div>
            </div>
            <div class="carouselList" id="CarouselList">
            </div>
        </div>
    </div>

    <script src="carousel.js"></script>
</body>

</html>
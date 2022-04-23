<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link rel="stylesheet" href="product.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <script src="/jquery.mask.js"></script>
</head>

<body>
    <div class="productAdd">
        <label>
            <input type="text" name="nome" id="NewProductName" required>
            <small>Nome</small>
        </label>
        <section>
            <label>
                <input type="text" name="preco" id="NewProductPrice" required>
                <small>Preço</small>
            </label>
            Por :
            <label>
                <input type="text" name="preco" id="NewProductPromo" required>
                <small>Preço Promo</small>
            </label>
        </section>
        <section>

            <label>
                <input type="text" name="preco" id="NewProductWeight" required>
                <small>Peso(?)</small>
            </label>
            <label>
                <input type="text" name="preco" id="NewProductCost" required>
                <small>Custo(?)</small>
            </label>
        </section>
        <label>
            <select id="NewProductCategory" >
                <option selected >Categoria</option>
            </select>
        </label>
        <label>
            <select  id="NewProductMaterial">
                <option selected >Material</option>
            </select>
        </label>
        <label>
            <textarea id="NewProductDescription"></textarea>
            <small>Descrição</small>
        </label>
        <div class="productOptions" id="OptionsList">

            <div class="item add">
                <input type="text" value="" id="newOptName">
                <input type="text" value="" id="newOptQuantity">
                <i class="fa-solid fa-plus" id="addOpt"></i>
            </div>
        </div>
        <div class="productImages">
            <div class="productMainImage addImage">
                <input type="file" name="imagem" id="NewProductFile1">
                <img src="../banner/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="NewProductImage1">
            </div>
            <div class="productSecImage">
                <div class="addImage">
                    <input type="file" name="imagem" id="NewProductFile2">
                    <img src="../banner/noImage.png">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="NewProductImage2">
                </div>
                <div class="addImage">
                    <input type="file" name="imagem" id="NewProductFile3">
                    <img src="../banner/noImage.png">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="NewProductImage3">
                </div>
                <div class="addImage">
                    <input type="file" name="imagem" id="NewProductFile4">
                    <img src="../banner/noImage.png">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="NewProductImage4">
                </div>
            </div>
        </div>

        <i id="save" class="fa-solid fa-floppy-disk"></i>


    </div>

    <script src="product.js"></script>
</body>

</html>
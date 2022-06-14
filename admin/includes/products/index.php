<?php
header("Content-Type: text/html; charset=utf-8");

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM" . $_SERVER['REMOTE_ADDR']));
    session_start();
}


if (!isset($_SESSION["user"]) || !isset($_SESSION["admin"])) {
    die(include("../../error/403.html"));

} else {
?>
    <div class="productsContainer adminContainer">
        <p class="adminDescricao">Nesta aba você pode inserir, alterar e excluir produtos</p>
        <div class="filterProducts">

            <span class="filterTitle">Pesquisar Produto</span>
            <label>
                <input type="text" id="textSearch">
                <i class="fas fa-search" id="btnSearch"></i>
            </label>
            <span id="totalProducts">Total <b></b> Produtos encontrados</span>
            <button onclick="modalProductShow()">
                Adicionar Produto
            </button>
        </div>
        <div class="displayProds">

            <div class="productsHeader">
                <label name="id" class="filterI selected">Id<i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="name" class="filterI ">Nome<i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="price" class="filterI">Preço <i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="promo" class="filterI">Em Promoção <i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="cat" class="filterI">Categoria <i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="qtd" class="filterI">Quantidade Em Estoque <i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="sold" class="filterI">Quantidade Vendida <i class="fa-solid fa-chevron-down">
                    </i></label>
                <label>Alterar/Visualizar</label>
                <label>Excluir</label>
            </div>

            <div class="products" id="productsList">


            </div>
            <div id="PageCounter" class="pageCounter">
            </div>
        </div>

        <div class="modalProduct" id="ModalProduct">
            <i class="fas fa-times closeMod" id="closeModal"></i>
            <span class="modalTitle">Cadastrar / Editar Produto <small>Nenhuma edição será feita até voce salvar </small></span>
            <div class="productAdd">
                <div class="productInfo">
                    <section>
                        <label>
                            <input type="text" name="nome" id="NewProductName" required>
                            <small>Nome</small>
                        </label>
                    </section>
                    <section>
                        <label>
                            <span class="rs">R$</span>

                            <input type="text" name="preco" id="NewProductCost" required>
                            <small>Custo</small>
                            <div class="help">
                                <i class="fas fa-question-circle"></i>

                                <div class="helpMessage">
                                    <p>
                                        O custo do seu produto é utilizado para calcular as margens de venda.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </section>


                    <section>
                        <label>
                            <select id="NewProductCategory">
                                <option selected value="0" name="0">Selecionar</option>
                            </select>
                            <small>
                                Categoria
                            </small>
                        </label>
                    </section>
                    <section>
                        <label>
                            <span class="rs">R$</span>
                            <input type="text" name="preco" id="NewProductPrice" required>
                            <small>Preço</small>
                            <div class="help">
                                <i class="fas fa-question-circle"></i>

                                <div class="helpMessage">
                                    <p>
                                        Insira o Preço Final do Produto
                                    </p>
                                </div>
                            </div>
                        </label>
                    </section>
                    <section>
                        <label>
                            <select id="NewProductMaterial">
                                <option selected name="0" value="0">Selecionar</option>
                            </select>
                            <small>
                                Material
                            </small>
                        </label>
                    </section>
                    <section>
                        <label>
                            <span class="rs">R$</span>

                            <input type="text" name="preco" id="NewProductPromo" required>
                            <small>Preço Promocional</small>
                            <div class="help">
                                <i class="fas fa-question-circle"></i>

                                <div class="helpMessage">
                                    <p>
                                        Caso o produto esteja em promoção, insira o preço promocional.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </section>


                    <section>
                        <label>
                            <input type="text" name="preco" id="NewProductWeight" required>
                            <small>Peso


                            </small>
                            <span class="kg">g
                            </span>
                            <div class="help">
                                <i class="fas fa-question-circle"></i>

                                <div class="helpMessage">
                                    <p>
                                        O peso (em gramas) é utilizado para o calculo de frete.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </section>
                    <section>
                        <label class="save">
                            <button id="saveProduct">
                                <span>Salvar</span>
                            </button>
                        </label>
                    </section>
                    <section>
                        <label>
                            <textarea id="NewProductDescription"></textarea>
                            <small>Descrição</small>
                        </label>
                    </section>
                    <section class="productOptions">
                        <span class="optionsHeader">Variações
                            <div class="help">
                                <i class="fas fa-question-circle"></i>

                                <div class="helpMessage">
                                    <p>
                                        Adicione as variações do seu produto (cores, tamanhos, etc.)
                                        Ao menos uma opção é obrigatória.
                                    </p>
                                </div>
                            </div>
                        </span>

                        <div id="OptionsList">

                        </div>
                        <div class="item add">
                            <span>Add. </span>
                            <input type="text" value="" id="newOptName" placeholder="Variação">
                            <input type="text" value="" id="newOptQuantity" placeholder="Qtd.">
                            <i class="fa-solid fa-plus" id="addOpt"></i>
                        </div>
                    </section>

                </div>

                <div class="productImages">
                    <div class="productMainImage addImage">
                        <input type="file" name="imagem" id="NewProductFile1">
                        <img src="img/noImage.png">
                        <i class="fa-solid fa-trash toDeleteList"></i>
                        <input type="hidden" id="NewProductImage1">
                    </div>
                    <div class="productSecImage">
                        <div class="addImage">
                            <input type="file" name="imagem" id="NewProductFile2">
                            <img src="img/noImage.png">
                            <i class="fa-solid fa-trash toDeleteList"></i>
                            <input type="hidden" id="NewProductImage2">
                        </div>
                        <div class="addImage">
                            <input type="file" name="imagem" id="NewProductFile3">
                            <img src="img/noImage.png">
                            <i class="fa-solid fa-trash toDeleteList"></i>
                            <input type="hidden" id="NewProductImage3">
                        </div>
                        <div class="addImage">
                            <input type="file" name="imagem" id="NewProductFile4">
                            <img src="img/noImage.png">
                            <i class="fa-solid fa-trash toDeleteList"></i>
                            <input type="hidden" id="NewProductImage4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="includes/products/product.js"></script>
    <script src="includes/products/products.js"></script>';
<?php
}
die();

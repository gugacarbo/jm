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
    <div class="purchasesShippingConfig adminContainer">
        <p class="adminDescricao">Nesta aba voce fazer a configuração para calculo de frete e frete grátis.
            <br>
            <b>Frete Grátis</b>
            <br>
            Voce pode configurar a disponibilidade de frete gratis por cidade ou por um estado inteiro.
            <br>
            <b>Cupons</b>
            <br>
            Os Cupons podem ser em porcentagem (Ex.: 15%) ou em valor (Ex.: R$ 10,00).
        </p>
        <div class="shipConfigContent">


            <div class="freteGratis box">
                <div>
                    <h4 class="freteGratisHeader">Frete Gratis</h4>
                    <label class="hasCheckboxToggle">
                        <input type="checkbox" id="freteGratisCheck">
                        <h3></h3>
                    </label>
                    <button id="SaveFreeShipConfig">
                        Salvar
                    </button>
                </div>
                <div class="selectFree">

                    <div class="allStates">
                        <span class="allfreelabel">Para Todo o Brasil</span>
                        <label class="hasCheckboxToggle">
                            <input type="checkbox" id="freeStateAll">
                            <h3></h3>
                        </label>
                    </div>

                    <div class="freeList">
                        <h3 class="listTitle">
                            Cidades
                            <div class="help helpDelete">
                                <i class="fas fa-trash-alt" onclick="deleteAllCity()"></i>
                                <div class="helpMessage">
                                    <p>
                                        Deleta todas as Cidades da lista de fréte grátis.
                                    </p>
                                </div>
                            </div>
                        </h3>
                        <div id="freeCity" class="list">
                        </div>
                    </div>

                    <div class="freeList">
                        <h3 class="listTitle">
                            Estados
                            <div class="help helpDelete">
                                <i class="fas fa-trash-alt" onclick="deleteAllState()"></i>
                                <div class="helpMessage">
                                    <p>
                                        Deleta todas os Estados da lista de fréte grátis.
                                    </p>
                                </div>
                            </div>
                        </h3>
                        <div id="freeState" class="list">
                        </div>
                        <span id="TextTodosEstados" style="display: none;">Todos os estados selecionados</span>
                    </div>

                    <div class="addF">
                        <span class="addTitle">Adicionar à lista de frete grátis</span>
                        <section>
                            <span class="addFTitle">Adicionar por Estado</span>
                            <input type="text" maxlength="2" id="addStateInput" placeholder="UF">
                            <i class="fa-solid fa-plus" id="addState"></i>
                            <label class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Adiciona um estado à lista de frete grátis.
                                    </p>
                                </div>
                            </label>
                        </section>

                        <section>
                            <span class="addFTitle">Adicionar por Cep</span>
                            <input type="text" maxlength="10" id="addCepInput" placeholder="88.888-888">
                            <i class="fa-solid fa-plus" id="addCep"></i>
                            <label class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Adiciona Cidade e Estado correspondentes à lista de frete grátis.
                                    </p>
                                </div>
                            </label>
                        </section>

                    </div>
                </div>

            </div>


            <div class="cupom box">
                <div class="boxTitle">
                    Cupons
                    <span id="newCupom">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </span>
                </div>
                <div class="cuponsList">
                    <div id="CuponsList">
                    </div>
                </div>

                <!--- // --->
                <div class="modalCupom" id="CupomModal">
                    <span id="closeModalCupom">X</span>

                    <div class="modalCupomShow">
                        <h3 class="modalCupomHeader" id="modalCupomHeader">Adicionar Cupom</h3>
                        <label class="modalCupomTicker">

                            <input type="text" placeholder="Código Do Cupom" id="NewCupomTicker">
                            <div class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Esse será o Código de uso do Cupom.
                                    </p>
                                </div>
                            </div>
                        </label>
                        <label class="modalCupomValue">
                            <input type="radio" name="cupomType" id="CupomAbsType">
                            <b>R</i><i class="fa-solid fa-dollar-sign"></i></b>
                            <input type="number" placeholder="Valor" max="50" id="NewCupomValue">
                            <input type="radio" name="cupomType" checked id="CupomRelType">
                            <b><i class="fa-solid fa-percent"></i></b>
                            <div class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Valor do cupom, podendo ser em Reais, ou em porcentagem.
                                    </p>
                                </div>
                            </div>
                        </label>
                        <label class="hasCheckboxToggle">
                            <span>Uso único</span>
                            <input type="checkbox" checked name="singleUseCumpom" id="singleUseCumpom">
                            <h3></h3>
                            <div class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Caso desativado, o cupom poderá ser utilizado mais de uma vez por cliente.
                                    </p>
                                </div>
                            </div>

                        </label>
                        <label class="loadingUsedCupom">
                            <input type="number" placeholder="Quantidade" id="NewCupomQuantity">
                        </label>
                        <button id="SaveCupom" class="inverted" data-id="">Salvar</button>
                    </div>
                </div>
            </div>
            <!--- // --->




        </div>

    </div>
    <script src="includes/configFree/configFree.js"></script>

<?php
}
die();

<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
';

    //die($content);
}
?>
<div class="purchasesShippingConfig adminContainer">
    <span class="adminTitle">Configurações de Frete</span>
    <p class="adminDescricao">Nesta aba voce fazer a configuração para calculo de frete e frete grátis.</p>
    <div class="shipConfigContent">
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




        <div class="freteGratis box">
            <div>
                <h4>Frete Gratis</h4>
                <label>
                    <input type="checkbox" id="freteGratisCheck">
                    <h4></h4>
                </label>
                <span class="allfreelabel">Para Todo o Brasil</span>
                <label>
                    <input type="checkbox" id="freeStateAll">
                    <h4></h4>
                </label>
            </div>

            <div class="selectFree">
                <div>
                    <h3>
                        <i class="fas fa-trash-alt" onclick="deleteAllCity()"></i>
                        Cidades
                    </h3>
                    <div id="freeCity">
                    </div>
                </div>
                <div>
                    <h3>
                        <i class="fas fa-trash-alt" onclick="deleteAllState()"></i>
                        Estados

                    </h3>
                    <div id="freeState">
                    </div>
                </div>

                <div class="addF">
                    <label>
                        <p>Adicionar por Estado</p>
                        <input type="text" maxlength="2" id="addStateInput" placeholder="Adicionar UF">
                        <i class="fa-solid fa-plus" id="addState"></i>
                        <section class="help">
                            <i class="fas fa-question-circle"></i>
                            <div class="helpMessage">
                                <p>
                                    Adiciona um estado à lista de frete grátiss
                                </p>
                            </div>
                        </section>
                    </label>
                    <label>

                        <p>Adicionar por Cep</p>
                        <input type="text" maxlength="10" id="addCepInput" placeholder="CEP">
                        <i class="fa-solid fa-plus" id="addCep"></i>
                        <section class="help">
                            <i class="fas fa-question-circle"></i>
                            <div class="helpMessage">
                                <p>
                                    Adiciona Cidade e Estado correspondentes à lista de frete grátiss
                                </p>
                            </div>
                        </section>
                    </label>
                </div>

            </div>
            <button id="SaveFreeShipConfig">
                Salvar
            </button>
        </div>
    </div>

</div>
<script src="includes/configFree/configFree.js"></script>
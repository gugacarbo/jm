<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
<div class="purchasesShippingConfig adminContainer">
    <span class="adminTitle">Configurações de Frete</span>
    <p class="adminDescricao">Nesta aba voce fazer a configuração para calculo de frete e frete grátis.</p>
    <div class="shipConfigContent">
        <div class="cupom box">
            <div class="boxTitle">
            Cupons
            </div>
            <div class="cuponsList">
             <div class="cuponsListHeader">
                <span>Cupom</span>
                <span>Valor</span>
                <span>Quantidade</span>
                <span>1a Compra</span>
                <span>Utilizado</span>
                <span>
                    Editar
                </span>

             </div>
            <div  id="CuponsList">

            </div>

            </div>
        </div>

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
    <script src="includes/configFree/configFree.js"></script>';

    die($content);
}
/*             <input type="text" id="addCityInput" placeholder="Adicionar Cidade">
                    <i class="fa-solid fa-plus" id="addCity"></i>
                    <section class="help">
                        <i class="fas fa-question-circle"></i>
                        <div class="helpMessage">
                            Adiciona uma cidade à lista de frete grátiss
                        </div>
                    </section> */
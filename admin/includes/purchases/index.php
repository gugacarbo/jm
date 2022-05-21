<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
    <div class="purchasesContainer adminContainer">

        <span class="adminTitle">Vendas</span>
        <p class="adminDescricao">Nesta aba você pode visualizar e controlar suas vendas.</p>
        <div class="purchasesContent">

            <div class="filterPurchases">

                <span class="filterTitle">Pesquisar por <b>nome do cliente</b> ou <b>código da compra</b></span>
                <label>
                    <input type="text" id="textSearch">
                    <i class="fas fa-search" id="btnSearch"></i>
                </label>
                <span id="totalPurchases">Total <b></b> Vendas encontradas</span>
            </div>
            <div class="purchasesHeader">
                <label name="id" class="filterI selected ">Id<i class="fa-solid fa-chevron-down up">
                    </i></label>
                <label name="buyDate" class="filterI">Data da Compra<i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="name" class="filterI ">Nome do Cliente<i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="price" class="filterI">Valor da Compra<i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="status" class="filterI">Status<i class="fa-solid fa-chevron-down">
                    </i></label>
                <label name="sended" class="filterI">Enviado
                    <i class="fa-solid fa-chevron-down">
                    </i></label>
                <label>Código da Compra</label>

                <label>Acessar</label>
            </div>

            <div class="purchases" id="purchasesList">


            </div>
            <div id="PageCounter" class="pageCounter">
            </div>
        </div>



        <div class="modalPurchase" id="ModalPurchase">
            <i class="fas fa-times" id="closeModalPurchase"></i>
            <section class="info">
                <h4>Informações do Cliente</h4>
                <span id="ClientName">Nome: <b>Gustavo</b></span>
                <span id="ClientEmail">Email: <b></b></span>
                <span id="ClientPhone">Telefone: <b></b></span>
                <span id="ClientCPF">CPF: <b></b></span>
                <span id="ClientBorndate">Data de Nasc. <b></b></span>
            </section>
            <section class="info">

                <h4>Informações de Entrega</h4>
                <span id="AddressCity">Cidade <b></b></span>
                <span id="AddressDistrict">Bairro: <b></b></span>
                <span id="AddressCep">CEP: <b></b></span>
                <span id="AddressStreet">Rua: <b></b></span>
                <span id="AddressComplement">Complemento: <b></b></span>
                <span id="ShippingType">PAC</span>
            </section>
            <section>
                <label>
                    <span>Status Da Compra</span>
                    <span id="StatusSelect"></span>
                    <!--

                        <select id="StatusSelect">
                        </select>
                        
                        <button>
                            Salvar
                        </button>
                    -->
                </label>
            </section>
            <section>
                <label>
                    <span>Código de Rastreio</span>
                    <input type="text" id="TrackingCode" placeholder="Código de Rastreio" maxlength="13">
                    <button id="addTrackingCode">
                        Salvar
                    </button>
                    <div class="help">
                        <i class="fas fa-question-circle"></i>
                        <div class="helpMessage">
                            <p>
                            Ao Salvar, um e-mail é enviado ao comprador com o código de rastreio.

                            </p>
                        </div>
                    </div>
                </label>
            </section>
            <section class="prods">
                <label class="prodsHeader">
                    <span>Produtos</span>
                    <span>Qtd</span>
                    <span>Valor Total</span>
                </label>
                <div class="productList" id="PurchaseProducts">

                </div>
            </section>
            <section class="info">

                <h4>Informações da Compra</h4>
                <span id="PurchaseDate">Data da Compra: <b></b></span>
                <span id="PurchaseLastUpdate">Ultima Atualização: <b></b></span>
                <span id="PurchaseStatus">Status da Compra: <b></b></span>
                <span id="PurchaseCode">Código da Compra: <a target="_blank"></a></span>
                <span id="PurchaseTotalAmount">Valor Total: R$ <b></b></span>
                <span id="PurchaseFeeAmount">Taxas: R$ <b></b></span>
                <span id="PurchaseDiscount">Desconto: R$ <b></b></span>
                <span id="PurchaseShippingPrice">Frete: R$ <b></b></span>
                <span id="PurchaseNetAmount">Lucro Liquido: R$ <b></b></span>
                <span id="PurchasePaymentMethod">Método de Pagamento: <b></b></span>
            </section>
        </div>
    </div>
    <script src="includes/purchases/purchases.js"></script>';

    die($content);
}

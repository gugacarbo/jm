<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 404)));
} else {
    //$content = '
?>
    <div class="adminConfigContainer adminContainer">
        <span class="adminTitle">Configurações</span>
        <p class="adminDescricao">
            Nesta aba voce pode configurar alguma funcionaliidades de seu e-commerce.
        </p>
        <div class="configContent">
            <div class="configBox box">
                <h1 class="boxTitle">Notificações e Email</h1>
                <div class="config">
                    <span>E-mail Principal: </span>
                    <input type="text" id="adminMail">
                    <section class="help">
                        <i class="fas fa-question-circle"></i>
                        <div class="helpMessage">
                            <p>
                                Seu Email Principal onde Receberá todas as Notificações.
                            </p>
                        </div>
                    </section>
                </div>
                <div class="config">
                    <span>E-mail para Contato: </span>
                    <input type="text" id="contactMail">
                    <section class="help">
                        <i class="fas fa-question-circle"></i>
                        <div class="helpMessage">
                            <p>
                                Configure um email para contato!
                            </p>
                        </div>
                    </section>
                </div>
                <div class="config">
                    <span>E-mail Automático: </span>
                    <input type="text" id="automaticMail">
                    <section class="help">
                        <i class="fas fa-question-circle"></i>
                        <div class="helpMessage">
                            <p>
                                E-mail Utilizado para envio de notificações aos clientes. (Obrigatório **@jmacessoriosdeluxo.com.br)
                            </p>
                        </div>
                    </section>
                </div>
                <div class="config pass">
                    <span>Senha do E-mail Automático: </span>
                    <input type="text" id="automaticPass" autocomplete="false" name="AutomaticPass">
                    <section class="help">
                        <i class="fas fa-question-circle"></i>
                        <div class="helpMessage">
                            <p>
                                Senha do E-mail Automático. Caso nao deseje alterar, basta deixar em branco
                            </p>
                        </div>
                    </section>
                </div>

                <div class="config">
                    <div class="configToggle">
                        <h2>Receber Notificações de:
                        </h2>
                        <div class="toggle">
                            <h4>Pedidos Realizados</h4>
                            <label>
                                <input type="checkbox" id="adminConfigSendNotification" value="1">
                                <h3></h3>
                            </label>
                            <section class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Ativa ou Desativa o Recebimenmto de Notificações de Pedidos Realizados. (Aguardando Pagamento)
                                    </p>
                                </div>
                            </section>
                        </div>

                        <div class="toggle">
                            <h4>Pagamento Em Análise</h4>
                            <label>
                                <input type="checkbox" id="adminConfigSendNotification" value="2">
                                <h3></h3>
                            </label>
                            <section class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Ativa ou Desativa o Recebimenmto de Notificações de Pagamentos Em Análise.
                                    </p>
                                </div>
                            </section>
                        </div>

                        <div class="toggle">
                            <h4>Pagamentos Aprovados</h4>
                            <label>
                                <input type="checkbox" id="adminConfigSendNotification" value="3">
                                <h3></h3>
                            </label>
                            <section class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Ativa ou Desativa o Recebimenmto de Notificações de Pedidos Pagamentos Aprovados.
                                    </p>
                                </div>
                            </section>
                        </div>

                        <div class="toggle">
                            <h4>Pedidos Finalizados</h4>
                            <label>
                                <input type="checkbox" id="adminConfigSendNotification" value="4">
                                <h3></h3>
                            </label>
                            <section class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Ativa ou Desativa o Recebimenmto de Notificações de Pedidos Finalizados.
                                        <br>
                                        Esta Notificação é enviada após o final do período de abertura de disputa do Cliente.
                                    </p>
                                </div>
                            </section>
                        </div>

                        <div class="toggle">
                            <h4>Pedidos Não Pagos</h4>
                            <label>
                                <input type="checkbox" id="adminConfigSendNotification" value="7">
                                <h3></h3>
                            </label>
                            <section class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Ativa ou Desativa o Recebimenmto de Notificações de Pedidos Cancelados por Falta de Pagamento do Boleto ou Recusa do Pagamento pelo cartao de crédito.
                                    </p>
                                </div>
                            </section>
                        </div>

                        <div class="toggle">
                            <h4>Cancelamentos</h4>
                            <label>
                                <input type="checkbox" id="adminConfigSendNotification" value="5">
                                <h3></h3>
                            </label>
                            <section class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Esta Notificação é enviada quando o Cliente solicita o cancelamento de um Pedido (Disputa).
                                    </p>
                                </div>
                            </section>
                        </div>

                        <div class="toggle">
                            <h4>Outras Notificações</h4>
                            <label>
                                <input type="checkbox" id="adminConfigSendNotification" value="9">
                                <h3></h3>
                            </label>
                            <section class="help">
                                <i class="fas fa-question-circle"></i>
                                <div class="helpMessage">
                                    <p>
                                        Ativa ou Desativa o Recebimenmto de Outras Notificações
                                    </p>
                                </div>
                            </section>
                        </div>


                    </div>
                </div>
                <button id="saveEmailConfig">
                    Salvar
                </button>
            </div>



            <!--//* Frete-->
            <div class="configBox box">
                <h1 class="boxTitle">Configurações de Frete</h1>

                <div class="config centerC">
                    <span>Cep Origem: </span>
                    <input type="text" id="cepOrigemFrete" placeholder="00000-000">
                    <section class="help">
                        <i class="fas fa-question-circle"></i>
                        <div class="helpMessage">
                            <p>
                                Insira o cep de origem para o calculo do frete.
                            </p>
                        </div>
                    </section>
                </div>
                <div class="config">

                    <div class="previewSize">
                        <div class="boxSizesImage">
                        </div>

                        <div class="shipSizes">
                            <label>
                                <span>Altura do Pacote: </span>
                                <input type="number" min="2" value="10" id="alturaFrete" max="105">
                                <b>cm</b>
                                <div class="help">
                                    <i class="fas fa-question-circle"></i>
                                    <div class="helpMessage">
                                        A altura deve ter entre 2cm e 105cm.
                                    </div>
                                </div>
                            </label>
                            <label>
                                <span>Largura do Pacote: </span>
                                <input type="number" min="11" value="10" id="larguraFrete" max="105">
                                <b>cm</b>

                                <div class="help">
                                    <i class="fas fa-question-circle"></i>
                                    <div class="helpMessage">
                                        A largura deve ter entre 11cm e 105cm.
                                    </div>
                                </div>
                            </label>
                            <label>
                                <span>Comprimento do Pacote: </span>
                                <input type="number" min="16" value="10" id="comprimentoFrete" max="105">
                                <b>cm</b>
                                <div class="help">
                                    <i class="fas fa-question-circle"></i>
                                    <div class="helpMessage">
                                        O comprimento deve ter entre 16cm e 105cm.
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                </div>
                <div class="config centerC">
                 
                        <span>Peso da Embalagem: </span>
                        <input type="number" min="0" step="1" value="0" max="500" id="aditionalWeight">
                        <b>g</b>
                        <div class="help">
                            <i class="fas fa-question-circle"></i>
                            <div class="helpMessage">
                                Adicione o Peso da Embalagem Utilizada Para Somar com o Peso do Produto Para Calculo de Frete
                            </div>
                        </div>
                    
                </div>
                <button id="saveShipingConfig">
                    Salvar
                </button>
            </div>

        </div>
    </div>


    <script src="includes/config/config.js"></script>';
<?php
    //die($content);
}

?>
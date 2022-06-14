<?php
header('Content-Type: application/json; charset=utf-8');

function getModelCancel($code)
{

    $subject = "Instruções de Cancelamento";
    $AltBody = "";
    $AltBody = "JM - Acessorios <br>";
    $AltBody .= "Instruções de Cancelamento";
    $AltBody .= "<br>Codigo do Pedido: " . $code;

    $serverName = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[SERVER_NAME]";

    $ContentMail = '

<div background="#f0f" style="display: flex; flex-direction:column; align-items: center;">
<body background="#f0f" style="display: flex; flex-direction:column; align-items: center;">
    <table style="border-spacing: 0px; width:600px;">
        <tr>
        <td bgcolor="#b66d76" valign="center" align="center">
        
        <img src="' . $serverName . '/img/Jm_Logo_Branco.png" width="300" alt="JM" style="display:block; margin: auto;">

        </td>
        </tr>
        <tr>
            <td max-width="600">
                <h1
                    style="text-align:center; font-family: Arial, Helvetica, sans-serif; color:#66262e; font-size: 15pt; margin: 10px 0">
                    Instrições de Cancelamento
                </h1>
            </td>
        </tr>
        <tr>
            <td max-width="600" bgcolor="#b66d76" style="padding: 8px 10px; color: #ffffff;" valign="center" align="center" color="#fff">
                <span color="#fff"
                    style="text-align: center; font-family: Arial, Helvetica, sans-serif;  font-size: 12pt; color: #fff; margin: 0 auto;">
                    Codigo do Pedido: 
                    ' . $code . '
                    </span>
            </td>
        </tr>
        <tr style="margin:0;">
            <td bgcolor="#b66d76" valign="center" align="center">
                <span color="white"
                    style="font-family: Arial, Helvetica, sans-serif;  text-align: center; color: #ffffff; width: 100%;">Visualize <a
                        href="' .
        $_SERVER['SERVER_NAME'] . "/checkStatus/?code=" . $code
        . '" style="font-weight: bold; color:#fff; ">Aqui</a> Seu Pedido
                </span>
            </td>
        </tr>
        <tr>
        <td bgcolor="#fff">
            <br>
                <h1 style="font-size: 15pt; text-align: center;">Que pena que você quer cancelar seu pedido!</h1>
                <br>
                <p  style="font-size: 14pt; text-align: center;">
                    Mas não se preocupe, vamos te ajudar a resolver seu problema.
                </p>
            <br>
            <br>
            <h2 style="font-size: 14pt; color: #aaa;">Primeiro Passo</h2>
            <p>
            Caso tenha acontecido algum problema no seu pedido, ou se voce desistiu da compra no período de 7 dias após a confirmação do pagamento,
            pedimos que entre em contato <a style="color: #b66d76; font-size: 13pt; font-weight: bold; " href="' . $serverName . '/#contactDiv"> Clicando Aqui</a> e informe o código do pedido e seu CPF
            que vamos lhe ajudar o mais rápido possivel.
            </p>
            <br>
            <br>
            <p> 
                Caso desejar, você ainda pode abrir uma disputa diretamente com o Pagseguro.
            </p>
            <h2 style="font-size: 12pt; color: #aaa;">Fiz a compra com uma conta do PagSeguro</h2>
                <ol>
                <li>
                Acesse sua conta PagBank PagSeguro via site*;
                </li>
                <li>
                No menu, vá até Minha Conta e selecione a opção Extrato de Transações;
                </li>
                <li>
                Localize o valor da compra realizada, verifique os dados e clique em Abrir Disputa;
                </li>
                <li>
                Preencha o formulário explicando detalhadamente o ocorrido e se aceita ou não reembolso parcial;
                </li>
                <li>
                Confirme clicando em Abrir Disputa.
                </li>
            </ol>
            <h2 style="font-size: 12pt; color: #aaa;">Fiz a compra com meu e-mail</h2>
            <p>
            Caso você tenha feito a compra utilizando somente seu e-mail, você precisará criar uma conta com o e-mail ultilizado e seguir os passos acima.
            </p>
        
            <a href="https://faq.pagseguro.uol.com.br/duvida/como-abrir-uma-disputa/115">Mais Informações</a>

        </td>
        <tr>
        <td height="40" bgcolor="#b66d76" color="#fff" style="text-align:center; ">
            <a style="color: #fff;" href="' . $serverName . '/">Home</a>
        </td>
    </tr>
    <tr>
        <td height="20" bgcolor="#b66d76" color="#fff" style="text-align:center; ">
            <a style="color: #fff;" href="' . $serverName . '/about">Sobre</a>
        </td>
    </tr>
    <tr>
        <td height="20" bgcolor="#b66d76" color="#fff" style="text-align:center; ">
            <a style="color: #fff;" href="' . $serverName . '/terms">Termos de Compra</a>
        </td>
    </tr>
    <tr>
        <td height="20" bgcolor="#b66d76" color="#fff" style="text-align:center; ">
            <a style="color: #fff;" href="' . $serverName . '/#contactDiv">Contato</a>
        </td>
    </tr>

    <tr>
        <td height="20" bgcolor="#b66d76" color="#fff" style="text-align:center; ">
            <a style="color: #fff;" href="' . $serverName . '/" style="text-align:center; padding: 5px 0;">
                <img src="' . $serverName . '/img/Jm_Logo_Branco.png" width="60" alt="JM"
                    style="display:block; margin: 10px auto;">

            </a>

            <a href="' . $serverName . '/" style="text-align:center; padding: 5px 0;">
                <img src="' . $serverName . '/img/scudero.png" width="30" alt="JM" style="display:block; margin: 20px auto;">

            </a>

        </td>
    </tr>
    <tr>
        <td  style="text-align:center;">
            <small  style="text-align:center; padding: 5px 0;">®Digital Scudero - Todos os direitos reservados - 2022</small>
        </td>
    </tr>
    <tr>
        <td height="50" bgcolor="#000" color="#fff">
            <h4
                style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #fff; background-color: #000; text-align:center;">
                Caso não tenha sido voce que realizou este pedido, por favor, entre em contato com o nosso atendimento.
                <br>
                <a href="' .  $serverName . '#contactDiv" style="color:#b66d76; text-decoration:none;">
                    Contato
                </a>
            </h4>
        </td>
    </tr>
</table>
    </div>
';
    return ((array('status' => 200, "content" => $ContentMail, "subject" => $subject, "AltBody" => $AltBody)));
}

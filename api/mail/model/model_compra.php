<?php
function getModel($notification)
{

    $statusS = "";
    $subject = "";
    $AltBody = "";
    $statusCode = $notification["status"];
    switch ($statusCode) {
        case "1":
            $statusS = "Pedido Realizado Com Sucesso! <br> Aguardando pagamento";
            $subject = "Pedido Realizado Com Sucesso! - Aguardando pagamento";
            break;
        case "2":
            $statusS = "Pagamento Em análise";
            $subject = "Pagamento Em análise";
            break;
        case "3":
            $statusS = "Pagamento Aprovado";
            $subject = "Pagamento Aprovado";
            break;
        case "4":

            return ((array("status" => "100", "message" => "Email Not Sended!")));
            $statusS = "Disponível";
            $subject = "Obrigado por comprar na nossa loja!";
            break;
        case "5":
            $statusS = "Uma disputa foi aberta para o pedido";
            $subject  = "Compra Em disputa";
            break;
        case "6":
            return ((array("status" => "100", "message" => "Email Not Sended!")));
            $statusS = "Devolvida";
            $subject = "Compra Devolvida";
            break;
        case "7":
            return ((array("status" => "100", "message" => "Email Not Sended!")));
            //- Remarketing
            $statusS = "Seu pagamento não foi concluído";
            $subject = "Compra Cancelada";
            break;
        case "8":
            $statusS = "Debitado";
            return ((array("status" => "100", "message" => "Email Not Sended!")));
            break;
        case "9":
            $statusS = "Retenção temporária";
            return ((array("status" => "100", "message" => "Email Not Sended!")));
            break;
        default:
            $statusS = "";
    }
    $items = $notification["itemCount"] > 1 ? $notification["items"]["item"] : [$notification["items"]["item"]];

    $totalPrice = 0;
    $shipping = $notification["shipping"];
    $AltBody = "JM - Acessorios <br>";
    $AltBody .= $subject;
    $AltBody .= "<br>Codigo do Pedido: " . $notification["code"];
    $serverName = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[SERVER_NAME]";

    $ContentMail = '

<div background="#f0f" style="display: flex; flex-direction:column; align-items: center;">
    <table style="border-spacing: 0px;">
        <tr>
            <td max-width="600"  bgcolor="#b66d76"  height="200">
            <img src="' . $serverName . '/img/Jm_Logo_Branco.png" width="300" alt="JM" style="display:block; margin: auto;">
            </td>
        </tr>
        <tr>
            <td max-width="600">
                <h1
                    style="text-align:center; font-family: Arial, Helvetica, sans-serif; color:#66262e; font-size: 15pt; margin: 10px 0">
                    ' .
        $statusS
        . '
                </h1>
            </td>
        </tr>
        <tr>
            <td max-width="600" bgcolor="#b66d76" style="padding: 8px 10px; color: #ffffff;" valign="center" align="center" color="#fff">
                <span color="#fff"
                    style="text-align: center; font-family: Arial, Helvetica, sans-serif;  font-size: 11pt; color: #fff; margin: 0 auto;">
                    Codigo do Pedido: 
                    ' . $notification["code"] . '
                    </span>
            </td>
        </tr>
        <tr style="margin:0;">
            <td bgcolor="#b66d76" valign="center" align="center">
                <span color="white"
                    style="font-family: Arial, Helvetica, sans-serif;  text-align: center; color: #ffffff; width: 100%;">Visualize <a
                        href="' .  $serverName . '/checkStatus/?code=' . $notification["code"]
        . '" style="font-weight: bold; color:#fff; ">Aqui</a> Seu Pedido
                </span>
            </td>
        </tr>' . '<tr><td>
        <h2 style="margin:25px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #333;">Data da Compra: ' . (date("d-m-Y", strtotime($notification["date"]))) . '</h2>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000;text-align:center;" >Comprador: ' . $notification["sender"]["name"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000;text-align:center;" >Email: ' . $notification["sender"]["email"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" ">Tel.:(' . $notification["sender"]["phone"]["areaCode"] . ')' . $notification["sender"]["phone"]["number"] . '</h4>
        <h2 style="margin:25px 0;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #333; text-align:center;">Endereço de envio</h2>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["postalCode"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["street"] . ', ' . $notification["shipping"]["address"]["number"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["complement"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["city"] . ' - ' . $notification["shipping"]["address"]["state"] . '</h4>
</td>
</tr>' . '
        
        <tr>
            <td>
       
                <div style="display:flex; width: 100%; justify-content: space-between;  border-bottom: 1px solid #b66d76; margin-top: 20px;">
            <h2 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 16pt; color: #333;">Produtos</h2></div>';



    //* Produtos *//
    $ContentMail .= "<table width='100%'>";

    foreach ($items as $item) {
        $totalPrice += floatval($item["amount"]) * floatval($item["quantity"]);
        $ContentMail .=
            "<tr>
                <td style='border-bottom:1px solid #b66d76;'>
                <h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76; width:100%;'>${item["description"]}</h4>
                <h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>${item["quantity"]} x R$ ${item["amount"]}</h4>
                </td>
                </tr>";
    }
    $ContentMail .= "</table>

    </tr>
    <tr>
        <td>
            <h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                Total em Produtos
            </h4>

            <h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                R$ ${totalPrice}
            </h4>        


            <h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                Frete
            </h4>
            <h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                R$ ${shipping["cost"]}
            </h4>        



            <h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                Desconto.
            </h4>
            <h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                R$ ${notification["discountAmount"]}
            </h4>        

            <h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                Total
            </h4>
            <h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #b66d76;'>
                R$ ${notification["grossAmount"]}
            </h4>
        </td>
    </tr>";




    $ContentMail .= '

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
                Caso não tenha sido voce que realizou esta compra, por favor, entre em contato com o nosso atendimento.
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
    return ((array('status' => 200, "content" => $ContentMail, "subject" => $subject, "AltBody" => $AltBody, "purchaseStatusCode" => $statusCode)));
}

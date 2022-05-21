<?php
header('Content-Type: application/json; charset=utf-8');

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
            $statusS = "Compra Realizada com Sucesso";
            $subject = "Compra Realizada com Sucesso";
            break;
            case "4":
                $statusS = "Disponível"; // ! compra finalizada
                $subject = "Obrigado por comprar na nossa loja!";
            //return ((array("status" => "403", "message" => "Email Not Sended!")));
            break;
        case "5":
            $statusS = "Uma disputa foi aberta para o pedido";
            $subject  = "Compra Em disputa";
            break;
        case "6":
            $statusS = "Devolvida";
            $subject = "Compra Devolvida";
            return((array("status" => "403", "message" => "Email Not Sended!")));
            break;
        case "7":
            $statusS = "Seu pagamento não foi aprovado";
            $subject = "Compra Cancelada";
            break;
        case "8":
            $statusS = "Debitado";
            return ((array("status" => "403", "message" => "Email Not Sended!")));
            break;
        case "9":
            $statusS = "Retenção temporária";
            return ((array("status" => "403", "message" => "Email Not Sended!")));
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



    $ContentMail = '
<html>
<body background="#f0f" style="display: flex; flex-direction:column; align-items: center;">
    <table style="border-spacing: 0px;">
        <tr>
            <td max-width="600" height="200"><img src="http://www.jmacessoriosdeluxo.com.br/mail/model/Logo.jpg" width="600" alt="JM"></td>
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
                    style="text-align: center; font-family: Arial, Helvetica, sans-serif;  font-size: 12pt; color: #fff; margin: 0 auto;">
                    Codigo do Pedido: 
                    ' . $notification["code"] . '
                    </span>
            </td>
        </tr>
        <tr style="margin:0;">
            <td bgcolor="#b66d76" valign="center" align="center">
                <span color="white"
                    style="font-family: Arial, Helvetica, sans-serif;  text-align: center; color: #ffffff; width: 100%;">Visualize <a
                        href="' .
        "http://jmacessoriosdeluxo.com.br/checkStatus/?code=" . $notification["code"]
        . '" style="font-weight: bold; color:#fff; ">Aqui</a> Seu Pedido
                </span>
            </td>
        </tr>' . '<tr><td>
        <h2 style="margin:25px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #333;">Data da Compra: ' . (date("d-m-Y", strtotime($notification["date"]))) . '</h2>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000;text-align:center;" >Comprador: ' . $notification["sender"]["name"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000;text-align:center;" >Email: ' . $notification["sender"]["email"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" ">Tel.:(' . $notification["sender"]["phone"]["areaCode"] . ')' . $notification["sender"]["phone"]["number"] . '</h4>
        <h2 style="margin:25px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #333; text-align:center;">Endereço de envio</h2>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["postalCode"] .'</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["street"] . ', ' . $notification["shipping"]["address"]["number"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["complement"] . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification["shipping"]["address"]["city"] . ' - ' . $notification["shipping"]["address"]["state"] . '</h4>
</td>
</tr>' . '
        
        <tr>
            <td>
       
                <div style="display:flex; width: 100%; justify-content: space-between;  border-bottom: 1px solid #b66d76; margin-top: 20px;">
            <h2 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 18pt; color: #333;">Itens</h2></div>';

    $ContentMail .= "<table width='100%'>";
    foreach ($items as $item) {
        //totalProds += parseFloat(item.amount) * parseFloat(item.quantity);
        $totalPrice += floatval($item["amount"]) * floatval($item["quantity"]);
        $ContentMail .=
            "<tr>
                <td style='border-bottom:1px solid #b66d76;'>
                <h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76; width:100%;'>${item["description"]}</h4>
                <h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>${item["quantity"]} x R$ ${item["amount"]}</h4>
                </td>
                </tr>";
    }
    $ContentMail .= "</table>";

    $ContentMail .= "</tr><tr><td>
<h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>Total em Produtos</h4>
<h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>R$ ${totalPrice}</h4>        


<h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>Frete</h4>
<h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>R$ ${shipping["cost"]}</h4>        

<h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>Desc.</h4>
<h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>R$ ${notification["discountAmount"]}</h4>        

<h4 style='margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>Total</h4>
<h4 style='margin:5px 0; text-align: right;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>R$ ${notification["grossAmount"]}</h4></td></tr>";




    $ContentMail .= '<tr>
            <td height="180" bgcolor="#b66d76" color="#000"></td>
        </tr>
    </table>
    </body>
</html>
';
    return ((array('status' => 200, "content" => $ContentMail, "subject" => $subject, "AltBody" => $AltBody, "purchaseStatusCode" => $statusCode)));
}

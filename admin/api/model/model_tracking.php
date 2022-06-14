<?php
header('Content-Type: application/json; charset=utf-8');

function getModel($load)
{
    $load = json_encode($load);
    $notification = json_decode($load);

    $subject = "Pedido Enviado! Jm - Acessórios de Luxo";
    $AltBody = "";

    $trackingCode = $notification->trackingCode;
    $totalPrice = 0;
    $shipping = $notification->shipping;
    $AltBody = "JM - Acessorios <br>";
    $AltBody .= $subject;
    $AltBody .= "<br>Codigo do Pedido: " . $notification->code;
    $AltBody .= "<br>PedidoEnviado " . $trackingCode;


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
                    Pedido Enviado!
                </h1>
            </td>
        </tr>
        <tr>
            <td max-width="600" bgcolor="#b66d76" style="padding: 8px 10px; color: #ffffff;" valign="center" align="center" color="#fff">
                <span color="#fff"
                    style="text-align: center; font-family: Arial, Helvetica, sans-serif;  font-size: 12pt; color: #fff; margin: 0 auto;">
                    Codigo do Pedido: 
                    ' . $notification->code . '
                    </span>
            </td>
        </tr>';
    $ContentMail .= "<tr><td>
        <h4 style='text-align:center; margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #b66d76;'>Código de Rastreio</h4>
        <h4 style='text-align:center; margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #333;'>$trackingCode</h4>
        </td>
        ";

    $ContentMail .= '<tr style="margin:0;">
            <td bgcolor="#b66d76" valign="center" align="center">
                <span color="white"
                    style="font-family: Arial, Helvetica, sans-serif;  text-align: center; color: #ffffff; width: 100%;">Visualize <a
                        href="' .
        "http://jmacessoriosdeluxo.com.br/checkStatus/?code=" . $notification->code
        . '" style="font-weight: bold; color:#fff; ">Aqui</a> Seu Pedido
                </span>
            </td>
        </tr>' . '<tr><td>
        <h2 style="margin:25px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #333;">Data da Compra: ' . (date("d-m-Y", strtotime($notification->date))) . '</h2>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000;text-align:center;" >Comprador: ' . $notification->sender->name . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000;text-align:center;" >Email: ' . $notification->sender->email . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" ">Tel.:(' . $notification->sender->phone->areaCode . ')' . $notification->sender->phone->number . '</h4>
        <h2 style="margin:25px 0;font-family: Arial, Helvetica, sans-serif; font-size: 12pt; color: #333; text-align:center;">Endereço de envio</h2>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification->shipping->address->postalCode . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification->shipping->address->street . ', ' . $notification->shipping->address->number . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification->shipping->address->complement . '</h4>
        <h4 style="margin:5px 0;font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; text-align:center;" >' . $notification->shipping->address->city . ' - ' . $notification->shipping->address->state . '</h4>
</td>
</tr>' . '
';

    $ContentMail .= '
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

<?php
header('Content-Type: application/json; charset=utf-8');

function getModelCancel($code)
{

    $subject = "Instruções de Cancelamento";
    $AltBody = "";
    $AltBody = "JM - Acessorios <br>";
    $AltBody .= "Instruções de Cancelamento";
    $AltBody .= "<br>Codigo do Pedido: " . $code;

    $ContentMail = '
<html>
<body background="#f0f" style="display: flex; flex-direction:column; align-items: center;">
    <table style="border-spacing: 0px; width:600px;">
        <tr>
            <td max-width="600" height="200"><img src="http://www.jmacessoriosdeluxo.com.br/mail/model/Logo.jpg" width="600" alt="JM"></td>
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
        "http://jmacessoriosdeluxo.com.br/checkStatus/?code=" . $code
        . '" style="font-weight: bold; color:#fff; ">Aqui</a> Seu Pedido
                </span>
            </td>
        </tr>
        <tr>
        <td bgcolor="#fff" valign="center" align="center">
            <br>
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            <br>
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            <br>
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            lorem ipsum dolor sit amet, consectetur adipiscing elit.
            <br>

        </td>
        <tr>
            <td height="180" bgcolor="#b66d76" color="#000"></td>
        </tr>
    </table>
    </body>
</html>
';
    return ((array('status' => 200, "content" => $ContentMail, "subject" => $subject, "AltBody" => $AltBody)));
}

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link rel="stylesheet" href="vendas.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>


</head>

<body>
    <div class="container">
        <table class="salesList" id="vendas">
            <tr>
                <th>Status</th>
                <th>Cliente</th>
                <th>Códio da Compra</th>
                <th>Valor Total</th>
                <th>Data de Compra</th>
                <th>-></th>
            </tr>

        </table>
        <div class="showSale">
            <!-- //  !  -->
            <div class="infoBox">
                <span>Cliente </span>
                <span id="clientName">&nbsp</span>
                <span id="clientEmail">&nbsp</span>
                <span id="clientPhone">&nbsp</span>
                <span id="clientCPF">&nbsp</span>
                <span id="clientDate">&nbsp</span>
            </div>
            <div class="infoBox">
                <span>Endereço</span>
                <span id="ShippingStreet">&nbsp</span>
                <span id="ShippingCEP">&nbsp</span>
                <span id="ShippingCity">&nbsp</span>
                <span id="ShippingComplement">&nbsp</span>

            </div>
            <div class="infoBox shipCode" style="width: 100%;">
            <label>Alterar Status</label>    
            <select>
                    <optgroup label="Alterar Status">
                    <option>0</option>
                </select>
                <button id="shippingCodeBtn">Alterar Status</button>
            </div>
            <div class="infoBox shipCode" style="width: 100%;">
                <input type="text" id="shippingCode" placeholder="Código de Rastreio">
                <button id="shippingCodeBtn">Enviar Código</button>
            </div>
            <div class="productList" id="prodList">
               
            </div>
  
            <div class="infoBox moreInfo">
                <span id="BuyDate">&nbsp</span>
                <span id="LastEventDate">&nbsp</span>
                <span id="BuyStatus">&nbsp</span>
                <span id="BuyCode">&nbsp</span>
                <span id="TotalAmount">&nbsp</span>
                <span id="FeeAmount">&nbsp</span>
                <span id="Discount">&nbsp</span>
                <span id="ShippingCost">&nbsp</span>
                <span id="NetAmount">&nbsp</span>
                <span id="ShippingType">&nbsp</span>
                <span id="PaymentMethod">&nbsp</span>
                <span id="PaymentLink">&nbsp</span>
            </div>
        </div>
    </div>
    <script src="vendas.js"></script>

</body>

</html>
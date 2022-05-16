<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="review.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../admin.css">
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="reviewPurchasesContent adminContainer">

        <span class="adminTitle">Compras Não Finalizadas</span>
        <p class="adminDescricao">Nesta são mostradas os pedidos realizados em seu site mas que não chegaram ao checkout. Os produtos adicionados aos Pedidos ainda não voltaram ao seu estoque.
            Para isso, basta clicar no botão "Revisar Produtos".
        </p>
        <button id="ReviewButton">
            Revisar Produtos
        </button>
        <div class="reviewContainer">

            <span id="totalOrders">Total <b></b> Pedidos não Finalizados</span>
            <div class="contentHeader">
                <span>Data</span>
                <span>Cliente</span>
                <span>Qtd de Produtos</span>
                <span>Valor Total</span>
            </div>
            <div class="reviewContent" id="reviewList">
            </div>
        </div>
    </div>




    <script>
        $("body").append($("<div class='adminHeader'>").load("../header.html"));
        $("body").append($("<div class='adminMenu'>").load("../menu.html"));

        $("#ReviewButton").click(function() {
            $.get("review.php", function(data) {
                data = JSON.parse(data);
                console.log(data);
                $("#reviewList").html("");
            })
        });

        $.get("getPurchases.php?get", function(data) {
            data = JSON.parse(data);
            $("#totalOrders b").html(data.length)
            $.each(data, function(index, value) {
                var buyer = JSON.parse(value['buyer']);
                var products = JSON.parse(value['products']);

                var now = Date.now();
                var date = new Date(value.buy_date);
                var int = parseInt((now - date.getTime()) / 1000 / 60 / 60 / 24) // Em dias;
                var dateString = date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear();


                $("#reviewList").append(`<div class="item">
                                        <span ${int > 3 ? "style='color:#f00;'" : ""}>${dateString} Há ${int} Dias</span>
                                        <span>${buyer.nome} ${buyer.sobrenome}</span>
                                        <span> ${products.length}</span>
                                        <span> ${value.totalValue}</span>
                                    </div>`);
            })
        })
    </script>
</body>

</html>
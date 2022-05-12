<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="review.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="content" id="Content">
    </div>
    <button id="ReviewButton">
        Reenviar Produtos Ao Estoque
    </button>
    <script>

        $("#ReviewButton").click(function() {
            $.get("review.php", function(data) {
                data = JSON.parse(data);
                console.log(data);
            })
        });
        
        $.get("getPurchases.php?get", function(data) {
            data = JSON.parse(data);
            console.log(data);
            $.each(data, function(index, value) {
                var buyer = JSON.parse(value['buyer']);
                var products = JSON.parse(value['products']);

                var now = Date.now();
                var date = new Date(value.buy_date);
                var int = parseInt((now - date.getTime()) / 1000 / 60 / 60 / 24) // Em dias;
                
                $("#Content").append(`<div>
                                        <span>Data da compra: ${value.buy_date}</span>
                                        <span ${int > 3 ? "style='color:#f00;'" : ""}> Há ${int} Dias</span>
                                        <span>Cliente: ${buyer.nome} ${buyer.sobrenome}</span>
                                        <span>Quantidade de Produtos ${products.length}</span>
                                    </div>`);
            })
        })
    </script>
</body>

</html>
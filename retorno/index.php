<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Realizada! - JM Acessórios de Luxo</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">
    <link rel="stylesheet" href="/css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="returnPage">
        <div class="returnContent">
            <h1>Compra Concluída com Sucesso!</h1>
            <p>Use esse código para rastrear sua compra</p>
            <span class="code"><?php echo ($_GET["code"]); ?></span>
            <i style="color: '#bbb'; font-size: 10pt;">Ele será enviado para seu e-mail</i>
            <div class="nps">
                <h2>Avalie Sua Compra!</h2>
                <div class="stars">
                    <input type="radio" name="nps1" id="nStar1" value="5">
                    <label for="nStar1">
                        <i class="fa-solid fa-star"></i>
                    </label>
                    <input type="radio" name="nps1" id="nStar2" value="4">
                    <label for="nStar2">
                        <i class="fa-solid fa-star"></i>
                    </label>
                    <input type="radio" name="nps1" id="nStar3" value="3">
                    <label for="nStar3">
                        <i class="fa-solid fa-star"></i>
                    </label>
                    <input type="radio" name="nps1" id="nStar4" value="2">
                    <label for="nStar4">
                        <i class="fa-solid fa-star"></i>
                    </label>
                    <input type="radio" name="nps1" id="nStar5" value="1">
                    <label for="nStar5">
                        <i class="fa-solid fa-star"></i>
                    </label>
                </div>
                <span id="rateMsg">&nbsp</span>
            </div>
            <span>Deixe uma Mensagem <i style="color: '#ddd'; font-size: 10pt;">opcional</i></span>
            <textarea></textarea>
            <button id="send">Enviar e Voltar a Loja</button>

        </div>
    </div>
    <script>
        $("input").on("change", () => {
            var rate = $("input[name='nps1']:checked").val();
            switch (rate) {
                case "1":
                    $("#rateMsg").html("Péssimo!");
                    break;
                case "2":
                    $("#rateMsg").html("Ruim!");
                    break;
                case "3":
                    $("#rateMsg").html("Bom!");
                    break;
                case "4":
                    $("#rateMsg").html("Muito Bom!");
                    break;
                case "5":
                    $("#rateMsg").html("Excelente!");
                    break;
            }
        })
        $("#send").on("click", () => {
            //$("#send").css("pointer-events", "none");
            var rate = $("input[name='nps1']:checked").val();
            var message = $("textarea").val();
            var code = "<?php echo ($_GET["code"]); ?>";
            $.post("/api/post/sendRating.php", {
                "rate": rate,
                "message": message,
                "code": code
            }, (data) => {
               window.location.href = "/";
            })
        })
    </script>

</body>

</html>
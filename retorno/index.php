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
            <div class="logo">
                <img src="/img/Jm_Logo_Branco.png" alt="Logo JM Acessórios de Luxo">
            </div>
            <h1>Compra Concluída com Sucesso!</h1>
            <p>Use esse código para rastrear sua compra</p>
            <span class="code">
                <input type="text" name="" id="CodeI" value=<?php echo '"' . ($_GET["code"]) . '"'; ?>>
                <i class="fas fa-copy" id="copyCode"></i></span>
            </span>
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
            <button id="send">Enviar e Voltar à Loja</button>
            <button id="noRate" style="opacity: 0;">Não Avaliar</button>

        </div>
    </div>
    <script>
        var confirmRate = 1;

        function copiarTexto() {
            let textoCopiado = $(".code").val();
            textoCopiado.select();
            textoCopiado.setSelectionRange(0, 99999)
            document.execCommand("copy");
            alert("O texto é: " + textoCopiado.value);
        }
        $('#copyCode').click(function() {
            $('#CodeI').select();
            var copiar = document.execCommand('copy');
            if (copiar) {
                $(this).css("transform", "scale(1.2)");
                var c = $(this).css("color");
                $(this).css("color", "green");
                setTimeout(() => {
                    $(this).css("color", c);
                    $(this).css("transform", "scale(1)");

                }, 500);
                //alert('Copiado');
            } else {
                //alert('Erro ao copiar, seu navegador pode não ter suporte a essa função.');
            }
            return false;
        });

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
        $("#noRate").on("click", () => {
            window.location.href = "/";
        })
        $("#send").on("click", () => {
            //$("#send").css("pointer-events", "none");
            var rate = $("input[name='nps1']:checked").val() || 0;
            var message = $("textarea").val();
            var code = "<?php echo ($_GET["code"]); ?>";

            if (rate <= 2 && confirmRate == 1) {
                var btxt = $("#send").html();
                confirmRate = 0;
                $("#noRate").css("transition", "1s");
                $("#noRate").css("opacity", "1");
                $("#send").html("Confirmar Nota?");
                setTimeout(() => {
                    $("#send").html(btxt);

                }, 2500);

                $(".fa-star").each(function() {
                    $(this).addClass("confirmRate");
                    setTimeout(() => {
                        $(this).removeClass("confirmRate");

                    }, 2500);
                });
                return;
            }

            $.post("/api/post/sendRating.php", {
                "rate": rate || 0,
                "message": message,
                "code": code
            }, (data) => {
                window.location.href = "/";
            })
        })
    </script>

</body>

</html>
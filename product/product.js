$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");

    $(".sImage img").on('click', function () {
        var src = $(this).attr('src')
        //console.log(src)
        $(".mainImg img").attr('src', src)
    })

    $('#cep').mask('00.000-000');

    $("#calcShippingBtn").on("click", () => {
        $(".animatedIconShipping").toggleClass("animateShipping");
        $(".preco").css("display", "none");

        setTimeout(() => {

            var config = {
                "nCdEmpresa": "08082650",
                "sDsSenha": "564321",
                "sCepOrigem": "70002900",
                "sCepDestino": "04547000",
                "nVlPeso": "1",
                "nCdFormato": "1",
                "nVlComprimento": "20",
                "nVlAltura": "20",
                "nVlLargura": "20",
                "sCdMaoPropria": "n",
                "nVlValorDeclarado": "0",
                "sCdAvisoRecebimento": "n",
                "nCdServico": "04510",
                "nVlDiametro": "0",
                "StrRetorno": "xml",
                "nIndicaCalculo": "3",
            }

            $.get("testfrete.json", (data) => {       //PAC
                $("#PrecoPac").html("R$ " + data['valor'])
                $("#PrazoPac").html(data['prazo'] + " Dias")
                $("#SLocal").html("cU DO MUNDO")
                $(".preco").css("display", "flex");


            })

            $(".animatedIconShipping").toggleClass("animateShipping");

        }, 1000)

    })
})
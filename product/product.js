$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");

    $.urlParam = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null) {
            return null;
        }
        else {
            return results[1] || 0;
        }
    }
    var id = 0;
    id = $.urlParam('id');

    if (!id) {
        window.location.replace("/")
    } else {
        getProd(id);
        
        $('#cep').mask('00.000-000');

        $("#calcShippingBtn").on("click", () => {
            $(".animatedIconShipping").addClass("animateShipping");
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

                $.get("/api/frete.json", (data) => {       //PAC
                    $("#PrecoPac").html("R$ " + data['vPac'].toFixed(2).replace('.', ','))
                    $("#PrazoPac").html(data['pPac'] + " Dias")
                    $("#PrecoSedex").html("R$ " + data['vSedex'].toFixed(2).replace('.', ','))
                    $("#PrazoSedex").html(data['pSedex'] + " Dias")
                    $("#SLocal").html(data['cidade'])
                    $(".preco").css("display", "flex");
                    $(".animatedIconShipping").removeClass("animateShipping");
                })
            }, 1000)

        })
    }

})

function getProd(id) {
    $.get("/api/getProdById.json", { id }, (prod) => {

        $("#MainImage").attr("src", prod["imgs"][1])
        $.each(prod["imgs"], function (i, img) {
            var i = '<div class="sImage"><img src="' + img + '" onclick="changeMain(\'' + img + '\')"></div>'
            $(".secImg").append(i)
        })
        $(".productName").text(prod["name"])
        if (prod["promo"]) {

            $(".ifPromo").css("display", "flex")
            $(".promo").text("De R$ " + prod['promo'].toFixed(2).replace('.', ','))
        } else {
            $(".ifPromo").css("display", "none")
        }
        const str = prod['price'].toString();
        const splitted = str.split('.');
        const intVal = parseInt(splitted[0]);
        const floatVal = parseInt(splitted[1] || 0);
        $("#intPrice").text(intVal)
        $("#floatPrice").text(floatVal || "00")
        $("#prodDesc").text(prod['desc'])
        $("#material").text(prod['material'])
        $("#size").text(prod['size'])
        $(".cartBtn").on('click', ()=>{
            alert("Cart" + prod['id'])
        })
        $(".buyBtn").on('click', ()=>{
            alert("buy" + prod['id'])
        })
    })
}

function changeMain(src) {
    console.log(src)
    $("#MainImage").attr('src', src)
}
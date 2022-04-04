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
            if ($("#cep").val().length < 10) {
                $(".preco").css("display", "none");
                $("#ShipError").css("display", "block")
                $("#ShipError").text("Cep Inválido")
            } else {
                $("#ShipError").css("display", "none")
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

                    $.get("/api/frete.json", (data) => {//PAC
                        if (data["error"]) {
                            $(".preco").css("display", "none");
                            $("#ShipError").css("display", "block")
                            $("#ShipError").text(data["error"])
                            $(".animatedIconShipping").removeClass("animateShipping");
                        } else {
                            $("#PrecoPac").html("R$ " + data['vPac'].toFixed(2).replace('.', ','))
                            $("#PrazoPac").html(data['pPac'] + " Dias")
                            $("#PrecoSedex").html("R$ " + data['vSedex'].toFixed(2).replace('.', ','))
                            $("#PrazoSedex").html(data['pSedex'] + " Dias")
                            $("#SLocal").html(data['cidade'])
                            $(".preco").css("display", "flex");
                            $(".animatedIconShipping").removeClass("animateShipping");
                        }
                    })
                }, 1000)
            }

        })
    }

})

function getProd(id) {
    $.get("/php/getProdById.php", { id }, (p) => {
        var prod = JSON.parse(p);
        prod.imgs = (JSON.parse(prod["imgs"]));
        prod.options = (JSON.parse(prod["options"]));
        //console.log(prod)
        $("#MainImage").attr("src", prod["imgs"][1])
        $.each(prod["imgs"], function (i, img) {
            var i = '<div class="sImage"><img src="' + img + '" onclick="changeMain(\'' + img + '\')"></div>'
            $(".secImg").append(i)
        })
        $(".productName").text(prod["name"])
        if (prod["promo"] > 0) {
            $(".ifPromo").css("display", "flex")
            $(".promo").text("De R$ " + prod['promo'].replace('.', ','))
        } else {
            $(".ifPromo").css("display", "none")
        }
        const str = prod['price'].toString();
        const splitted = str.split('.');
        const intVal = parseInt(splitted[0]);
        const floatVal = parseInt(splitted[1] || 0);
        $("#intPrice").text(intVal)
        $("#floatPrice").text(floatVal || "00")
        $("#prodDesc").text(prod['description'])
        $("#material").text(prod['material'])
        $("#size").text(prod['size'])
        

        $(".cartBtn").on('click', () => {
            addCart(parseInt((prod['id'])), 1, $("input[name='prodVar']:checked").val())
        })
        $(".buyBtn").on('click', () => {
            addCart(parseInt((prod['id'])), 1, $("input[name='prodVar']:checked").val())

            window.location.replace("/cart")

        })
        for(opt in prod['options']){
            var o ='<label><input type="radio" name="prodVar" '+(prod['options'][opt] == 0 ? "disabled" : "")+' value="'+opt+'"><span>'+opt+'</span><small>'+prod['options'][opt]+' Unidades Disponíveis</small></label>';
            $("#ProdOptions").append(o);
           
        }
        $("input[name='prodVar']:first-child").attr('checked', true);
    })
}

function changeMain(src) {
    $("#MainImage").attr('src', src)
}
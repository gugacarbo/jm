$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");



    var id = 0;
    id = $.urlParam('id');

    if (!id) {
        window.location.replace("/")
    } else {
        getProd(id);

        $('#cep').mask('00.000-000');

        $("#calcShippingBtn").on("click", () => {
            takeShipping();

        })
    }

})

function takeShipping() {
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
                "sCepDestino": ($("#cep").val().replace("-", "").replace(".", "")),
                "nVlPeso": itemWeight, //Peso + Embalagem Kg
            }

            $.get("/php/frete.php", config, (d) => {//PAC    
                var data = JSON.parse(d);
                data = JSON.parse(data);
                if (data["erro"][0] > 0 || data["erro2"][0] > 0) {
                    $(".preco").css("display", "none");
                    $("#ShipError").css("display", "block")
                    $("#ShipError").text("Erro, Tente Novamente Mais Tarde")// data["error"])
                    $(".animatedIconShipping").removeClass("animateShipping");
                } else {
                    $("#PrecoPac").html("R$ " + parseFloat(data['valorPac'][0]).toFixed(2).replace('.', ','))
                    $("#PrazoPac").html(data['prazoPac'][0] + " Dias")
                    $("#PrecoSedex").html("R$ " + parseFloat(data['valorSedex'][0]).toFixed(2).replace('.', ','))
                    $("#PrazoSedex").html(data['prazoSedex'][0] + " Dias")
                    $("#SLocal").html((Object.keys(data['cidade']["bairro"]).length === 0 ? "" : data['cidade']["bairro"]) + " - " + data['cidade']["cidade"] + " - " + data['cidade']["uf"]);
                    $(".preco").css("display", "flex");
                    $(".animatedIconShipping").removeClass("animateShipping");
                }
            })
        }, 1000)
    }
}

function changeMain(src) {
    $("#MainImage").attr('src', src)
}


var itemWeight;
function getProd(id) {
    $.get("/php/getProdById.php", { id }, (p) => {
        var prod = JSON.parse(p);
        prod.imgs = (JSON.parse(prod["imgs"]));
        prod.options = (JSON.parse(prod["options"]));
        itemWeight = prod.weight;
        $("#MainImage").attr("src", prod["imgs"][1])
        $.each(prod["imgs"], function (i, img) {
            var i = '<div class="sImage"><img src="' + img + '" onclick="changeMain(\'' + img + '\')"></div>'
            $(".secImg").append(i)
        })
        var str;
        $(".productName").text(prod["name"])
        if (prod["promo"] > 0) {
            $(".ifPromo").css("display", "flex")
            $(".promo").text("De R$ " + parseFloat((prod['promo'])).toFixed(2).replace('.', ','))
            str = prod['price'].toString();
        } else {
            $(".ifPromo").css("display", "none")
            str = prod['price'].toString();
        }
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
        for (opt in prod['options']) {
            var o = '<label><input type="radio" name="prodVar" ' + (prod['options'][opt] == 0 ? "disabled" : "checked='checked'") + ' value="' + opt + '"><span>' + opt + '</span><small>' + prod['options'][opt] + ' Unidades Disponíveis</small></label>';
            $("#ProdOptions").append(o);

        }

    })
}



$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    }
    else {
        return results[1] || 0;
    }
}
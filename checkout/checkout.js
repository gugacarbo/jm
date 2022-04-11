$("header").load("/includes/header.html");
$("footer").load("/includes/footer.html");

var totalPrice = 0;
var totalWeight = 0;
var totalItens = 0;
var buyerData;
var shipData;
var TotalData = {};

$(document).ready(_ => {
    getData();
    callCart();

    takeShipping().then((value) => {
        shipData = JSON.parse(value);
        shipData = JSON.parse(shipData)
        if (shipData.erro > 0 || shipData.erro2 > 0) {
            window.location.href = "/";
        }
        $("#waitingShipping").css("display", "none");
    });

    $('input.sinput').on('input', function (e) {
        var shipping = $(this).val();
        var shippingPrice = 0;
        if (shipping == "PAC") {
            shippingPrice = shipData.valorPac[0];
            shipData.price = shipData.valorPac[0];
            shipData.selected = "PAC";
        } else if (shipping == "SEDEX") {
            shippingPrice = shipData.valorSedex[0];
            shipData.price = shipData.valorSedex[0];
            shipData.selected = "SEDEX";
        }
        $('#totalPrice').html("R$" + (parseFloat(totalPrice) + parseFloat(shippingPrice)).toFixed(2).replace(".", ","));
        $("#checkoutButton").removeClass("off")
    });

    $("#checkoutButton").on("click", () => {
        $("#checkoutButton").addClass("off")
        $("#redirectM div").fadeOut(1);
        TotalData.buyer = buyerData;
        TotalData.ship = shipData;
        TotalData.cart = JSON.parse(localStorage.getItem("JM_CART"));

        $.ajax({
            url: "/php/updateProdQuantity.php",
            type: "GET",
            data: {
                cart: TotalData.cart
            },
            success: function (data) {
                data = JSON.parse(data);
                console.log(data)
                if (data["status"] == "sucess") {
                    $.ajax({
                        url: "/php/checkout.php",
                        type: "GET",
                        data: {
                            buyer: TotalData.buyer,
                            ship: TotalData.ship,
                            cart: TotalData.cart
                        },
                        success: function (data) {
                            data = JSON.parse(data);
                            console.log(data);
                            if (data["status"] == "sucess") {
                                $("body").toggleClass("blockBody");
                                $("#redirectM div .fa-circle-xmark").css("display", "none");
                                $("#redirectM").css("opacity", "1");
                                $("#redirectM").css("display", "flex");
                                $("#redirectM div").fadeIn(1000);

                                deleteList(); // !
                                setTimeout(() => {
                                    window.location.href = data["url"];
                                }, 1500);
                            } else {

                                $("body").toggleClass("blockBody");
                                $("#redirectM div .fa-circle-xmark").css("background-color", "#f00");
                                $("#redirectM div .fa-circle-check").css("display", "none");
                                $("#redirectM div h1").html("Ocorreu um erro!");
                                $("#redirectM div p").html("Tente novamente mais tarde!");
                                $("#redirectM").css("opacity", "1");
                                $("#redirectM").css("display", "flex");
                                $("#redirectM div").fadeIn(1000);
                                setTimeout(() => {
                                    window.location.href = "/";
                                }, 1500);
                            }
                        }
                    });

                } else {
                    $("body").toggleClass("blockBody");
                    $("#redirectM div .fa-circle-xmark").css("background-color", "#f00");
                    $("#redirectM div .fa-circle-check").css("display", "none");
                    $("#redirectM div h1").html("Ocorreu um erro!");
                    $("#redirectM div p").html("Tente novamente mais tarde!");
                    $("#redirectM").css("display", "flex");
                    $("#redirectM").css("opacity", "1");
                    $("#redirectM div").fadeIn(1000);
                    setTimeout(() => {
                        window.location.href = "/";
                    }, 1500);
                }
            }
        });

    })
})


function getData() {

    var url = new URL(window.location.href);
    buyerData = {
        'nome': url.searchParams.get("nome"),
        'sobrenome': url.searchParams.get("sobrenome"),
        'email': url.searchParams.get("email"),
        'telefone': url.searchParams.get("telefone"),
        'cpf': url.searchParams.get("cpf"),
        'nascimento': url.searchParams.get("nascimento"),
        'cep': url.searchParams.get("cep"),
        'rua': url.searchParams.get("rua"),
        'bairro': url.searchParams.get("bairro"),
        'UF': url.searchParams.get("UF"),
        'cidade': url.searchParams.get("cidade"),
        'numero': url.searchParams.get("numero"),
        'complemento': url.searchParams.get("complemento")
    }

    $("#BuyerName").html(buyerData.nome + " " + buyerData.sobrenome);
    $("#BuyerDate").html(buyerData.nascimento);
    $("#BuyerCPF").html(buyerData.cpf);
    $("#BuyerPhone").html(buyerData.telefone);
    $("#BuyerEmail").html(buyerData.email);
    $("#BuyerAddress").html(buyerData.rua + ", " + buyerData.numero);
    $("#BuyerCidade").html(buyerData.cidade + " - " + buyerData.UF);
    $("#BuyerBairro").html(buyerData.bairro);
    $("#BuyerCEP").html(buyerData.cep);
    $("#BuyerComplemento").html(buyerData.complemento);
}



async function callCart() {

    var cart_ = localStorage.getItem("JM_CART");
    if (cart_) {
        cart_ = JSON.parse(cart_);
    } else {
        window.location.href = "/";
    }

    if (Object.keys(cart_).length == 0) {
        window.location.href = "/";
    }

    $("#CartProds").html("");
    var w;
    $.each(cart_, (p, item) => {
        var px = $.ajax({
            url: "/php/getProdById.php?id=" + cart_[p].id,
            method: "GET",
            success: async function (l) {
                var prod = JSON.parse(l);
                prod.imgs = (JSON.parse(prod["imgs"]));
                prod.options = (JSON.parse(prod["options"]));

                var checkoutProd = '<div class="product">' +
                    '<a class="pImage" href="/product/?id=' + cart_[p].id + '">' +
                    '<img src="' + prod['imgs'][1] + '" alt="">' +
                    '</a>' +
                    '<div class="pInfo">' +
                    '<span class="pName">' + prod['name'] + '</span>' +
                    (prod['options'][cart_[p].opt] > 0 ? "<span class='pAvailable'>Em Estoque" : "<span class='pAvailable' style='color:#922'>Indisponível") + '</span>' +
                    '<div class="pQuantity">Qtd.: ' +
                    '<span>' +
                    cart_[p].qtd +
                    '</span>' +
                    '</div>' +
                    '<span class="vari">Variação ' + cart_[p].opt + '</span>' +
                    '</div>' +
                    '<div class="pPrice">' +
                    '<span>R$ ' + (parseFloat((prod['price']) * cart_[p].qtd).toFixed(2)).replace(".", ",") + "" + '</span>'
                    + '</div></div>';
                $("#ckeckoutProducts").append(checkoutProd);
                return await prod;
            }
        })
        px.then((value) => {
            var v = JSON.parse(value)
            w = parseFloat(v.weight) * parseInt(cart_[p].qtd);
            totalPrice += (parseFloat((parseFloat(v.price) * cart_[p].qtd).toFixed(2)));
            totalItens += parseInt(cart_[p].qtd);
            $("#subTotalPrice").html("Subtotal (" + totalItens + " Itens) : <b>R$ " + totalPrice.toFixed(2).replace(".", ",") + "<b>")
            totalWeight += w;
            return w;
        })
    })
}



async function takeShipping() {
    var config = {
        "sCepDestino": buyerData.cep,
        "nVlPeso": 0.25
    }

    var ship = $.get("/php/frete.php", config, (d) => {
        var data = JSON.parse(d);
        data = JSON.parse(data);
        //console.log(data)

        if (data["erro"][0] > 0 || data["erro2"][0] > 0) {
            window.location.href("/")
        } else {
            $("#prazoPac").html("PAC: " + data['prazoPac'][0] + " Dias")
            $("#prazoSedex").html("Sedex: " + data['prazoSedex'][0] + " Dias")
            $("#valorPac").html("R$ " + (data['valorPac'][0]))
            $("#valorSedex").html("R$ " + (data['valorSedex'][0]))
        }
    })
    return await ship;
}

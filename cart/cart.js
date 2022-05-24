var cart_;
$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");


    callCart();

})



function delProd(id, opt) {
    addCart(parseInt(id), 0, opt);
    setTimeout(() => {
        $("#CartProds").html("");
        callCart();
    }, 200);
}



async function callCart() {
    var totalPrice = 0;
    var totalItens = 0;
    var invalidProd = 0;

    cart_ = localStorage.getItem("JM_CART");
    if (cart_) {
        cart_ = JSON.parse(cart_);
    } else {
        cart_ = []
    }
    $("#CartProds").html("");



    const promises = cart_.map(async item => {
        const d = await $.ajax({
            url: "/api/get/getProdById.php?id=" + item.id,
            method: "GET",
        })
        return d
    })

    const prods = await Promise.all(promises)
    $.each(prods, (i, item) => {


        if ((item['options'][cart_[i].opt] == 0) || (!parseInt(item['options'][cart_[i].opt]))) {
            invalidProd = 1;
        }

        var cartProd = '<div class="cartP">' +
            '<a class="pImage" href="/product/?id=' + cart_[i].id + '">' +
            '<img src="' + item['imgs'][1] + '" alt="">' +
            '</a>' +
            '<div class="pInfo">' +
            '<span class="pName">' + item['name'] + '</span>' +
            (item['options'][cart_[i].opt] > 0 ? "<span class='pAvailable'>Em Estoque" : "<span class='pAvailable' style='color:#922'>Indisponível") + '</span>' +

            '<div class="pQuantity"><span>Qtd.:</span>' +

            '<select onchange="changeQtd(this,' + cart_[i].id + ',' + "'" + [cart_[i].opt] + "'" + ')">  ';
        (item['options'][cart_[i].opt] == 0) ? $("#goForm").addClass("off") : "";

        for (var x = 0; x < item['options'][cart_[i].opt]; x++) {
            cartProd += "<option value='" + (x + 1) + "'" + ((x + 1) == cart_[i].qtd ? "selected" : "") + ">" + (x + 1) + "</option>"
        }

        cartProd += '</select>' +

            '<span class="delProd" onclick="delProd(' + (cart_[i].id + ",\'" + [cart_[i].opt]) + '\')">Excluir</span>' +
            '</div>' +
            '<span class="vari">Variação: ' + cart_[i].opt + '</span>' +
            '</div>' +
            '<div class="pPrice">' +
            '<span>R$ ' + ((parseFloat(item['price'])).toFixed(2)).replace(".", ",") + '</span>' +

            '</div></div>';
        $("#CartProds").append(cartProd);
        if (invalidProd == 1) {
            $("#goForm").addClass("off")
        } else {
            $("#goForm").removeClass("off")
        }
        totalPrice += (parseFloat((parseFloat(item.price) * cart_[i].qtd)));
        totalItens += parseInt(cart_[i].qtd);

        $("#totalPrice").html("Subtotal (" + totalItens + " Itens) : R$ " + totalPrice.toFixed(2).replace(".", ","))
    })


    if (cart_.length == 0) {
        $(".cartButtons").css("display", "none");
        $("#totalPrice").html("");
        $("#CartProds").append("<div class='noProds'><span>Carrinho de Compras Vazio</span><i class='fa-solid fa-cart-shopping'></i></div>");
    } else {
        $(".chechoutBtns").css("display", "flex");
    }
}

function changeQtd(select, id, opt) {
    addCart(id, $(select).val(), opt, 0);
    callCart();
}


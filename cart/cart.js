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


    $.each(cart_, async (p, item) => {

         $.ajax({
            url: "/api/get/getProdById.php?id=" + cart_[p].id,
            method: "GET",
            success:  function (prod) {


                if ((prod['options'][cart_[p].opt] == 0) || (!parseInt(prod['options'][cart_[p].opt]))) {
                    invalidProd = 1;
                }

                var cartProd = '<div class="cartP">' +
                    '<a class="pImage" href="/product/?id=' + cart_[p].id + '">' +
                    '<img src="' + prod['imgs'][1] + '" alt="">' +
                    '</a>' +
                    '<div class="pInfo">' +
                    '<span class="pName">' + prod['name'] + '</span>' +
                    (prod['options'][cart_[p].opt] > 0 ? "<span class='pAvailable'>Em Estoque" : "<span class='pAvailable' style='color:#922'>Indisponível") + '</span>' +
                
                    '<div class="pQuantity"><span>Qtd.:</span>' +

                    '<select onchange="changeQtd(this,' + cart_[p].id + ',' + "'" + [cart_[p].opt] + "'" + ')">  ';
                (prod['options'][cart_[p].opt] == 0) ? $("#goForm").addClass("off") : "";

                for (var x = 0; x < prod['options'][cart_[p].opt]; x++) {
                    cartProd += "<option value='" + (x + 1) + "'" + ((x + 1) == cart_[p].qtd ? "selected" : "") + ">" + (x + 1) + "</option>"
                }

                cartProd += '</select>' +
                    
                    '<span class="delProd" onclick="delProd(' + (cart_[p].id + ",\'" + [cart_[p].opt]) + '\')">Excluir</span>' +
                    '</div>' +
                    '<span class="vari">Variação: ' + cart_[p].opt + '</span>' +
                    '</div>' +
                    '<div class="pPrice">' +
                    '<span>R$ ' + ((parseFloat(prod['price'])).toFixed(2)).replace(".", ",") + '</span>' +
                   
                    '</div></div>';
                $("#CartProds").append(cartProd);
                if (invalidProd == 1) {
                    $("#goForm").addClass("off")
                } else {
                    $("#goForm").removeClass("off")
                }
                totalPrice += (parseFloat((parseFloat(prod.price) * cart_[p].qtd)));
                totalItens += parseInt(cart_[p].qtd);

                $("#totalPrice").html("Subtotal (" + totalItens + " Itens) : R$ " + totalPrice.toFixed(2).replace(".", ",") + "")
               
            }
        })

    })

    if (cart_.length == 0) {
        $(".chechoutBtns").css("display", "none");
        $("#totalPrice").html("");
        $("#CartProds").append("<div class='noProds'><span>Carrinho de Compras Vazio</span><i class='fa-solid fa-cart-shopping'></i></div>");
    } else {
        $(".chechoutBtns").css("display", "flex");
    }
}

function changeQtd(select, id, opt) {
    addCart(id, $(select).val(), opt);
    callCart();
}


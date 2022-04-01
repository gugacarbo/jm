$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");

    callCart();

})



function delProd(id, opt) {
    addCart(id, 0, opt);
    setTimeout(() => {
        $("#CartProds").html("");
        callCart();
    }, 200);
}

function callCart() {

    var cart_ = localStorage.getItem("JM_CART");
    if (cart_) {
        cart_ = JSON.parse(cart_);
    } else {
        var cart_ = []
    }

    var totalPrice = 0;
    $.each(cart_, (p, item) => {
        var opts = cart_[p].qtd;
        $.get("/api/getProdById.json", { "id": cart_[p].id }, (prod) => {

            var cartProd = '<div class="cartP">' +
                '<div class="pImage">' +
                '<img src="' + prod['imgs'][1] + '" alt="">' +
                '</div>' +
                '<div class="pInfo">' +
                '<span class="pName">' + prod['name'] + '</span>' +
                (prod['options'][cart_[p].opt] > 0 ? "<span class='pAvailable'>Em Estoque" : "<span class='pAvailable' style='color:#922'>Indisponível") + '</span>' +
                '<div class="giftCheck"><input type="checkbox" id="' + prod['id'] + 'isGift"> Este produto é para presente?</div>' +
                '<div class="pQuantity">Qtd.:' +

                '<select>  ';
            for (var x = 0; x < prod['options'][cart_[p].opt]; x++) {
                cartProd += "<option value='" + x + 1 + "'>" + (x + 1) + "</option>"
            }

            console.log(cart_[p])
            cartProd += '</select>' +
                '<span class="delProd" onclick="delProd(' + (prod['id']+",\'"+ [cart_[p].opt]) + '\')">Excluir</span>' +
                '</div>' +
                '<span class="vari">Variação ' + cart_[p].opt + '</span>' +
                '</div>' +
                '<div class="pPrice">' +
                '<span>R$ ' + prod["price"] + '</span>' +
                '<span>Ou em até 4x de R$' + (prod["price"] / 4).toFixed(2) + '</span>' +
                '<span>Sem Juros</span>' +
                '</div></div>';
            $("#CartProds").append(cartProd);
            totalPrice += parseFloat(prod["price"]);
            $("#totalPrice").html("Subtotal(" + cart_.length + " Itens): R$ " + totalPrice.toFixed(2).replace(".", ",") + "")
        })
    })
}
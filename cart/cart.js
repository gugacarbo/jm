$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");

    callCart();
    
})

function delProd(id){
    addCart(id);
    $("#CartProds").html("");
    setTimeout(() => {
        callCart();
    }, 200);
}

function callCart(){
    
    var cart_ = localStorage.getItem("JM_CART");
    if (cart_) {
        cart_ = JSON.parse(cart_);
    } else {
        var cart_ = []
    }

    var totalPrice = 0;
    for (p in cart_) {
        $.get("/api/getProdById.json", { "id": cart_[p].id }, (prod) => {
            
            var cartProd = '<div class="cartP">' +
                '<div class="pImage">' +
                '<img src="'+prod['imgs'][1]+'" alt="">' +
                '</div>' +
                '<div class="pInfo">' +
                '<span class="pName">'+prod['name']+'</span>' +
                '<span class="pAvailable">'+(prod['availableQuantity'] > 0 ? "Em Estoque" : "Indisponível")+'</span>' +
                '<div class="giftCheck"><input type="checkbox" id="'+prod['id']+'isGift"> Este produto é para presente?</div>' +
                '<div class="pQuantity">Qtd.:' +
                
                '<select>  ';
                for(var x = 0; x < prod['availableQuantity']; x ++){
                    cartProd += "<option value='"+x+1+"'>"+(x+1)+"</option>"
                }
                
                cartProd += '</select>' +
                '<span class="delProd" onclick="delProd(' + (prod['id'] + p) + ')">Excluir</span>' +
                '</div>' +
                '</div>' +
                '<div class="pPrice">' +
                '<span>R$ '+prod["price"]+'</span>' +
                '<span>Ou em até 4x de R$'+(prod["price"]/4).toFixed(2)+'</span>' +
                '<span>Sem Juros</span>' +
                '</div></div>';
                $("#CartProds").append(cartProd);
                totalPrice += parseFloat(prod["price"]);
                $("#totalPrice").html("Subtotal("+cart_.length+" Itens): R$ "+totalPrice.toFixed(2).replace(".",",")+"")
            })
    }
}
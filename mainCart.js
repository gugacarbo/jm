/**
 * TODO Cart
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */
var cart = [];

$(document).ready(() => {
  initListStorage();
  setTimeout(() => {
    saveListStorage(cart)
  }, 200);
})

function initListStorage() {
  var cart_ = localStorage.getItem("JM_CART");
  if (cart_) {
    cart = JSON.parse(cart_);
  } else {
    cart = [];
  }
}

function saveListStorage(list) {
  var jsonStr = JSON.stringify(list);
  localStorage.setItem("JM_CART", jsonStr);
  $("#ItemCart").html(cart.length);
}

function deleteList() {
  cart = [];
  saveListStorage(cart);
}





function addCart(id, qtd = 0, opt = 0) {
  id = parseInt(id);
  if (opt == 0) {
    var scriptUrl = "/php/getProdById.php?id=" + id;
    //console.log(scriptUrl);

    $.ajax({
      url: scriptUrl,
      type: 'get',
      dataType: 'json',
      async: false,
      success: function (data) {
        data["options"] = JSON.parse(data["options"]);

        opt = ((Object.keys(data["options"])[0]));
      },
      fail: function (data) {
      }
    });
  }

  console.log(opt)

  //Verifica se o produto ja esta no carrinho e remove
  var cart_ = cart.filter((item) => {
    // console.log(item.id, id, item.opt, opt)
    return item.id != id || item.opt != opt;
  });
  // Atualiza carrinho
  cart = cart_;

  //Adiciona novo item ao carrinho se qtd > 0
  if (qtd > 0) {
    cart.unshift({ "id": id, "qtd": qtd, "opt": opt });
  }
  saveListStorage(cart);
}


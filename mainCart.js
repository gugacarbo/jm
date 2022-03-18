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
  $("#ItemCart").html(cart.length)
}

function deleteList() {
  cart = [];
  saveListStorage(cart);
}





function addCart(id, qtd = 0, opt = 0) {
  if (opt == 0) {
    var scriptUrl = "/api/getProdById.json?id=" + id;
    $.ajax({
      url: scriptUrl,
      type: 'get',
      dataType: 'json',
      async: false,
      success: function (data) {
        opt = (Object.keys(data["options"])[0]);
      }
    });
  }

  var cart_ = cart.filter((item) => {
    return item.id !== id || item.opt !== opt
  });

  cart = cart_;

  if (qtd > 0) {
    cart.unshift({ "id": id, "qtd": qtd, "opt": opt });
  }
  saveListStorage(cart);
}
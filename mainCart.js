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
    $.ajax({
      url: scriptUrl,
      type: 'get',
      dataType: 'json',
      async: false,
      success: function (data) {
        data["options"] = JSON.parse(data["options"]);
        console.log(data["options"] );

        $.each(data["options"], function (i, item) {
          if (data["options"][i] != 0 && opt == 0) {
            opt = i
          }
        })
      },
      fail: function (data) {
      }
    });
  }

  //Verifica se o produto ja esta no carrinho e remove
  var cart_ = cart.filter((item) => {
    return item.id != id || item.opt != opt;
  });
  cart = cart_;

  if (qtd > 0) {
    cart.unshift({ "id": parseInt(id), "qtd": parseInt(qtd), "opt": opt });
  }
  saveListStorage(cart);
}


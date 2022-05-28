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

function addCart(id, qtd = 0, opt = 0, msg = 1) {
  
  id = parseInt(id);

  if (opt == 0) {
    var scriptUrl = "/api/get/getProdById.php?id=" + id;
    $.ajax({
      url: scriptUrl,
      type: 'get',
      dataType: 'json',
      async: false,
      success: function (data) {
        data["options"] = (data["options"]);

        $.each(data["options"], function (i, item) {
          if (data["options"][i] != 0 && opt == 0) {
            opt = i
          }
        })
        if (opt == 0) {
          alert("Não há opções disponíveis para este produto!");
          return;
        }
      },
      fail: function (data) {
          alert("Erro ao buscar produto!");
        return;
      }
    });
  }

  if(opt != 0){
    var cart_ = cart.filter((item) => {
      return item.id != id || item.opt != opt;
  });
  cart = cart_;

  if(msg == 1){
    $("#addCartMessage").css("display", "flex");
    setTimeout(() => {
      $("#addCartMessage").css("opacity", "1");
    }, 20);
    setTimeout(() => {
      $("#addCartMessage").css("opacity", "0");
      setTimeout(() => {
        $("#addCartMessage").css("display", "none");
      }, 500);
    }, 2000);
  }
  if (qtd > 0 ) {

    cart.unshift({ "id": parseInt(id), "qtd": parseInt(qtd), "opt": opt });
  }

  cart.sort(function (a, b) {
    if (a.id > b.id) {
      return 1;
    }
    if (a.id < b.id) {
      return -1;
    }
    // a must be equal to b
    return 0;
  });
  saveListStorage(cart);
}
}


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

 $(document).ready(()=>{
    initListStorage();
    setTimeout(() => {
      $("#ItemCart").html(cart.length)
    }, 200);
  })
  
  function initListStorage() {
  
    var cart_ = localStorage.getItem("JM_CART");
    if (cart_) {
      cart = JSON.parse(cart_);
    } else {
      //console.log("not List")
    }
  
    //console.log(cart)
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
  
  
  
  
  
  function addCart(id, qtd = 0) {
    var cart_ = cart.filter((item) => item.id !== id);
    cart = cart_;
    if(qtd>0){
      cart.unshift({ "id": id, "qtd": qtd });
    }
    saveListStorage(cart);
  }
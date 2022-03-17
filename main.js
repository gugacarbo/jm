$(document).ready(() => {
  $("header").load("includes/header.html");
  $("footer").load("includes/footer.html");

  $('form').submit(false);

  $("#SendNewsletter").on('click', () => {
    var nome = $("#NewsletterNome").val();
    var email = $("#NewsletterEmail").val();
    var data = {
      'nome': nome,
      "email": email
    }
  })


  setBanner("#BannerSlider", "MAIN");

  getGliders();





  setTimeout(callBanner, 100);
})




function getGliders(){
  $.get("/api/getGlider.json", (data)=>{
    $.each(data, function (i, img) {
      
    });
  })
}





  function setProdSlider(id_el, prods_id) {
  prods_id.forEach(prod_id => {
    var prod = getProductById(prod_id);
    var ProdCarousel =
    "<div class='carouselItem'>"
    + "<a class='carouselImg' href='/product?id="+prod['id']+"'>"
    + "<img src='" + prod['imgs'][0] + "'>"
    + "<span class='carouselPromo'" + (prod['promo'] > 0 ? ">" + Math.trunc((1 - (prod['price'] / prod['promo'])) * 100) + "% OFF" : "style='display:none;'>") + "</span>"
    + "</a>"
    + "<span class='carouselItemName'>" + prod['name'] + "</span>"
    + "<span class='carouselItemPrice'>R$" + prod['price'] + "</span>"
    + "<span class='carouselItemPay'>ou em 4x de " + Math.round(prod['price'] / 4) + "</span>"
    + "<i class='fas fa-shopping-cart' onclick='addCart("+prod['id']+")'></i>"
    + "</div>";
    $(id_el).append(ProdCarousel)
  })
}


/**
 * ! Banner
 * 
 * 
 * 
 * 
 */
  function setBanner(el_id, banner_name) {
    //"/api/getBanner.php"
    $.get("/api/getBanner.json", { 'name': banner_name }, function (data) {
      $.each(data, function (i, img) {
        $(el_id).append("<img src='" + img + "'>");
      });
    })
  }

function callBanner() {
  var bannerSlider = new Glider(document.querySelector('#BannerSlider'), {
    slidesToShow: 1,
    slidesToScroll: 1,
    draggable: false,
    dragVelocity: 2,
    dots: '.dotsBanner',
    duration: 3,
    rewind: true,
    arrows: {
      prev: '.prevBanner',
      next: '.nextBanner',
    },
  });
  sliderAuto(bannerSlider, 1500)
}



function sliderAuto(slider, miliseconds) {
  const slidesCount = slider.track.childElementCount;
  let slideTimeout = null;
  let nextIndex = 1;

  function slide() {
    slideTimeout = setTimeout(
      function () {
        nextIndex = slider.slide + 1;
        if (nextIndex >= slidesCount) {
          nextIndex = 0;
        }

        slider.scrollItem(nextIndex);
      },
      miliseconds
    );
  }

  slider.ele.addEventListener('glider-animated', function () {
    window.clearInterval(slideTimeout);
    slide();
  });

  slide();
}
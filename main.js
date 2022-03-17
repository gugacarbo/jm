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
  
})





/**
 * * Gliders
 * 
 * 
 * 
 * 
 */


var Gliders = [];
function getGliders() {

  $.get("/api/getGlider.json", (data) => {
    $.each(data, function (i, prods) {

      var category = "<div class='categoryCarousel'>" +
        "<div class='carouselTitle'>" + i + "</div>" +
        "<div class='showCarousel' id='Carousel" + i + "'>" +
        "<div class='carouselItem moreItens'>" +
        "<span>Exibir Mais Produtos</span>" +
        "</div>" +
        "</div>" +
        "<div class='carouselBtn prev" + i + "'><i class=' fas fa-chevron-left'></i></div>" +
        "<div class='carouselBtn next" + i + "'><i class='fas fa-chevron-right'></i></div>" +
        "<div role='tablist' class='dots" + i + "'></div>" +
        "</div>";
      $("#Carousel").append(category)



      $.each(prods, function (k, id) {
        $.get("api/getProdById.json", { "id": id }, function (prod) {

          var ProdCarousel =
            "<div class='carouselItem'>"
            + "<a class='carouselImg' href='/product?id=" + prod['id'] + "'>"
            + "<img src='" + prod['imgs'][1] + "'>"
            + "<span class='carouselPromo'" + (prod['promo'] > 0 ? ">" + Math.trunc((1 - (prod['price'] / prod['promo'])) * 100) + "% OFF" : "style='display:none;'>") + "</span>"
            + "</a>"
            + "<span class='carouselItemName'>" + prod['name'] + "</span>"
            + "<span class='carouselItemPrice'>R$" + prod['price'] + "</span>"
            + "<span class='carouselItemPay'>ou em 4x de " + Math.round(prod['price'] / 4) + "</span>"
            + "<i class='fas fa-shopping-cart' onclick='addCart(" + prod['id'] + ")'></i>"
            + "</div>";

          $("#Carousel" + i).append(ProdCarousel)
        })

      })

      Gliders.push(i)
    });
  })

  setTimeout(callGliders, 300);
}



function callGliders() {
  Gliders.forEach(i => {
    new Glider(document.querySelector('#Carousel' + i), {
      slidesToShow: 4.5,
      slidesToScroll: 2,
      draggable: true,
      dragVelocity: 1,
      dots: '.dots' + i,
      duration: 3,
      arrows: {
        prev: '.prev' + i,
        next: '.next' + i,
      },
    });
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
  setTimeout(callBanner, 100);
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
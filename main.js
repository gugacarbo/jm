$(document).ready(() => {
  $("header").load("/includes/header.html");
  $("footer").load("/includes/footer.html");
  $('form').submit(false);

  $('#contactPhone').mask('(00) 0.0000-0000');

  //*Cadatra novo usuario na newsletter
  $("#SendNewsletter").on('click', () => {
    var nome = $("#NewsletterName").val();
    var email = $("#NewsletterEmail").val();
    if (nome && email) {
      var data = {
        'name': nome,
        "email": email
      }

      $.post("/api/post/cadNewsLetter.php", data, function (data) {

        if (data["status"] >= 200 && data["status"] < 300) {
          $("#NewsletterErrorDisplay").css("display", "flex");
          $("#NewsletterErrorDisplay").css("color", "#fff");
          $("#NewsletterErrorDisplay").html("Cadastro realizado com sucesso!");
          setTimeout(() => {
            $("#NewsletterErrorDisplay").css("display", "none");
          }, 1300);
        } else {
          $("#NewsletterErrorDisplay").css("display", "flex");
          $("#NewsletterErrorDisplay").css("color", "#fff")
          $("#NewsletterErrorDisplay").html(data.status == 403 ? "Tente Novamente Mais Tarde" : "Erro ao cadastrar");
          setTimeout(() => {
            $("#NewsletterErrorDisplay").css("display", "none");
          }, 1300);
        }
      })
    }
  })


  //*Envia contato para db e redireciona para whatsapp
  $("#contactSend").on('click', () => {
    var nome = $("#contactName").val();
    var phone = $("#contactPhone").val();
    var message = "Nome: " + nome + "\n"
      + "Telefone: " + phone + "\n"
      + $("#contactMessage").val();

    if (nome && phone && $("#contactMessage").val()) {
      var sendToDb = $.post("/api/post/sendContact.php", { name: nome, phone, message }, data => {

      })
      let url = "https://wa.me/+5549999604384?text=";

      window.open(
        url + encodeURI(message),
        '_blank'
      )
    }
  })


  setBanner("#BannerSlider", "MAIN_BANNER");
  getGliders();
})

/**
 * * Gliders
 */

var Gliders = [];
async function getGliders() {

  $.get("/api/get/getGlider.php").then(data => {
    $.each(data, function (i, prods) {

      var category = "<div class='categoryCarousel'>" +
        "<div class='carouselTitle'>" + prods["name"] + "</div>" +
        "<div class='showCarousel' id='Carousel" + prods["name"].replace(" ", "") + "'>" +
        "<a class='carouselItem moreItens' href='/products/?cat=" + prods["category"] + "'>" +
        "<span>Exibir Mais Produtos</span>" +
        "</a>" +
        "</div>"
      if (Object.keys((prods["prod_ids"])).length >= 4) {
        category += "<div class='carouselBtn prev" + prods["name"].replace(" ", "") + "'><i class=' fas fa-chevron-left'></i></div>" +
          "<div class='carouselBtn next" + prods["name"].replace(" ", "") + "'><i class='fas fa-chevron-right'></i></div>"
      }

      category += "<div role='tablist' class='dots" + prods["name"].replace(" ", "") + "'></div>" +
        "</div>";
      $("#Carousel").append(category);

      var prodCount = Object.keys((prods["prod_ids"])).length;
      var actProdCount = 0;

      $.each((prods["prod_ids"]), function (k, prod) {
        //console.log(prod)
        var ProdCarousel =
          "<div class='productModel " + (prod.totalQuantity == 0 ? "unavailable' style='order:5;'" : "'") + ">"
          + "<a class='productModelImg' href='/product/?id=" + prod['id'] + "'>"
          + "<img src='" + prod['imgs'][1] + "'>"
          + "<span class='productModelPromo'" + (prod['promo'] > 0 ? ">" + Math.trunc((1 - (prod['price'] / prod['promo'])) * 100) + "% OFF" : "style='display:none;'>") + "</span>"
          + "</a>"
          + "<span class='productModelName'>" + prod['name'] + "</span>"
          + "<span class='productModelPrice'>R$" + (parseFloat(prod['price']).toFixed(2)).replace(".", ",") + "</span>"
          + "<span class='productModelPay'>ou em 2x de " + (parseFloat(prod['price']) / 2).toFixed(2).replace(".", ",") + "</span>"
          //+ "<i class='fas fa-shopping-cart' onclick='addCart(" + (prod['id']) + ", 1)'></i>"
          + "<i class='fas fa-shopping-cart' onclick='addCart(" + (prod.id) + ", 1)'></i>"
          + "</div>";
        //console.log(ProdCarousel)

        $("#Carousel" + prods["name"].replace(" ", "")).append(ProdCarousel)
        actProdCount++;

        if (prodCount == actProdCount) {
          callGliders(prods["name"].replace(" ", ""))
        }
      })
    })

  })
}




async function callGliders(i) {


  var g = new Glider(document.querySelector('#Carousel' + i), {
    slidesToShow: 4.5,
    slidesToScroll: 3,
    draggable: true,
    dragVelocity: 1,
    dots: '.dots' + i,
    duration: 3,
    arrows: {
      prev: '.prev' + i,
      next: '.next' + i,
    },
    responsive: [
      {
        breakpoint: 000,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 280,
        settings: {
          slidesToShow: 1.5,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2.5,
          slidesToScroll: 2,
        },
      },
      {
        breakpoint: 700,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 2,
        },
      },
      {
        breakpoint: 800,
        settings: {
          slidesToShow: 3.5,
          slidesToScroll: 3,
        },
      },
      {
        breakpoint: 900,
        settings: {
          slidesToShow: 4.5,
          slidesToScroll: 3,
        },
      },
    ],
  });

}



/**
 * * Banner
 */
async function setBanner(el_id, banner_name) {
  //"/api/getBanner.php"
  return await $.get("/api/get/getBanner.php", { 'name': banner_name }, function (data) {
    if (data.status >= 200 && data.status < 300) {
      $.each(data["images"], function (i, img) {
        if (img != "")
          $(el_id).append("<img src='" + img + "'>");
      });
    }
  })
    .then((value) => {
      callBanner();
    })
}


//setTimeout(callBanner, 100);

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




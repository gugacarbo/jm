
$(document).ready(() => {
  $("header").load("/includes/header.html");
  $("footer").load("/includes/footer.html");



  $('form').submit(false);

  $("#SendNewsletter").on('click', () => {
    var nome = $("#NewsletterName").val();
    var email = $("#NewsletterEmail").val();
    console.log(nome, email)
    if (nome && email) {
      var data = {
        'name': nome,
        "email": email
      }

      $.get("/php/cadNewsLetter.php", data, function (data) {
        $("#NewsletterErrorDisplay").css("display", "flex");
        setTimeout(() => {
          $("#NewsletterErrorDisplay").css("display", "none");
        }, 1300);
      })
    }
  })


  $('#contactPhone').mask('(00) 0.0000-0000');

  $("#contactSend").on('click', () => {
    var nome = $("#contactName").val();
    var phone = $("#contactPhone").val();
    var message = "Nome: " + nome + "\n"
      + "Telefone: " + phone + "\n"
      + $("#contactMessage").val();

    if (nome && phone && $("#contactMessage").val()) {
      var sendToDb = $.get("/php/sendContact.php",{name : nome, phone, message}, data =>{

      })
      let url = "https://wa.me/+5549999604384?text=";
      console.log(url + encodeURI(message))

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
 * 
 * 
 * 
 * 
 */


var Gliders = [];
async function getGliders() {

  try {
    await $.get("/php/getGlider.php").then(d => {
      var data = JSON.parse(d);
      //console.log(data)
      var x = $.each(data, function (i, prods) {
        var category = "<div class='categoryCarousel'>" +
          "<div class='carouselTitle'>" + prods["name"] + "</div>" +
          "<div class='showCarousel' id='Carousel" + prods["name"] + "'>" +
          "<a class='carouselItem moreItens' href='/products/?cat=" + prods["name"].toLowerCase() + "'>" +
          "<span>Exibir Mais Produtos</span>" +
          "</a>" +
          "</div>" +
          "<div class='carouselBtn prev" + prods["name"] + "'><i class=' fas fa-chevron-left'></i></div>" +
          "<div class='carouselBtn next" + prods["name"] + "'><i class='fas fa-chevron-right'></i></div>" +
          "<div role='tablist' class='dots" + prods["name"] + "'></div>" +
          "</div>";
        $("#Carousel").append(category);

        $.each(JSON.parse(prods["prod_ids"]), function (k, id) {
          $.get("/php/getProdById.php", { id }, (p) => {
            var prod = JSON.parse(p);
            prod.imgs = (JSON.parse(prod["imgs"]));
            prod.options = (JSON.parse(prod["options"]));
            //console.log(prod)
            var ProdCarousel =
              "<div class='carouselItem'>"
              + "<a class='carouselImg' href='/product/?id=" + prod['id'] + "'>"
              + "<img src='" + prod['imgs'][1] + "'>"
              + "<span class='carouselPromo'" + (prod['promo'] > 0 ? ">" + Math.trunc((1 - (prod['promo'] / prod['price'])) * 100) + "% OFF" : "style='display:none;'>") + "</span>"
              + "</a>"
              + "<span class='carouselItemName'>" + prod['name'] + "</span>"
              + "<span class='carouselItemPrice'>R$" +  (parseFloat(prod['promo'] > 0 ?  prod['promo'] : prod['price']).toFixed(2)).replace(".", ",") + "</span>"
              + "<span class='carouselItemPay'>ou em 2x de " + (parseFloat((prod['promo'] > 0 ?  prod['promo'] : prod['price']) / 2).toFixed(2)).replace(".", ",") + "</span>"
              //+ "<i class='fas fa-shopping-cart' onclick='addCart(" + (prod['id']) + ", 1)'></i>"
              + "<i class='fas fa-shopping-cart' onclick='addCart(" + (id) + ", 1)'></i>"
              + "</div>";
              //console.log(ProdCarousel)

            $("#Carousel" + prods["name"]).append(ProdCarousel)
          })
        })
        setTimeout(() => {

          callGliders(prods["name"]);
        }, 300);
      })
    })
  } catch (e) {
    console.log(e)
  } finally {
  }
}


async function callGliders(i) {
  var g = await new Glider(document.querySelector('#Carousel' + i), {
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
        breakpoint: 500,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 3,
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
 * ! Banner
 * 
 * 
 * 
 * 
 */
function setBanner(el_id, banner_name) {
  //"/api/getBanner.php"
  $.get("/php/getBanner.php", { 'name': banner_name }, function (d) {
    var data = JSON.parse(d);
    var images = JSON.parse(data["images"]);
    $.each(images, function (i, img) {
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




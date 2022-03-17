
var cart = [
  //{"id" : "qtd"},
  {"1" : "1"},
  {"2" : "2"},
  {"3" : "2"},
];



$(document).ready(() => {
  
  $('form').submit(false);

  $("#SendNewsletter").on('click', ()=>{
    var nome = $("#NewsletterNome").val();
    var email = $("#NewsletterEmail").val();
    var data = {
      'nome' : nome,
      "email" : email
    }

    alert("Email Cadastrado Com Sucesso!")
    console.log(data)
   /* $.get("cadastraNewsLetter.php", data, (data)=>{
      alert(data)
    })*/
  })

  var imgs = ["https://img.elo7.com.br/product/zoom/1C340F5/quadro-tela-paisagens-natureza-praia-coqueiro-mar-areia-004-quadro-tela.jpg",
    "https://img.elo7.com.br/product/zoom/1C340F5/quadro-tela-paisagens-natureza-praia-coqueiro-mar-areia-004-quadro-tela.jpg",
    "https://img.elo7.com.br/product/zoom/1C340F5/quadro-tela-paisagens-natureza-praia-coqueiro-mar-areia-004-quadro-tela.jpg"
  ]
  setBannerSlider("#BannerSlider", imgs);



  setProdSlider("#Carousel1", [1, 2, 3, 4, 5, 6]);
  setProdSlider("#Carousel3", [1, 2, 3, 4, 5, 6]);
  setProdSlider("#Carousel2", [1, 2, 3, 4, 5, 6]);
  callSliders()

})








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

//Sliders ------------------------------------------------------------

function setBannerSlider(el_id, imgs) {
  imgs.forEach(img => {
    $(el_id).append("<img src='" + img + "'>")
  })
}



function callSliders() {
  var carousel1 = new Glider(document.querySelector('#Carousel1'), {
    slidesToShow: 4.5,
    slidesToScroll: 2,
    draggable: true,
    dragVelocity: 1,
    dots: '.dots',
    duration: 3,
    arrows: {
      prev: '.prev1',
      next: '.next1',
    },
  });

  var carousel2 = new Glider(document.querySelector('#Carousel2'), {
    slidesToShow: 4.5,
    slidesToScroll: 2,
    draggable: true,
    dragVelocity: 1,
    dots: '.dots2',
    duration: 3,
    arrows: {
      prev: '.prev2',
      next: '.next2',
    },
  });

  var carousel3 = new Glider(document.querySelector('#Carousel3'), {
    slidesToShow: 4.5,
    slidesToScroll: 2,
    draggable: true,
    dragVelocity: 1,
    dots: '.dots3',
    duration: 3,
    arrows: {
      prev: '.prev3',
      next: '.next3',
    },
  });


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
  sliderAuto(bannerSlider, 1000)


initListStorage();

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



function addCart(id){

}

//salvando em storage
function saveListStorage(list){
	var jsonStr = JSON.stringify(list);
	localStorage.setItem("list",jsonStr);
}

//verifica se já tem algo salvo
function initListStorage(){
	var testList = localStorage.getItem("list");
	if(testList){
		list = JSON.parse(testList);
	}

}

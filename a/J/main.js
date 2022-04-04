const prod = '<div class="product"> <div class="imgContainer"> <div class="promo">33%</div> </div><h3 class="name">colar dahorao de baralho</h3><h4 class="price">R$ 199,90</h4><span class="credit">ou em 4 x 49,90</span><a href="prod.html"><i class="fas fa-cart-plus"></i></a></div>'

$(document).ready(function () {

    $("#pMin").mask('000.000,00', { reverse: true });;
    $("#pMax").mask('000.000,00', { reverse: true });;



    var i = 0;
    $(".toggleMenu").on('click', function () {
        //i == 0 ? $("#menu").addClass('menuOpen') : $("#menu").removeClass('menuOpen');
        i == 0 ? i = 1 : i = 0;
        $("#menu").toggleClass('menuOpen', i)
        $(".toggleMenuContent").toggleClass('toggleOn', i)
        document.body.style.overflow = i == 1 ? "initial" : "hidden";
        console.log('on')
        $("#Bd1").toggleClass('B1', i)
        $("#Bd2").toggleClass('B2', i)
        $("#Bd3").toggleClass('B3', i)

    })



    var div = $('#menuFixed');
    $(window).scroll(function () {
        if ($(this).scrollTop() > 700) {
            div.addClass("on");
            $('body').css('padding-top', 100);
        } else {
            div.removeClass("on");
            $('body').css('padding-top', 0);
        }
    });


    var k = 0;
    $(".filter").on('click', function () {
        $(".fa-angle-down").toggleClass("on", k);
        $("#menuFixed").toggleClass("filterMobile", k);

        k = !k;
    });



    var x = 0;
    $(window).scroll(function () {
        if ($(window).scrollTop() == (($(document).height()) - ($(window).height())) && x < 20) {
            for (var i = 0; i < 4; i++) {
                $(".products").append(prod)
            }
            x += 4;
        }
    });
    
    for (var i = 0; i < 11; i++) {
        $(".products").append(prod)
    }
    


    switchable({
        $element: $('#slides'),
        // MORE OPTIONS HERE
        showNav: true,
        animateSpeed: 600,
        pauseOnHover: true,
        interval: 5000,
    });


});


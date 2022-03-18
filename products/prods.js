$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");
    setBanner("#ProdsSlider", "PRODS");

    $('#SearchMinVal').mask('000.000,00', { reverse: true });
    $('#SearchMaxVal').mask('000.000,00', { reverse: true });

    $(window).scroll(function () {
        if ($(this).scrollTop() > 550) {
            $(".filterBox").css("position", "fixed");
            $(".filterBox").css("left", "0");
            $(".filterBox").css("top", "0");
            $(".products").css("margin-top", "150px");
        } else {
            $(".filterBox").css("position", "inherit");
            $(".products").css("margin-top", "0px");
        }
    });

    callProds("");
    
    $("#Search").on('click', () => {
        var minVal = $("#SearchMinVal").val();
        var MaxVal = $("#SearchMaxVal").val();
        var category = $("#SearchCategory").val();
        var order = $("#SearchOrderBy").val();
        var text = $("#SearchText").val();
        var searchQuery = {
            "min": minVal,
            "max": MaxVal,
            "cat": category,
            "ord": order,
            "text": text
        }
        callProds(searchQuery);
    })
})


/**
 * *Produtos
 * 
 * 
 * 
 */

function callProds(query) {
    $.get("/api/getFiltered.json", query, (ids) => {
        $("#ShowProducts").html("");
        $.each(ids, function (_, id) {
            $.get("/api/getProdById.json", { "id": id }, function (prod) {
                var prodApend =
                    "<div class='product'>"
                    + "<a class='prodImage' href='/product/?id=" + prod['id'] + "'>"
                    + "<img src='" + prod['imgs'][1] + "'>"
                    + "<img class='sImage' src='" + prod['imgs'][2] + "'>"
                    + "<span class='promo'" + (prod['promo'] > 0 ? ">" + Math.trunc((1 - (prod['price'] / prod['promo'])) * 100) + "% OFF" : "style='display:none;'>") + "</span>"
                    + "</a>"
                    + "<span class='prodName'>" + prod['name'] + "</span>"
                    + "<span class='prodPrice'>R$" + prod['price'] + "</span>"
                    + "<span class='prodPay'>ou em 4x de " + Math.round(prod['price'] / 4) + "</span>"
                    + "<i class='fas fa-shopping-cart' onclick='addCart(" + prod['id'] + ", 1)'></i>"
                    + "</div>";

                $("#ShowProducts").append(prodApend)
            })
        })
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
    var bannerSlider = new Glider(document.querySelector('#ProdsSlider'), {
        slidesToShow: 1,
        slidesToScroll: 1,
        draggable: false,
        dragVelocity: 2,
        dots: '.dotsProdBanner',
        duration: 3,
        rewind: true,
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
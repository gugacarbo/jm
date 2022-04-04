$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");
    setBanner("#ProdsSlider", "PRODUCTS_BANNER");

    $('#SearchMinVal').mask('000.000,00', { reverse: true });
    $('#SearchMaxVal').mask('000.000,00', { reverse: true });

    callProds("");

    $("#Search").on('click', () => {
        var minVal = $("#SearchMinVal").val();
        var MaxVal = $("#SearchMaxVal").val();
        var category = $("#SearchCategory").val();
        var order = $("#SearchOrderBy").val();
        var text = $("#SearchText").val();
        console.log(minVal, MaxVal, category, order, text)
        var searchQuery = {
            "min": (minVal.replace(",", ".") || 0),
            "max": (MaxVal.replace(",", ".") || 20000),
            "cat": category || 0,
            "order": order || "price DESC", 
            "text": text || ""
        }
        
        console.log(searchQuery)
        callProds(searchQuery);
    })

    $("#toggleFilter").on("click", () => {
        $(".filterBox").toggleClass("off");
    })

    $(document).scroll(function () {
        if ($(document).scrollTop() > $("#ShowProducts").height()) {
            console.log("Loading");
        }
    });
})


/**
 * *Produtos
 * 
 * 
 * 
 */



function callProds(query) {
    $.get("/php/getFiltered.php", query, (ids_) => {
        console.log(ids_)
        var ids = JSON.parse(ids_);
        $("#ShowProducts").empty();
        $.each(ids, function (_, id) {

            $.get("/php/getProdById.php", { id: id }, (p) => {
                var prod = JSON.parse(p);
                prod.imgs = (JSON.parse(prod["imgs"]));
                prod.options = (JSON.parse(prod["options"]));
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
                    + "<i class='fas fa-shopping-cart' onclick='addCart(" + id + ", 1)'></i>"
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




//! Test Search Text
$('#SearchText').keyup(function () {
    console.log(filter($(this).val()));

})


function filter(value) {
    var jsonArray = [{ "name": "123" }, { "name": "2" }];
    console.log(value)
    return $.grep(jsonArray, function (n, i) {
        return n.name.includes(value);
    });
}

//

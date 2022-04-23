$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");


    $("#Search").on('click', () => {
        search();
    })

    $("#toggleFilter").on("click", () => {
        $(".filterBox").toggleClass("off");
    })


    setBanner("#ProdsSlider", "PRODUCTS_BANNER");
    getCategory()

    callProds({
        "text": $.urlParam("text"),
        "min": $.urlParam("min") || 0,
        "max": $.urlParam("max") || 20000,
        "cat": $.urlParam("cat") || 0,
        "order": $.urlParam("order") || "price ASC"
    });

    //range id SearchMaxRange on change, change SearchMaxVal value


    //range id SearchMinRange on change, change SearchMinVal value

    /*
    */
    //SearchMaxRange on change
    $("#SearchMaxRange").on("change", () => {
        search();
    })
    //SearchMinRange on change
    $("#SearchMinRange").on("change", () => {
        search();
    })

    $("#slider-range").on("slidechange", function (event, ui) {
        search();
    });
    //SearchCategory on change
    $("#SearchCategory").on("change", () => {
        search();
    })

    //SearchOrderBy on change
    $("#SearchOrderBy").on("change", () => {
        search();
    })
    $("#filterProducts input[type='number']").on("change paste", () => {
        search();
    })


    var typingTimer;                //timer identifier
    var doneTypingInterval = 1000;  //time in ms, 5 seconds for example
    var $input = $('#SearchText');

    //on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });

    function doneTyping() {
        search();
    }

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() && prodCount >= numProd) {
            pageDown();
            $(".loadingProducts").css("display", "block");
        }
    });

})





function search(n = 1) {
    if (n == 1) {
        page = 0;
        $("#ShowProducts").empty();
    }
    var minVal = $("#SearchMinVal").val();
    var MaxVal = $("#SearchMaxVal").val();
    var category = $("#SearchCategory").val();
    var order = $("#SearchOrderBy").val();
    var text = $("#SearchText").val();
    //console.log(minVal, MaxVal, category, order, text)
    var searchQuery = {
        "min": (minVal.replace(",", ".") || 0),
        "max": (MaxVal.replace(",", ".") || 20000),
        "cat": category || 0,
        "order": order || "price ASC",
        "text": text || ""
    }
    callProds(searchQuery);
}
/**
 * *Produtos
 */
var page = 0;
var numProd = 15;
var maxPages = 0;
var prodCount = 0;
function callProds(query) {
    $.get("/php/getFiltered.php", query, (ids_) => {
        prodCount = 0;
        var ids = JSON.parse(ids_);
        maxPages = Math.ceil(ids.length / numProd);
        $("#maxPages").html(maxPages);

        var SlicedId = ids.slice(page * numProd, (page + 1) * numProd)

        $.each(SlicedId, function (_, id) {
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
                    + "<span class='prodPrice'>R$" + (parseFloat(prod['price']).toFixed(2)).replace(".", ",") + "</span>"
                    + "<span class='prodPay'>ou em 2x de " + (parseFloat((prod['price']) / 2).toFixed(2)).replace(".", ",") + "</span>"
                    + "<i class='fas fa-shopping-cart' onclick='addCart(" + (id) + ", 1)'></i>"
                    + "</div>";
                $("#ShowProducts").append(prodApend)
                prodCount++;
            })
        })
        if (Object.keys(ids).length == 0) {
            $("#ShowProducts").append("<h1 class='notFound'>Nenhum produto encontrado </h1>")
        }
        setTimeout(() => {
            $(".loadingProducts").css("display", "none");
        }, 510);
    });
}


//*  Category
function getCategory() {
    $.get("/php/getCategory.php", function (data) {
        var categories = JSON.parse(data);
        $.each(categories, function (i, cat) {
            $("#SearchCategory").append("<option value='" + cat["id"] + "'>" + cat["name"] + "</option>");
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
async function setBanner(el_id, banner_name) {
    //"/api/getBanner.php"
    return await $.get("/php/getBanner.php", { 'name': banner_name }, function (d) {
        var data = JSON.parse(d);
        var images = JSON.parse(data["images"]);
        $.each(images, function (i, img) {
            if (img != "")
                $(el_id).append("<img src='" + img + "'>");
        });
    })
        .then((value) => {
            callBanner();
        })
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

$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    }
    else {
        return results[1] || 0;
    }
}


function pageDown() {
    page++;
    search(0);
}
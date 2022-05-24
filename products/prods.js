var minVal = 0;
var MaxVal = 20000;
var sTimer
$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");

    setBanner("#ProdsSlider", "PRODUCTS_BANNER");
    getCategory()
    search();

    $("#Search i").on('click', () => {
        search();
    })

    $("#toggleFilter").on("click", () => {
        $(".filterBox").toggleClass("off");
    })
    //SearchMaxRange on change
    $("#SearchMaxRange").on("change", () => {
        search();
    })

    //SearchMinRange on change
    $("#SearchMinRange").on("change", () => {
        search();
    })

    $("#slider-range").on("slidechange", function (event, ui) {
        clearTimeout(sTimer);
        sTimer = setTimeout(() => {
            search();
        }, 200);
    });

    //SearchCategory on change
    $("#SearchCategory").on("change", () => {
        clearTimeout(sTimer);
        sTimer = setTimeout(() => {
            search();
        }, 200);
    })

    //SearchOrderBy on change
    $("#SearchOrderBy").on("change", () => {
        search();
    })

    $("#filterProducts input[type='number']").on("keyup change paste", (event) => {
        //if key is enter
        if (event.keyCode == 13) {
            search();
        }
        clearTimeout(sTimer);
        sTimer = setTimeout(() => {
            search();
        }, 200);
    })


    var typingTimer;
    var doneTypingInterval = 1000;
    var $input = $('#SearchText');

    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });


    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });



    //? On scroll

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() && prodCount >= numProd) {
            pageDown();
            $(".loadingProducts").css("display", "block");
        }
    });

})


function doneTyping() {
    search();
}



var searching;

function search(n = 1) {
    if (n == 1) {
        page = 0;
        $("#ShowProducts").empty();
    }

    var category = $("#SearchCategory").val();
    var order = $("#SearchOrderBy").val();
    var text = $("#SearchText").val();
    minVal = $("#SearchMinVal").val();
    MaxVal = $("#SearchMaxVal").val();
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
    $(".loadingProducts").css("display", "flex");

    searching = 1;
    $.get("/api/get/getFiltered.php", query, (data) => {

        if (data.status >= 200 && data.status < 300) {
            prodCount = 0;
            var prods = data.products
            if (data["maxPrice"] > 0) {
                var MaxPriceS = Math.ceil((data["maxPrice"] / 100) * 100) + 100;
                console.log(MaxPriceS)
                $('#slider-range').slider("option", "max", MaxPriceS);
                $("#SearchMaxVal").val() > (MaxPriceS) ? $("#SearchMaxVal").val(MaxPriceS) : $("#SearchMaxVal").val(parseInt($("#SearchMaxVal").val()));
                $("#SearchMinVal").val() > (MaxPriceS) ? $("#SearchMinVal").val(0) : $("#SearchMinVal").val(parseInt($("#SearchMinVal").val()));
                $("#ShowProducts").append("<h1 class='notFound'>Nenhum produto encontrado </h1>")
            } else if (prods.length == 0) {
                $("#ShowProducts").append("<h1 class='notFound'>Nenhum produto encontrado </h1>")
            } else {

                maxPages = Math.ceil(prods.length / numProd);
                $("#maxPages").html(maxPages);
                var SlicedProds = prods.slice(page * numProd, (page + 1) * numProd)
                var MaxPriceS = Math.ceil((prods[0]["maxPrice"] / 100) * 100) + 100;
                $('#slider-range').slider("option", "max", MaxPriceS);
                $("#SearchMaxVal").val() > (MaxPriceS) ? $("#SearchMaxVal").val(MaxPriceS) : $("#SearchMaxVal").val(parseInt($("#SearchMaxVal").val()));
                $("#SearchMinVal").val() > (MaxPriceS) ? $("#SearchMinVal").val(0) : $("#SearchMinVal").val(parseInt($("#SearchMinVal").val()));

                $.each(SlicedProds, function (_, prod) {
                    prod.imgs = (JSON.parse(prod["imgs"]));
                    prod.options = (JSON.parse(prod["options"]));
                    //console.log(prod)
                    var prodApend =
                        "<div class='productModel " + (prod.totalQuantity == 0 ? "unavailable" : "") + "' " + (prod.totalQuantity == 0 ? "style='order:100;'" : "") + ">"
                        + "<a class='productModelImg' href='/product/?id=" + prod['id'] + "'>"
                        + "<img src='" + prod['imgs'][1] + "'>"
                        + ((prod['imgs'][2] != "") ? "<img class='productModelSecImg' src='" + prod['imgs'][2] + "'>" :  "<img class='productModelSecImg' src='" + prod['imgs'][1] + "'>")
                        + "<span class='productModelPromo'" + (prod['promo'] > 0 ? ">" + Math.trunc((1 - (prod['price'] / prod['promo'])) * 100) + "% OFF" : "style='display:none;'>") + "</span>"
                        + "</a>"
                        + "<span class='productModelName'>" + prod['name'] + "</span>"
                        + "<span class='productModelPrice'>R$" + (parseFloat(prod['price']).toFixed(2)).replace(".", ",") + "</span>"
                        + "<span class='productModelPay'>ou em 2x de " + (parseFloat((prod['price']) / 2).toFixed(2)).replace(".", ",") + "</span>"
                        + "<i class='fas fa-shopping-cart' onclick='addCart(" + (prod['id']) + ", 1)'></i>"
                        + "</div>";
                    $("#ShowProducts").append(prodApend)
                    prodCount++;
                })
            }
        }else{
        }
    }).then(() => {
        searching = 0;
        setTimeout(() => {
            $(".loadingProducts").css("display", "none");
        }, 510);
    })
}


//*  Category
function getCategory() {
    $.get("/api/get/getCategory.php", function (categories) {
        $.each(categories, function (i, cat) {
            $("#SearchCategory").append("<option value='" + cat["id"] + "'>" + cat["name"] + "</option>");
        })
    })
}

function setBanner(el_id, banner_name) {
    //"/api/getBanner.php"
    return $.get("/api/get/getBanner.php", { 'name': banner_name }, function (data) {
        if (data.status >= 200 && data.status < 300) {
            console.log(data)
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
    if (searching == 0) {
        console.log("pageDown")
        page++;
        search(0);
    } else {
        console.log("Produtos em busca")

    }
}
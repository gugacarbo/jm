

var bannerImg = {};

var actBannerImg = 0;

function bannerStart(img, speed = 5000) {
    bannerImg = img;
    img.forEach((i, k) => {
        $(".dots").append("<div class='dot' onclick='bannerSetImg(bannerImg[" + k + "], " + k + ")'></div>")
    })
    bannerSetImg(img[0]);

    var scroll = setInterval(scrollF, speed);
    var stpScroll = null;
    $("#bannerLeftBtn").on("click", () => {
        actBannerImg == 0 ? actBannerImg = bannerImg.length - 1 : actBannerImg--;
        bannerScroll(1, bannerImg[actBannerImg])




    })
    $("#bannerRightBtn").on("click", () => {
        actBannerImg == bannerImg.length - 1 ? actBannerImg = 0 : actBannerImg++;
        bannerScroll(0, bannerImg[actBannerImg])
         clearInterval(scroll);

    })

    
}

var scrollF = () => {
    actBannerImg == bannerImg.length - 1 ? actBannerImg = 0 : actBannerImg++;
    actBannerImg == bannerImg.length ? bannerScroll(1, bannerImg[actBannerImg]) : bannerScroll(0, bannerImg[actBannerImg]);
    
};

function bannerSetImg(img, indexI = 0) {
    var dot = "div .dot:nth-child(" + (indexI + 1) + ")"
    $(".dot").removeClass("dotActive");
    $(dot).addClass("dotActive")
    actBannerImg = indexI;
    $("#mainImg").fadeTo(400, 0, () => {
        $("#mainImg img").attr("src", img);
        $("#mainImg").fadeTo(400, 1)
    })
}

function bannerScroll(banner_LR, actimg) {
    var dot = "div .dot:nth-child(" + (actBannerImg + 1) + ")"
    $(".dot").removeClass("dotActive");
    $(dot).addClass("dotActive")
    banner_LR = !banner_LR;
    $("#mainImg").css("z-index", "1");
    $(banner_LR == 0 ? "#leftImg img" : "#rightImg img").attr("src", actimg);
    $(banner_LR == 0 ? "#leftImg" : "#rightImg").css("z-index", "3")
    $(banner_LR == 0 ? "#leftImg" : "#rightImg").css("transform", "translateX(0)");

    setTimeout(() => {
        $("#mainImg img").attr("src", actimg);
        $("#mainImg").css("z-index", "4");
        $(banner_LR == 0 ? "#leftImg" : "#rightImg").css("transform", banner_LR == 0 ? "translateX(-100%)" : "translateX(100%)");

        $(banner_LR == 0 ? "#leftImg" : "#rightImg").css("z-index", "2")
    }, 600);
}
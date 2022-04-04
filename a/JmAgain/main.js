
$(document).ready(function () {
    $("#Toggle").on("click", () => {
        $("#Toggle").toggleClass("on");
        $(".menuResp").toggleClass("menuOn");

    })

    var img = ['https://wallpaperaccess.com/full/346725.jpg',
        "https://c4.wallpaperflare.com/wallpaper/214/442/543/digital-art-son-goku-dragon-ball-dragon-ball-z-island-hd-wallpaper-preview.jpg",
        "https://wallpaperaccess.com/full/709186.png",
        "https://c4.wallpaperflare.com/wallpaper/31/105/276/retrowave-synthwave-neon-ultrawide-wallpaper-thumb.jpg"
    ]
    bannerStart(img, 3000);
})





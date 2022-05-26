var toDelete = [];

$(document).ready(function () {

    $.get("/api/get/getBanner.php", { name: "MAIN_BANNER" }, function (data) {
        
        $.each(data.images, function (i, value) {
            $("#Banner1File" + (i) + " + img").attr("src", value || "img/noImage.png");
            $("#Banner1File" + (i) + " + img + i").attr("value", value);
            $("#Banner1File" + (i) + " + img + i + input[type='hidden']").val(value);
        })
    })
    $.get("/api/get/getBanner.php", { name: "PRODUCTS_BANNER" }, function (data) {
        

        $.each(data.images, function (i, value) {
      

            $("#Banner2File" + (i) + " + img").attr("src", value || "img/noImage.png");
            $("#Banner2File" + (i) + " + img + i").attr("value", value);
            $("#Banner2File" + (i) + " + img + i + input[type='hidden']").val(value);
        })
    })
    $.get("/api/get/getBanner.php", { name: "ABOUT_BANNER" }, function (data) {
        
        $.each(data.images, function (i, value) {
            $("#Banner3File" + (i) + " + img").attr("src", value || "img/noImage.png");
            $("#Banner3File" + (i) + " + img + i").attr("value", value);
            $("#Banner3File" + (i) + " + img + i + input[type='hidden']").val(value);
        })
    }).then((value) => {
        setTimeout(() => {
            preview()
            preview()
            preview()
            preview()
            setInterval(preview, 2000);
        }, 300);
    })


    $("#but_uploadBanners").click(function () {

        var banners = {
            "MAIN_BANNER": [$("#Banner1Image1").val(), $("#Banner1Image2").val(), $("#Banner1Image3").val()],
            "PRODUCTS_BANNER": [$("#Banner2Image1").val(), $("#Banner2Image2").val(), $("#Banner2Image3").val()],
            "ABOUT_BANNER": [$("#Banner3Image1").val(), $("#Banner3Image2").val(), $("#Banner3Image3").val()],
        }

        $.ajax({
            url: "/admin/api/post/editBanners.php",
            type: "POST",
            data: {
                banners: banners
            },
            success: function (data) {
                if(data.status >= 200 && data.status < 300){
                    $("#but_uploadBanners").addClass("doneButton")
                   setTimeout(() => {
                       $(".doneButton").removeClass("doneButton")
                   }, 1500);
               } else {
                   alert(data.message);
                   $("#but_uploadBanners").addClass("alertButton")
                   setTimeout(() => {
                       $(".alertButton").removeClass("alertButton")
                   }, 1500);
               }
            }
        })

        $.each(banners, function (i, value) {
            $.each(value, function (i, x) {
                const index = toDelete.indexOf(x);
                if (index > -1) {
                    toDelete.splice(index, 1);
                    console.log(x)
                }
            })
        })

        $.each(toDelete, function (i, value) {
            $.ajax({
                url: "/admin/api/file/delete.php",
                type: "POST",
                data: {
                    file: value
                },
                success: function (data) {
                }
            })
        })

    })


    $(".fa-trash").on("click", function () {
        var file = $(this).attr("value");
        toDelete.push(file);
        $("img[src='" + file + "']").attr("src", "img/noImage.png");
        $("input[value='" + file + "']").val("");
    })


    $("input:file").change(function () {
        var fd = new FormData();
        var files = $(this)[0].files;

        if (files.length > 0) {

            fd.append('file', files[0]);

            var fileId = ($(this).attr("id"))
            var actImg = ($("#" + fileId + " + img + i + input[type='hidden']").val());
            var dir = "/img/banners/";

            $("#" + fileId + " + img + i + input[type='hidden']").val("");
            var prevTarget = $(this);
            $.ajax({
                beforeSend: function () {
                    
                    $(prevTarget).parent().addClass("loadingImage");
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = (evt.loaded / evt.total) * 100;
                            //Do something with upload progress here

                        }
                    }, false);
                    return xhr;
                },
                url: '/admin/api/file/upload.php?dir=' + dir,
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response["status"] >= 200 && response["status"] < 300) {
                        $("#" + fileId + " + img").attr("src", response["location"]);
                        $("#" + fileId + " + img").show();
                        $("#" + fileId + " + img + i").attr("value", response["location"]);
                        $("#" + fileId + " + img + i + input[type='hidden']").val(response["location"]);
                        toDelete.push(response["location"]);
                        toDelete.push(actImg);
                    } else {
                        alert(response["message"]);
                    }
                    $(prevTarget).parent().removeClass("loadingImage");
                },
                error: function (response) {
                    $(prevTarget).parent().removeClass("loadingImage");
                    alert("Error", "Erro Ao Enviar Imagem", "error");
                }
            });

        } else {
        }

    });
});


var swtichPrev = 0;
function preview() {
    var prevImages = [
        [$("#Banner1File1 + img").attr("src"), $("#Banner1File2 + img").attr("src"), $("#Banner1File3 + img").attr("src")],
        [$("#Banner2File1 + img").attr("src"), $("#Banner2File2 + img").attr("src"), $("#Banner2File3 + img").attr("src")],
        [$("#Banner3File1 + img").attr("src"), $("#Banner3File2 + img").attr("src"), $("#Banner3File3 + img").attr("src")]
    ]
    switch (swtichPrev) {
        case 0:
            $("#imgMain img:nth-child(1)").attr("src", prevImages[0][0] || "img/noImage.png");
            $("#imgProd img:nth-child(1)").attr("src", prevImages[1][0] || "img/noImage.png");
            $("#imgAbout img:nth-child(1)").attr("src", prevImages[2][0] || "img/noImage.png");
            $(".prev1Img").css("z-index", "5");
            $(".prev2Img").css("z-index", "1");
            $(".prev3Img").css("z-index", "1");

            swtichPrev = 1;
            break;
        case 1:
            $("#imgMain img:nth-child(2)").attr("src", prevImages[0][1] || "noImage.png");
            $("#imgProd img:nth-child(2)").attr("src", prevImages[1][1] || "img/noImage.png");
            $("#imgAbout img:nth-child(2)").attr("src", prevImages[2][1] || "img/noImage.png");
            $(".prev1Img").css("z-index", "1");
            $(".prev2Img").css("z-index", "5");
            $(".prev3Img").css("z-index", "1");

            swtichPrev = 2;
            break;
        case 2:
            $("#imgMain img:nth-child(3)").attr("src", prevImages[0][2] || "img/noImage.png");
            $("#imgProd img:nth-child(3)").attr("src", prevImages[1][2] || "img/noImage.png");
            $("#imgAbout img:nth-child(3)").attr("src", prevImages[2][2] || "img/noImage.png");
            $(".prev1Img").css("z-index", "1");
            $(".prev2Img").css("z-index", "1");
            $(".prev3Img").css("z-index", "5");
            swtichPrev = 0;
            break;
        default:
            swtichPrev = 0;
            break;
    }
}

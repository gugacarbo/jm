var toDelete = [];

$(document).ready(function () {

    $.get("/php/getBanner.php", { name: "MAIN_BANNER" }, function (data) {
        data = JSON.parse(data);
        var images = JSON.parse(data.images);
        $.each(images, function (i, value) {
            $("#Banner1File" + (i) + " + img").attr("src", value || "noImage.png");
            $("#Banner1File" + (i) + " + img + i").attr("value", value);
            $("#Banner1File" + (i) + " + img + i + input[type='hidden']").val(value);
        })
    })
    $.get("/php/getBanner.php", { name: "PRODUCTS_BANNER" }, function (data) {
        data = JSON.parse(data);
        
        var images = JSON.parse(data.images);
        $.each(images, function (i, value) {
            console.log(value);

            $("#Banner2File" + (i) + " + img").attr("src", value || "noImage.png");
            $("#Banner2File" + (i) + " + img + i").attr("value", value);
            $("#Banner2File" + (i) + " + img + i + input[type='hidden']").val(value);
        })
    })
    $.get("/php/getBanner.php", { name: "ABOUT_BANNER" }, function (data) {
        data = JSON.parse(data);
        var images = JSON.parse(data.images);
        $.each(images, function (i, value) {
            $("#Banner3File" + (i) + " + img").attr("src", value|| "noImage.png");
            $("#Banner3File" + (i) + " + img + i").attr("value", value);
            $("#Banner3File" + (i) + " + img + i + input[type='hidden']").val(value);
        })
    })


    $("#but_upload").click(function () {

        var banners = {
            "MAIN_BANNER": [$("#Banner1Image1").val(), $("#Banner1Image2").val(), $("#Banner1Image3").val()],
            "PRODUCTS_BANNER": [$("#Banner2Image1").val(), $("#Banner2Image2").val(), $("#Banner2Image3").val()],
            "ABOUT_BANNER": [$("#Banner3Image1").val(), $("#Banner3Image2").val(), $("#Banner3Image3").val()],
        }

        $.ajax({
            url: "upload_banners.php",
            type: "POST",
            data: {
                banners: banners
            },
            success: function (data) {

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
                url: "/admin/file/delete.php",
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
        $("img[src='" + file + "']").attr("src", "noImage.png");
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
            
            $.ajax({
                beforeSend: function () {
                },
                url: '/admin/file/upload.php?dir=' + dir,
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response["status"] == "success") {
                        $("#" + fileId + " + img").attr("src", response["location"]);
                        $("#" + fileId + " + img").show();
                        $("#" + fileId + " + img + i").attr("value", response["location"]);
                        $("#" + fileId + " + img + i + input[type='hidden']").val(response["location"]);
                        toDelete.push(response["location"]);
                        toDelete.push(actImg);
                    } else {
                        $("#" + fileId + " + img").attr("src", "");
                        $("#" + fileId + " + img").show();
                        $("#" + fileId + " + img + i").attr("value", "");
                        $("#" + fileId + " + img + i + input[type='hidden']").val("");
                        alert(response["message"]);
                    }
                },
            });

        } else {
            alert("Please select a file.");
        }

    });
});
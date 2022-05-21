$(document).ready(function () {
    $("textarea").jqte();

    $.get("/about/aboutFile.txt?" + new Date().getTime(), function (response) {
        var text = response;
        $("textarea").jqteVal(text);
    });
    $.get("/about/mapLink.txt", function (response) {
        var text = response;
        $("#EditMap").val(text);
    });
    $(("#DeleteAboutImage")).click(function () {
        $.post("/admin/api/file/delete.php", { file: "/about/aboutImage.jpg" }, function (response) {
            var text = response;
            $("#AboutImageFile + img").attr("src", "/img/noImage.png");
        });
    })

    $("#SaveAdminAbout").click(function () {
        var fd = new FormData();
        var content = $("textarea").val()
        content = content.replace("script", "");
        var blob = new Blob([content], { type: "text/plain" });
        fd.append("file", blob, "aboutFile.txt");

        var dir = "/about/"
        $.ajax({
            beforeSend: function () {
            },
            url: '/admin/api/file/upload.php?md5=false&dir=' + dir,
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function (d) {
                if (d.status >= 200 && d.status < 300) {

                    var mapsL = new FormData();
                    var contentMap = $("#EditMap").val()

                    var blobMap = new Blob([contentMap], { type: "text/plain" });
                    fd.append("file", blobMap, "mapLink.txt");
                    $.ajax({
                        beforeSend: function () {
                        },
                        url: '/admin/api/file/upload.php?md5=false&dir=' + dir,
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            $(".doneButton").removeClass("doneButton")
                            $(".alertButton").removeClass("alertButton")
                            if (response.status >= 200 && response.status < 300) {
                                $("#SaveAdminAbout").addClass("doneButton")
                                setTimeout(() => {
                                    $(".doneButton").removeClass("doneButton")
                                }, 1500);
                            } else {
                                alert(data.message);
                                $("#SaveAdminAbout").addClass("alertButton")
                                setTimeout(() => {
                                    $(".alertButton").removeClass("alertButton")
                                }, 1500);
                            }
                        }
                    })
                }
            }
        })
    })
    $("input:file").change(function () {
        var fd = new FormData();
        var files = $(this)[0].files;

        if (files.length > 0) {
            fd.append('file', files[0], "aboutImage.jpg");
            var dir = "/about/";
            var prevTarget = $(this);
            $.ajax({
                beforeSend: function () {
                    $(prevTarget).parent().addClass("loadingImage");
                },
                url: '/admin/api/file/upload.php?md5=false&dir=' + dir,
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response["status"] >= 200 && response["status"] < 300) {
                        $("#AboutImageFile + img").attr("src", "/img/noImage.png");
                        setTimeout(() => {
                            $("#AboutImageFile + img").attr("src", response["location"] + "?" + new Date().getTime());
                        }, 100);
                    } else {
                        alert("Erro Ao Carregar Imagem")
                    }
                },
            }).then((value) => {
                $(prevTarget).parent().removeClass("loadingImage");
            })
        } else {
        }

    });
})



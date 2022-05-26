$(document).ready(function () {

    $("textarea").jqte();

    $.get("/about/aboutFile.txt?" + new Date().getTime(), function (response) {
        $("textarea").jqteVal(response);
    })

    $.get("/about/mapLink.txt?", function (response) {
        $("#EditMap").val(response);
        $("#MapL").html(response);
        $("#UseMaps").attr("checked", true);
    }).catch(function () {
        $("#MapL").hide();

    });

    $.get("/about/aboutImage.jpg?").then(function (response) {
        $("#aboutImg img").attr("src", "/about/aboutImage.jpg?noCache=" + new Date().getTime());
        $("#UseBottomBanner").attr("checked", true);
    }).catch((value) => {
        $("#aboutImg").hide();
    })

    $(("#DeleteMapLink")).click(function () {
        $("#EditMap").val("");
        $("#MapL").html('');

    })

    $(("#DeleteAboutImage")).click(function () {
        $("#AboutImageFile").val('')
        $("#AboutImageFile + img").attr("src", "/img/noImage.png?" + new Date().getTime());
    })

    $("#UseBottomBanner").on("change", function () {
        if ($(this).is(":checked")) {
            $("#aboutImg").show();
        } else {
            $("#aboutImg").hide();
        }
    })
    $("#UseMaps").on("change", function () {
        if ($(this).is(":checked")) {
            $("#MapL").show();
        } else {
            $("#MapL").hide();
        }
    })





    $("#SaveAdminAbout").click(function () {

        //? If usinf Bottom Banner
        if (!$("#UseBottomBanner").is(":checked")) {

            //! no use image, deleting
            $.post("/admin/api/file/delete.php", { file: "/about/aboutImage.jpg" });

        } else {

            //* Uploading image
            var fd = new FormData();
            var files = $("#AboutImageFile")[0].files;

            if (files.length > 0) {
                fd.append('file', files[0], "aboutImage.jpg");
                var dir = "/about/";
                $.ajax({
                    url: '/admin/api/file/upload.php?md5=false&dir=' + dir,
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response["status"] >= 200 && response["status"] < 300) {
                        } else {
                            alert("Erro Ao Carregar Imagem")
                        }
                    },
                })
            }else{
                $("#UseBottomBanner").prop("checked", false);
                $("#aboutImg").hide();
                $.post("/admin/api/file/delete.php", { file: "/about/aboutImage.jpg" })
            }
        }

        //x Delete support Image
        $.post("/admin/api/file/delete.php", { file: "/about/aboutImage_.jpg" })


        //? If using Maps
        if ($("#UseMaps").is(":checked")) {

            //* Uploading Map Link
            var fd = new FormData();
            var contentMap = $("#EditMap").val()
            if (contentMap != "") {
                var blobMap = new Blob([contentMap], { type: "text/plain" });
                fd.append("file", blobMap, "mapLink.txt");
                var dir = "/about/";
                
                $.ajax({
                    url: '/admin/api/file/upload.php?md5=false&dir=' + dir,
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function (response) {

                        if (response.status >= 200 && response.status < 300) {
                            $("#MapL").html($("#EditMap").val());
                        }
                    }
                })
            }else{
                $("#UseMaps").prop("checked", false);
                $("#MapL").hide();
                $.post("/admin/api/file/delete.php", { file: "/about/mapLink.txt" });
            }
        } else {
            //! Delete Map Link
            $.post("/admin/api/file/delete.php", { file: "/about/mapLink.txt" });
        }


        //* Uploading About File
        var fd = new FormData();
        var content = $("textarea").val()
        content = content.replace("script", "");
        var blob = new Blob([content], { type: "text/plain" });
        fd.append("file", blob, "aboutFile.txt");
        var dir = "/about/"
        $.ajax({
            url: '/admin/api/file/upload.php?md5=false&dir=' + dir,
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
        }).then(function (response) {
            $(".doneButton").removeClass("doneButton")
            $(".alertButton").removeClass("alertButton")
            $("#SaveAdminAbout").addClass("doneButton")
            setTimeout(() => {
                $("#SaveAdminAbout").removeClass("doneButton")
            }, 1500);

        })
    })



    $("#AboutImageFile").change(function () {
        var fd = new FormData();
        var files = $(this)[0].files;

        if (files.length > 0) {
            fd.append('file', files[0], "aboutImage_.jpg");
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



$(document).ready(function () {
    $("textarea").jqte();
    $.get("/about/aboutFile.txt", function (response) {
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
            $("#AboutImageFile + img").attr("src", "img/noImage.png");
        });
    })

    $("#SaveAdminAbout").click(function () {
        var fd = new FormData();
        var content = $("textarea").val()
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
            success: function (response) {
            }
        })

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
                    $(prevTarget).parent().addClass("loading");
                },
                url: '/admin/api/file/upload.php?md5=false&dir=' + dir,
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response["status"] == 200) {
                        $("#AboutImageFile + img").attr("src", "");
                        $("#AboutImageFile + img").attr("src", response["location"] + "?" + new Date().getTime());
                    } else {
                    }
                },
            }).then((value) => {
                $(prevTarget).parent().removeClass("loading");

            })

        } else {
            alert("Please select a file.");
        }

    });
})
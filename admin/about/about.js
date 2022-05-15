$(document).ready(function () {
    $("textarea").jqte();
    $.get("/about/aboutFile.html", function (response) {
        var text = response;

        $("textarea").jqteVal(text);
    });
    $.get("/about/mapLink.html", function (response) {
        var text = response;

        $("#EditMap").val(text);
    });
    $(("#DeleteAboutImage")).click(function () {
        $.post("../file/delete.php", { file: "/about/aboutImage.jpg" }, function (response) {
            var text = response;
            $("#AboutImageFile + img").attr("src", "noImage.png");
        });
    })

    $("#save").click(function () {
        var fd = new FormData();
        var content = $("textarea").val()
        var blob = new Blob([content], { type: "text/xml" });
        fd.append("file", blob, "aboutFile.html");

        var dir = "/about/"
        $.ajax({
            beforeSend: function () {
            },
            url: '/admin/file/upload.php?md5=false&dir=' + dir,
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
            }
        })

        var mapsL = new FormData();
        var contentMap = $("#EditMap").val()

        var blobMap = new Blob([contentMap], { type: "text/xml" });
        fd.append("file", blobMap, "mapLink.html");
        $.ajax({
            beforeSend: function () {
            },
            url: '/admin/file/upload.php?md5=false&dir=' + dir,
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
                url: '/admin/file/upload.php?md5=false&dir=' + dir,
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response["status"] == "success") {
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
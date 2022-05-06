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
    $("#save").click(function () {
        var fd = new FormData();
        var content = $("textarea").val()
        var blob = new Blob([content], { type: "text/xml" });
        fd.append("file", blob,  "aboutFile.html");
        
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
        fd.append("file", blobMap,  "mapLink.html");
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

            $.ajax({
                beforeSend: function () {
                },
                url: '/admin/file/upload.php?md5=false&dir=' + dir,
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response["status"] == "success") {
                        $("#NewProductFile1 + img").attr("src", response["location"]);
                    } else {
                    }
                },
            });

        } else {
            alert("Please select a file.");
        }

    });
})
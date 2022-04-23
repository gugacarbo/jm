var toDelete = [];

$(document).ready(function () {
    $.get("/php/getCategory.php", function (data) {
        var category = JSON.parse(data);
        //category foreach
        $.each(category, function (index, value) {

            var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';

            $("#NewProductCategory").append(item);
        })
    })
    $.get("../getMaterial.php", function (data) {
        var material = JSON.parse(data);
        //category foreach
        $.each(material, function (index, value) {

            var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';

            $("#NewProductMaterial").append(item);
        })
    })


    if ($.urlParam('id')) {
        $.get("getProdById.php", { id: $.urlParam('id') }, function (data) {
            data = JSON.parse(data);
            console.log(data);
            $("#NewProductName").val(data['name']);
            if (data['promo'] == 0) {
                $("#NewProductPrice").val(data['price']);
            } else {
                $("#NewProductPrice").val(data['promo']);
                $("#NewProductPromo").val(data['price']);
            }
            $("#NewProductCost").val(data['cost']);
            $("#NewProductWeight").val(data['weight']);
            $("#NewProductDescription").html(data['description']);
            $("#NewProductMaterial option[name='" + data['material'] + "']").attr("selected", "selected");
            $("#NewProductCategory option[name='" + data['category'] + "']").attr("selected", "selected");
            /**
             *              <div class="item">
            <input type="text" value="" >
            <input type="text" value="" >
            <i class="fa-solid fa-pen" id="addMat"></i>
            <i class="fa-solid fa-trash" id="addMat"></i>
        </div>
             */
            var options = JSON.parse(data['options']);
            $.each(options, function (index, value) {

                var item = '<div class="item">'
                    + '<input type="text" value="' + index + '"  >'
                    + '<input type="text" value="' + value + '"  >'
                    + '<i class="fa-solid fa-pen"></i>'
                    + '<i class="fa-solid fa-trash"></i>'
                    + '</div>';
                $("#OptionsList").append(item);

            })

            var images = JSON.parse(data['imgs']);
            $.each(images, function (index, value) {

                var item = '<div class="item">'
                    + '<img src="' + value + '" >'
                    + '<i class="fa-solid fa-trash"></i>'
                    + '</div>';

                $("#NewProductFile" + index + " + img").attr("src", value);
                $("#NewProductFile" + index + " + img").show();
                $("#NewProductFile" + index + " + img + i").attr("value", value);
                $("#NewProductFile" + index + " + img + i + input[type='hidden']").val(value);


            })
        })
    }

    $("#save").click(function () {

        var prodImages = {
            "1": $("#NewProductImage1").val(),
            "2": $("#NewProductImage2").val(),
            "3": $("#NewProductImage3").val()
        }
        $.each(prodImages, function (i, x) {
            const index = toDelete.indexOf(x);
            if (index > -1) {
                toDelete.splice(index, 1);
                console.log(x)
            }
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

        var prodOptions = {};
        $.each($("#OptionsList").find(".item"), function (i, x) {
            if (i != "")
                prodOptions[$(x).find("input:eq(0)").val()] = $(x).find("input:eq(1)").val();
        })

        var productPrice;
        var productPromo;
        if ($("#NewProductPromo").val() != 0) {
            productPrice = $("#NewProductPromo").val();
            productPromo = $("#NewProductPrice").val();
        } else {
            productPrice = $("#NewProductPrice").val();
            productPromo = $("#NewProductPromo").val();

        }

        var newProduct = {
            name: $("#NewProductName").val(),
            price: productPrice,
            promo: productPromo || 0,
            cost: $("#NewProductCost").val(),
            description: $("#NewProductDescription").val(),
            material: $("#NewProductMaterial").val(),
            category: $("#NewProductCategory").val(),
            weight: $("#NewProductWeight").val(),
            options: prodOptions,
            imgs: (prodImages)
        }
        console.log(newProduct);
        $.ajax({
            url: "/admin/product/save.php?id=" + $.urlParam('id'),
            type: "POST",
            data: {
                product: newProduct
            },
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
            }
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
            var dir = "/img/products/";

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

$("#addOpt").click(function () {
    var newOpt = $("#newOptName").val();
    var newOptQ = $("#newOptQuantity").val();

    var item = '<div class="item">'
        + '<input type="text" value="' + newOpt + '"  >'
        + '<input type="text" value="' + newOptQ + '"  >'
        + '<i class="fa-solid fa-trash" onclick="deleteOpt(' + "'" + newOptQ + "'" + ')\"></i>'
        + '</div>';

    $("#OptionsList").append(item);
    $("#newOptName").val("");
    $("#newOptQuantity").val("");

})



function deleteOpt(n) {

    $("#OptionsList").find("[value='" + n + "']").parent().remove();


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
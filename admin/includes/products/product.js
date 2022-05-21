var toDelete = [];

$(document).ready(function () {

    $("#newOptQuantity").mask("0000 un.", { reverse: true });
    $("#NewProductPrice").mask("0000,0#", { reverse: true });
    $("#NewProductPromo").mask("0000,0#", { reverse: true });
    $("#NewProductCost").mask("0000,0#", { reverse: true });
    $("#NewProductWeight").mask("0000");

    $.get("/admin/api/get/getCategory.php", function (category) {
        $.each(category, function (index, value) {
            var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';
            $("#NewProductCategory").append(item);
        })
    })
    $.get("/admin/api/get/getMaterial.php", function (material) {
        $.each(material, function (index, value) {
            var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';
            $("#NewProductMaterial").append(item);
        })
    })


    $("#saveProduct").click(function () {
        var id = $(this).attr("name");
        id = id || 0;
        var prodImages = {
            "1": $("#NewProductImage1").val(),
            "2": $("#NewProductImage2").val(),
            "3": $("#NewProductImage3").val(),
            "4": $("#NewProductImage4").val()
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
                url: "/admin/api/file/delete.php",
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
            prodOptions[$(x).find("input:eq(0)").val()] = $(x).find("input:eq(1)").val().replace(/\s/g, '');
        })

        var productPrice;
        var productPromo;
        if ($("#NewProductPromo").val() != "") {
            productPrice = parseFloat($("#NewProductPromo").val().replace(",", "."));
            productPromo = parseFloat($("#NewProductPrice").val().replace(",", "."));
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
            weight: parseFloat($("#NewProductWeight").val()) / 1000,
            options: prodOptions,
            imgs: (prodImages)
        }
        if (newProduct.name == "") {
            $("#NewProductName").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductName").removeClass("invalidInput")
            }, 1000);
        }
        else if (newProduct.cost == "") {
            $("#NewProductCost").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductCost").removeClass("invalidInput")
            }, 1000);
        }
        else if (newProduct.category == 0) {
            $("#NewProductCategory").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductCategory").removeClass("invalidInput")
            }, 1000);
        }
        else if (newProduct.price == "") {
            $("#NewProductPrice").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductPrice").removeClass("invalidInput")
            }, 1000);
        }
        else if (newProduct.material == 0) {
            $("#NewProductMaterial").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductMaterial").removeClass("invalidInput")
            }, 1000);
        }
        else if (newProduct.weight == "") {
            $("#NewProductWeight").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductWeight").removeClass("invalidInput")
            }, 1000);
        }
        else if (newProduct.description == "") {
            $("#NewProductDescription").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductDescription").removeClass("invalidInput")
            }, 1000);
        } else if (Object.keys(newProduct.options).length == 0) {
            $("#newOptName").addClass("invalidInput")
            $("#newOptQuantity").addClass("invalidInput")
            setTimeout(() => {
                $("#newOptName").removeClass("invalidInput")
                $("#newOptQuantity").removeClass("invalidInput")
            }, 1000);
        } else if (newProduct.imgs[1] == "") {
            $("#NewProductFile1").parent().addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductFile1").parent().removeClass("invalidInput")
            }, 1000);
        } else if ((parseFloat(newProduct.promo) > 0 && (parseFloat(newProduct.price)) > parseFloat(newProduct.promo))) {
            $("#NewProductPrice").addClass("invalidInput")
            $("#NewProductPromo").addClass("invalidInput")
            setTimeout(() => {
                $("#NewProductPrice").removeClass("invalidInput")
                $("#NewProductPromo").removeClass("invalidInput")
            }, 1000);

        } else if (((parseFloat(newProduct.cost)) > parseFloat(newProduct.price))) {
            if (parseFloat(newProduct.promo) > 0) {
                $("#NewProductPrice").addClass("invalidInput")
                $("#NewProductCost").addClass("invalidInput")
                $("#NewProductPromo").addClass("invalidInput")
                setTimeout(() => {
                    $("#NewProductCost").removeClass("invalidInput")
                    $("#NewProductPrice").removeClass("invalidInput")
                    $("#NewProductPromo").removeClass("invalidInput")
                }, 1000);
            } else {
                $("#NewProductPrice").addClass("invalidInput")
                $("#NewProductCost").addClass("invalidInput")
                setTimeout(() => {
                    $("#NewProductPrice").removeClass("invalidInput")
                    $("#NewProductCost").removeClass("invalidInput")
                }, 1000);
            }


        } else {

            $.ajax({
                url: "/admin/api/post/postProduct.php",
                type: "POST",
                data: {
                    id: id,
                    product: newProduct
                },
                success: function (data) {
                    if (data.status >= 200 && data.status < 300) {
                        $("#saveProduct").addClass("doneButton")
                        setTimeout(() => {
                            $(".doneButton").removeClass("doneButton")
                        }, 1500);
                    } else {
                        alert(data.message);
                        $("#saveProduct").addClass("alertButton")
                        setTimeout(() => {
                            $(".alertButton").removeClass("alertButton")
                        }, 1500);
                    }
                }
            })
            search();
        }


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
            var prevTarget = $(this);

            $.ajax({
                beforeSend: function () {
                    $(prevTarget).parent().addClass("loadingImage");
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
                },
            }).then((value) => {
                $(prevTarget).parent().removeClass("loadingImage");
            })
        } else {
        }

    });
});


$(".toDeleteList").on("click", function () {
    var file = $(this).attr("value");
    toDelete.push(file);
    $("img[src='" + file + "']").attr("src", "img/noImage.png");
    $("input[value='" + file + "']").val("");
})

$("#closeModal").click(function () {
    $("#ModalProduct").css("display", "none");
    console.log("cls")
})

$("#addOpt").click(function () {
    var newOpt = $("#newOptName").val();
    var newOptQ = $("#newOptQuantity").val().replace("un.", '').replace(" ", '');
    var dup = 0;
    if (newOpt != "" && newOptQ != "" && parseInt(newOptQ) > 0) {
        $("#OptionsList").find(".item").each(function (i, x) {
            if ($(x).find("input:eq(0)").val() == newOpt) {
                $(x).find("input:eq(0)").addClass("invalidInput")
                setTimeout(() => {
                    $(x).find("input:eq(0)").removeClass("invalidInput")

                }, 1000);
                dup = 1;
            }
        })

        if (dup == 0) {

            var item = '<div class="item">'
                + '<input type="text" value="' + newOpt + '"  >'
                + '<input type="text" value="' + newOptQ + '"  >'
                + `<i class="fa-solid fa-trash" onclick="deleteOpt(this)\"></i>`
                + '</div>';

            $("#OptionsList").append(item);
            $("#newOptName").val("");
            $("#newOptQuantity").val("");
        }
    }
})



function deleteOpt(this_) {
    $(this_).parent().remove();

}




function modalProductShow(id = 0) {
    if (id > 0) {

        $.get("/admin/api/get/getProdById.php", { id }, function (data) {

            $("#NewProductName").val(data['name']);
            if (data['promo'] == 0) {
                $("#NewProductPrice").val(data['price'].toFixed(2).replace(/\./g, ','))
            } else {
                $("#NewProductPrice").val(data['promo'].toFixed(2).replace(/\./g, ','));
                $("#NewProductPromo").val(data['price'].toFixed(2).replace(/\./g, ','));
            }
            $("#NewProductCost").val(data['cost'].toFixed(2).replace(/\./g, ','));
            $("#NewProductWeight").val(data['weight'] * 1000);
            $("#NewProductDescription").html(data['description']);
            $("#NewProductMaterial option[name='" + data['material'] + "']").attr("selected", true);
            $("#NewProductCategory option").each(function (i, x) {
                if ($(x).val() == data['categoryId']) {
                    $(x).attr("selected", true);
                } else {
                    $(x).attr("selected", false);
                }
            })
            var options = (data['options']);


            $("#OptionsList").html("");
            $.each(options, function (index, value) {

                var item = '<div class="item">'
                    + '<input type="text" value="' + index + '"  >'
                    + '<input type="text" value="' + value + '"  >'
                    + '<i class="fa-solid fa-trash" onclick="deleteOpt(this)"></i>'
                    + '</div>';
                $("#OptionsList").append(item);

            })

            var images = (data['imgs']);
            $.each(images, function (index, value) {

                var item = '<div class="item">'
                    + '<img src="' + value + '" >'
                    + '<i class="fa-solid fa-trash"></i>'
                    + '</div>';

                $("#NewProductFile" + index + " + img + i").attr("value", value);
                $("#NewProductFile" + index + " + img").show();
                $("#NewProductFile" + index + " + img + i + input[type='hidden']").val(value);
                value = value || "img/noImage.png";
                $("#NewProductFile" + index + " + img").attr("src", value);
            })
            $("#saveProduct").attr("name", id);
        })
    } else {
        clearProduct();

    }
    $("#ModalProduct").css("display", "flex");


}

function clearProduct() {
    $("#NewProductName").val("");
    $("#NewProductPrice").val("");
    $("#NewProductPromo").val("");

    $("#NewProductCost").val("");
    $("#NewProductWeight").val("");
    $("#NewProductDescription").html("");
    $("#NewProductCategory option").attr("selected", false);
    $("#NewProductMaterial option").attr("selected", false);
    $("#NewProductCategory option[name='0']").attr("selected", "selected");
    $("#NewProductMaterial option[name='0']").attr("selected", "selected");


    $("#OptionsList").html("");


    for (var index = 1; index <= 4; index++) {
        $("#NewProductFile" + index + " + img + i").attr("value", '');
        $("#NewProductFile" + index + " + img").show();
        $("#NewProductFile" + index + " + img + i + input[type='hidden']").val('');
        $("#NewProductFile" + index + " + img").attr("src", "img/noImage.png");
        $("#saveProduct").attr("name", "");
    }
}
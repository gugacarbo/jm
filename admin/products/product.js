var toDelete = [];

$(document).ready(function () {

    $("#newOptQuantity").mask("0000 un.", { reverse: true });
    $("#NewProductPrice").mask("0000,0#", { reverse: true });
    $("#NewProductPromo").mask("0000,0#", { reverse: true });
    $("#NewProductCost").mask("0000,0#", { reverse: true });
    $("#NewProductWeight").mask("0,000", { reverse: false });


    $.get("/php/getCategory.php", function (data) {
        var category = JSON.parse(data);
        $.each(category, function (index, value) {
            var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';
            $("#NewProductCategory").append(item);
        })
    })
    $.get("../getMaterial.php", function (data) {
        var material = JSON.parse(data);
        $.each(material, function (index, value) {
            var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';
            $("#NewProductMaterial").append(item);
        })
    })


    $("#saveProduct").click(function () {
        $("#saveProduct").addClass("alertButton")

        setTimeout(() => {
            $("#saveProduct").removeClass("alertButton")

        }, 1500);
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
            prodOptions[$(x).find("input:eq(0)").val()] = $(x).find("input:eq(1)").val().replace(/\s/g, '');
        })

        var productPrice;
        var productPromo;
        if ($("#NewProductPromo").val() != "") {
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
        console.log(newProduct)

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
            $("#saveProduct").removeClass("alertButton")
            $("#saveProduct").addClass("doneButton")
            
            setTimeout(() => {
                $("#saveProduct").removeClass("doneButton")
            }, 1500);

            $.ajax({
                url: "saveProduct.php?id=" + id,
                type: "POST",
                data: {
                    product: newProduct
                },
                success: function (data) {
                    data = JSON.parse(data);
                    modalProductShow(data.id);
                    search();
                }
            })
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
                    $(prevTarget).parent().addClass("loading");
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
            }).then((value) => {
                $(prevTarget).parent().removeClass("loading");

            })

        } else {
            alert("Please select a file.");
        }

    });
});


$(".toDeleteList").on("click", function () {
    var file = $(this).attr("value");
    toDelete.push(file);
    $("img[src='" + file + "']").attr("src", "noImage.png");
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

        $.get("getProdById.php", { id }, function (data) {
            data = JSON.parse(data);
            $("#NewProductName").val(data['name']);
            if (data['promo'] == 0) {
                $("#NewProductPrice").val(data['price'].toFixed(2).replace(/\./g, ','))
            } else {
                $("#NewProductPrice").val(data['promo'].toFixed(2).replace(/\./g, ','));
                $("#NewProductPromo").val(data['price'].toFixed(2).replace(/\./g, ','));
            }
            $("#NewProductCost").val(data['cost'].toFixed(2).replace(/\./g, ','));
            $("#NewProductWeight").val(data['weight']);
            $("#NewProductDescription").html(data['description']);
            $("#NewProductMaterial option[name='" + data['material'] + "']").attr("selected", true);
            $("#NewProductCategory option[value='" + data['categoryId'] + "']").attr("selected", "selected");
            var options = JSON.parse(data['options']);
            console.log(data)

            $("#OptionsList").html("");
            $.each(options, function (index, value) {

                var item = '<div class="item">'
                    + '<input type="text" value="' + index + '"  >'
                    + '<input type="text" value="' + value + '"  >'
                    + '<i class="fa-solid fa-trash" onclick="deleteOpt(this)"></i>'
                    + '</div>';
                $("#OptionsList").append(item);

            })

            var images = JSON.parse(data['imgs']);
            $.each(images, function (index, value) {

                var item = '<div class="item">'
                    + '<img src="' + value + '" >'
                    + '<i class="fa-solid fa-trash"></i>'
                    + '</div>';

                $("#NewProductFile" + index + " + img + i").attr("value", value);
                $("#NewProductFile" + index + " + img").show();
                $("#NewProductFile" + index + " + img + i + input[type='hidden']").val(value);
                value = value || "noImage.png";
                $("#NewProductFile" + index + " + img").attr("src", value);
                $("#saveProduct").attr("name", id);

            })
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
        $("#NewProductFile" + index + " + img").attr("src", "noImage.png");
        $("#saveProduct").attr("name", "");
    }
}
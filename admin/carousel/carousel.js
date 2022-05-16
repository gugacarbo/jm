
var UsedGlider = []
var SelectedItens = []

var actImagePrev = 0;



$(document).ready(function () {
    $("body").append($("<div class='adminHeader'>").load("../header.html"));
    $("body").append($("<div class='adminMenu'>").load("../menu.html"));
    

    startGliders()


    $("#selectProdsButton").click(function () {
        if ($("#selectProdsButton").hasClass("cantOpenList")) {
        } else {
            $(".selectProds").css("display", "flex");
        }


    })

    $("#closeProdList").click(function () {
        SelectedItens.length > 0 ? updatePrevManual(SelectedItens) : ""

        $(".selectProds").hide()

    })


    $("#Categories").on("change", function () {
        var catI = $(this).val();
        takeCat(catI);

    })

    $("#addCarousel").on("click", function () {
        var cat = $("#Categories").val();
        var type = $("input[name='type']:checked").val();
        var selectAutoType = ($("#autoType").val());
        var category = cat;

        if (type == "id") {
            var SelectType = "id";
            var select = SelectedItens;
            SelectedItens = []
        } else if (type == "auto") {
            var SelectType = "auto";
            var select = selectAutoType;
        }

        $.get("createCarousel.php", { category: category, SelectType: SelectType, select: select }, function (data) {
            data = JSON.parse(data);
            if (data.status == "success") {
                alert("Carousel created");

                startGliders()
            }
        })
    })


    $("#autoType").on("change", function () {

        var cat = $("#Categories").val();
        var filter = $("#autoType").val();
        $("#selectProdsButton").addClass("cantOpenList")
        if (filter == "promo") {
            var query = {
                cat: cat,
                promo: 1,
            }
        } else {
            $("#autoType").val("price");
            var query = {
                cat: cat,
            }
        }

        $.get("getFiltered.php", query, function (data) {
            $("#radioPrice").attr("checked", true);

            data = JSON.parse(data);

            $("#prodList").empty();
            actImagePrev = 0;
            $.each(data, function (index, prod) {
                if (actImagePrev < 7) {
                    var images = JSON.parse(prod["imgs"]);
                    $(".preview .items .item")[actImagePrev].innerHTML = `<img src="${images[1]}">`
                    actImagePrev++
                }
                if (data.length < 7) {
                    for (let index = data.length; index < 7; index++) {
                        $(".preview .items .item")[index].innerHTML = `<img src="noImage.png">`
                    }
                }
            })
        }).then((value) => {
            $("#addCarousel").attr("disabled", false);
        })

    })

    // * ----------------

    $("input[name='type']").on("change", function () {
        //alert("Please select a category");
        var type = $(this).val();
        SelectedItens = [];
        var cat = $("#Categories").val();

        if (type == "auto") {
            $("#selectProdsButton").addClass("cantOpenList")
            $("#autoType").attr("disabled", false);
            if (UsedGlider.indexOf(parseInt(cat)) > -1) {

                var filter = $("#autoType").val();
                if (filter == "promo") {
                    var query = {
                        cat: cat,
                        promo: 1,
                    }
                } else {
                    $("#autoType").val("price");
                    var query = {
                        cat: cat,
                    }
                }


                $.get("getFiltered.php", query, function (data) {
                    $("#radioPrice").attr("checked", true);
                    data = JSON.parse(data);

                    $("#prodList").empty();
                    actImagePrev = 0;
                    $.each(data, function (index, prod) {
                        if (actImagePrev < 7) {
                            var images = JSON.parse(prod["imgs"]);
                            $(".preview .items .item")[actImagePrev].innerHTML = `<img src="${images[1]}">`
                            actImagePrev++
                        }
                        if (data.length < 7) {

                            for (let index = data.length; index < 7; index++) {
                                $(".preview .items .item")[index].innerHTML = `<img src="noImage.png">`

                            }
                        }
                    })
                })

                //!
            } else {
                auto();
            }
            $("#addCarousel").attr("disabled", false);
        } else { //? Manual

            $("#radioId").prop("checked", true);
            $("#autoType").attr("disabled", true);
            $("#prodList").empty();

            SelectedItens = [];

            $.get("getFiltered.php", { cat: cat }, function (r) {

                r = JSON.parse(r);
                $.each(r, function (index, value) {
                    var prod = (value);
                    var images = JSON.parse(prod["imgs"]);

                    //if id is in json select


                    var item = '<div>' +
                        '<input type="checkbox" name="selectedProducts" id="selectingProd' + prod["id"] + '" onclick="selectItems(this,' + prod["id"] + ')" >' +
                        '<img src="' + images[1] + '" alt="">' +
                        "</div>"
                    $("#prodList").append(item);


                })
            }).then(function () {
                if (UsedGlider.indexOf(parseInt(cat)) > -1) {
                    $.get("getAglider.php", { id: cat }, function (data) {
                        data = JSON.parse(data);
                        var select = JSON.parse(data["select"]);
                        actImagePrev = 0;
                        updatePrevManual(select);
                    })
                }
            }).then(function () {
                $("#selectProdsButton").removeClass("cantOpenList")
            })
            if (SelectedItens.length > 0) {
                $("#addCarousel").attr("disabled", false);
            } else {
                $("#addCarousel").attr("disabled", true);
            }
            $("input[name='type']").attr("disabled", false);
            $("checkbox[name='selectedProducts']").attr("disabled", false);
        }
    })
})


function auto() {

    $("input[name='type']").attr("disabled", true);
    $("#autoType").attr("disabled", true);
    $("#selectProdsButton").addClass("cantOpenList")

    var cat = $("#Categories").val();
    var catName = $("#Categories").find(":selected").text();
    var filter = $("#autoType").val("price");

    $("#prodList").empty();

    $.get("getFiltered.php", { cat }, function (data) {

        var glider = JSON.parse(data);;

        var createC = '<div class="carousel preview" >'
            + '<span class="name">' + catName + '</span>'
        createC += '<div class="items">'


        $.each(glider, function (index, data) {
            if (index < 7) {
                var images = JSON.parse(data["imgs"]);
                createC += '<div class="item"><img src="' + images[1] + '" alt=""></div>'
            }
        })
        if (glider.length < 7) {
            for (let index = 0; index < 7 - glider.length; index++) {
                createC += ('<div class="item"><img src="noImage.png" alt=""></div>')
            }
        }
        createC += '</div></div>'

        if (glider.length == 0) {
            alert("No products in this category")
            $("#Categories").val(0)
            resetPreview()

        } else {

            $(".previewBox").html(createC);

            $("input[name='type']").attr("disabled", false);
            $("#autoType").attr("disabled", false);
            $("#addCarousel").attr("disabled", false);
        }

    })

}


function resetPreview() {
    SelectedItens = [];

    if ($(".preview").attr("id")) {
        $(".preview").appendTo("#CarouselList");
        $(".preview").removeClass("preview")
    } else {

        $(".previewBox").html("");
    }
}



function editGlider(id_) {
    $(".selectProds").hide();
    $.get("getAglider.php", { id: id_ }, function (data) {
        data = JSON.parse(data);
        var glider = $("#Glider" + data.id);
        $(glider).addClass("preview")
        $(glider).appendTo(".previewBox");

        $("#prodList").empty();

        var select = JSON.parse(data["select"]);

        if (data["SelectType"] == "auto") {
            $("#selectProdsButton").addClass("cantOpenList")


            $("#radioAuto").prop("checked", true);
            if (select['type'] == "price") {
                $("#autoType").val('price');
            } else {
                $("#autoType").val('promo');
            }
            $("#autoType").attr("disabled", false);


        } else {
            $("#radioId").prop("checked", true);
            $("#autoType").attr("disabled", true);
            $("#addCarousel").attr("disabled", true);
            $("#prodList").empty();
            var id_ = $("#Categories").val();
            SelectedItens = [];

            $.get("getFiltered.php", { cat: id_ }, function (r) {

                r = JSON.parse(r);
                actImagePrev = 0;

                $.each(r, function (index, value) {
                    var prod = (value);
                    var images = JSON.parse(prod["imgs"]);

                    //if id is in json select


                    var item = '<div>' +
                        '<input type="checkbox" name="selectedProducts" id="selectingProd' + prod["id"] + '" onclick="selectItems(this,' + prod["id"] + ')" >' +
                        '<img src="' + images[1] + '" alt="">' +
                        "</div>"
                    $("#prodList").append(item);


                })
            }).then(function () {
                if (UsedGlider.indexOf(parseInt(id_)) > -1) {
                    $.get("getAglider.php", { id: id_ }, function (data) {
                        data = JSON.parse(data);
                        var select = JSON.parse(data["select"]);
                        actImagePrev = 0;
                        updatePrevManual(select);
                    })
                }
            }).then(function () {
                $("#selectProdsButton").removeClass("cantOpenList")

                if (SelectedItens.length > 0) {
                    $("#addCarousel").attr("disabled", false);
                } else {
                    $("#addCarousel").attr("disabled", true);
                }
            })
            $("checkbox[name='selectedProducts']").attr("disabled", false);
        }
        $("input[name='type']").attr("disabled", false);

    })

}




function takeCat(catI) {

    $("#Categories option[value=" + catI + "]").attr("selected", "selected");
    resetPreview()
    if (UsedGlider.indexOf(parseInt(catI)) > -1) {
        editGlider(catI);
    } else if (catI > 0) {
        $("#radioAuto").prop("checked", true);
        $("#autoType").val("price");
        auto();
    } else {
        $("#radioAuto").prop("disabled", true);
        $("#radioAuto").prop("checked", false);
        $("#autoType").attr("disabled", true);
        $("#addCarousel").attr("disabled", true);
    }
}








function startGliders() {

    $(".previewBox").html("");
    $("input[name='type']").attr("disabled", true);
    $("#autoType").attr("disabled", true);
    $("#addCarousel").attr("disabled", true);
    $("#Categories").val(0);
    $("#prodList").empty();
    $("#CarouselList").empty();
    UsedGlider = [];
    SelectedItens = [];

    $.get("/php/getGlider.php", function (data) {
        var gliders = JSON.parse(data);

        $.each(gliders, function (index, glider) {
            UsedGlider.push(glider['category']);

            var prods = JSON.parse(glider['prod_ids']);
            var carousel = '<div class="carousel" style="order:' + glider['id'] + ';" id="Glider' + glider['id'] + '">'
                + '<span class="name">' + glider["name"] + '</span>'
            carousel += '<div class="items"></div>'
            $("#CarouselList").append(carousel);


            $.each(prods, function (index, data) {
                var images = JSON.parse(data["imgs"]);
                $("#Glider" + glider['id'] + " .items").append('<div class="item"><img src="' + images[1] + '" alt=""></div>')
            })
            if (prods.length < 7) {
                for (let index = 0; index < 7 - prods.length; index++) {
                    $("#Glider" + glider['id'] + " .items").append('<div class="item"><img src="noImage.png" alt=""></div>')
                }
            }

            var sel = glider["SelectType"] == "auto" ? (glider["select"] == "price" ? "Menores Preços" : "Em Promoção") : "";
            var sType = glider["SelectType"] == "id" ? "Seleção Manual" : "Seleção Automática Por " + sel;

            $("#Glider" + glider['id']).append(`<div class="info">
                                            <span>Itens Selecionados por:</span>
                                            <span><b>${sType}</b></span>
                                            <span>Quantidade de Itens: <b>${prods.length}</b></span>
                                            <label>
                                            <span class="deleteGlider" onclick="takeCat(${glider.category})"><i class="fa-solid fa-edit"></i></span>
                                            <span class="deleteGlider" onclick="deleteGlider(` + glider['id'] + `)"><i class="fa-solid fa-trash-can"></i></span>
                                            </label>
                                            </div>`)
        })
    }).then((value) => {
        $.get("/php/getCategory.php", function (data) {
            var category = JSON.parse(data);
            $("#Categories").empty();
            var item = '<option value="0" >Selecionar Categoria</option>';
            $("#Categories").append(item);

            $.each(category, function (index, value) {
                var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';
                $("#Categories").append(item);
            })
        })
    })
}





function updatePrevManual(select) {
    actImagePrev = 0;
    SelectedItens = [];

    if (Array.isArray(select) && select.length > 0) {
        $.each(select, function (index, value) {
            $("#selectingProd" + value).prop("checked", true);
            SelectedItens.push(parseInt(value));
            $(".preview .items .item")[actImagePrev].innerHTML = `<img src="${$("#selectingProd" + value + " + img").attr("src")}" class="selecting">`
            actImagePrev++
        })
    }
    if (actImagePrev < 7) {
        var act = actImagePrev;
        for (let index = act; index < 7; index++) {
            $(".preview .items .item")[index].innerHTML = `<img src="noImage.png" class="selecting">`
            actImagePrev++
        }
    }
    if (SelectedItens.length > 0) {
        $("#addCarousel").attr("disabled", false);
    } else {
        $("#addCarousel").attr("disabled", true);
    }
}

function selectItems(this_, id) {

    if (SelectedItens.indexOf(id) == -1) {
        if (SelectedItens.length < 7) {
            SelectedItens.push(id);
        } else {
            alert("You can't select more than 7 !! items");
            $(this_).prop("checked", false);
        }
    } else {
        SelectedItens.splice(SelectedItens.indexOf(id), 1);
    }
    if (SelectedItens.length == 0) {
        $("#addCarousel").attr("disabled", true);
    }
}


function deleteGlider(id_) {
    if (confirm("Are you sure you want to delete this glider?")) {
        $.get("deleteGlider.php", { id: id_ }, function (data) {
            resetPreview();
            startGliders();
        })
    }
}
var UsedGlider = []
var Gliders = []
var SelectedItens = []
$(document).ready(function () {

    $("input[name='type']").attr("disabled", true);
    $("#autoType").attr("disabled", true);
    $("#addCarousel").attr("disabled", true);



    // * Inicialização
    startGliders();
    // * Fim Inicialização

    $("#Categories").on("change", function () {
        var catI = $(this).val();
        console.log(catI);
        if (UsedGlider.indexOf(parseInt(catI)) > -1) {
            editGlider(catI);

        } else {
            auto();
        }
        $("#radioAuto").prop("checked", true);
    })

    $("input[name='type']").on("change", function () {

        var type = $(this).val();
        SelectedItens = [];
        if (type == "auto") {
            auto();
        } else {
            $("#autoType").attr("disabled", true);
            $("#addCarousel").attr("disabled", true);

            var cat = $("#Categories").val();
            $.get("getFiltered.php", { cat: cat }, function (data) {
                data = JSON.parse(data);
                $("#prodList").empty();
                $.each(data, function (index, value) {
                    $.get("/php/getProdById.php", { id: value }, (p) => {
                        var prod = JSON.parse(p);
                        var images = JSON.parse(prod["imgs"]);
                        var item = '<div>' +
                            '<input type="checkbox" name="selectedProducts" onclick="selectItems(this,' + prod["id"] + ')">' +
                            '<img src="' + images[1] + '" alt="">' +
                            "</div>"
                        $("#prodList").append(item);
                    })
                })
            }).then((value) => {
                $("input[name='type']").attr("disabled", false);
                $("checkbox[name='selectedProducts']").attr("disabled", false);
            })
        }

    })

    $("#autoType").on("change", function () {
        auto();
    })


    $("#addCarousel").on("click", function () {
        var cat = $("#Categories").val();
        var type = $("input[name='type']:checked").val();
        console.log(cat);
        console.log(type);
        console.log(SelectedItens);
        var selectAutoType = ($("#autoType").val());
        if (type == "id") {
            var category = cat;
            var SelectType = "id";
            var select = SelectedItens;
            $.get("createCarousel.php", { category: category, SelectType: SelectType, select: select }, function (data) {
                console.log(data);
            }).then(() => {
                startGliders()
            })
        } else if (type == "auto") {
            var category = cat;
            var SelectType = "auto";
            var select = selectAutoType;
            console.log(select);
            $.get("createCarousel.php", { category: category, SelectType: SelectType, select: select }, function (data) {
                console.log(data);
            }).then(() => {
                startGliders()
            })
        }
        $("#prodList").empty();
        $("input[name='type']").attr("disabled", true);
        $("#autoType").attr("disabled", true);
        $("#addCarousel").attr("disabled", true);
        $("#Categories").val(0);

    })


})

function editGlider(id_) {
    $.get("getAglider.php", { id: id_ }, function (data) {
        data = JSON.parse(data);
        console.log(data)

        $("#prodList").empty();
        var select = JSON.parse(data["select"]);
        if (data["SelectType"] == "auto") {
            $("#radioAuto").prop("checked", true);
            if (select['type'] == "price") {
                $("#autoType").val('price');
            } else {
                $("#autoType").val('promo');
            }
            auto();
        } else {
            $("#radioId").prop("checked", true);

            $("#autoType").attr("disabled", true);
            $("#addCarousel").attr("disabled", true);
            SelectedItens = [];

            var cat = $("#Categories").val();
            $.get("getFiltered.php", { cat: cat }, function (r) {
                r = JSON.parse(r);
                $("#prodList").empty();
                $.each(r, function (index, value) {
                    $.get("/php/getProdById.php", { id: value }, (p) => {
                        var prod = JSON.parse(p);
                        var images = JSON.parse(prod["imgs"]);

                        //if id is in json select
                        var isIn = 0

                        $.each(select, function (index, value) {
                            if (value == prod["id"]) {
                                isIn = 1;
                                SelectedItens.push(parseInt(value));
                            }
                        })

                        var item = '<div>' +
                            '<input type="checkbox" name="selectedProducts" onclick="selectItems(this,' + prod["id"] + ')" ' + ((isIn == 1) ? 'checked' : '') + '>' +
                            '<img src="' + images[1] + '" alt="">' +
                            "</div>"
                        $("#prodList").append(item);
                    })
                })
            }).then((value) => {
                $("input[name='type']").attr("disabled", false);
                $("checkbox[name='selectedProducts']").attr("disabled", false);
            })
            console.log(SelectedItens)

        }
    })
}
function deleteGlider(id_) {
    if (confirm("Are you sure you want to delete this glider?")) {
        $.get("deleteGlider.php", { id: id_ }, function (data) {
            console.log(data);
        })
    }
}

function selectItems(this_, id) {

    if (SelectedItens.indexOf(id) == -1) {
        if (SelectedItens.length < 10) {

            SelectedItens.push(id);
        } else {
            alert("You can't select more than 10 items");
            $(this_).prop("checked", false);
        }
    } else {
        SelectedItens.splice(SelectedItens.indexOf(id), 1);
    }
    if (SelectedItens.length == 0) {
        $("#addCarousel").attr("disabled", true);
    } else {
        $("#addCarousel").attr("disabled", false);

    }
    console.log(SelectedItens);
}

function auto() {

    $("input[name='type']").attr("disabled", true);
    $("#autoType").attr("disabled", true);
    var cat = $("#Categories").val();
    var filter = $("#autoType").val();
    if (filter == "price") {
        var query = {
            cat: cat,
        }
    } else {
        var query = {
            cat: cat,
            promo: 1,
        }
    }

    $.get("getFiltered.php", query, function (data) {
        $("#radioPrice").attr("checked", true);
        data = JSON.parse(data);

        $("#prodList").empty();
        $.each(data, function (index, value) {
            $.get("/php/getProdById.php", { id: value }, (p) => {
                var prod = JSON.parse(p);
                var images = JSON.parse(prod["imgs"]);
                var item = '<div>' +
                    '<input type="checkbox" name="selectedProducts" value="' + prod["id"] + '" disabled>' +
                    '<img src="' + images[1] + '" alt="">' +
                    "</div>"
                $("#prodList").append(item);
            })
        })
    }).then((value) => {
        $("input[name='type']").attr("disabled", false);
        $("#autoType").attr("disabled", false);
        $("checkbox").attr("disabled", true);
        $("#addCarousel").attr("disabled", false);


    })

}

function startGliders() {

    $("#CarouselList").empty();
    $.get("/php/getGlider.php", function (data) {
        var gliders = JSON.parse(data);
        
        $.each(gliders, function (index, glider) {
            Gliders.push((glider))
            var ids = JSON.parse(glider['prod_ids']);
            var carousel = '<div class="carousel" id="Glider' + glider['id'] + '">'
                + '<span>' + glider["name"] + '</span>'
            carousel += '</div></div>'
            $("#CarouselList").append(carousel);
            UsedGlider.push(glider['category']);
            $.each(ids, function (index, id) {
                $.get("../product/getProdById.php", { id: id }, function (data) {
                    data = JSON.parse(data);
                    var images = JSON.parse(data["imgs"]);
                    $("#Glider" + glider['id']).append('<div><img src="' + images[1] + '" alt=""></div>')
                })
            })
            $("#Glider" + glider['id']).append('<span class="deleteGlider" onclick="deleteGlider(' + glider['id'] + ')">Del</span>')
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
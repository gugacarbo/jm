
var UsedGlider = []
var SelectedItens = []
var UsedSelectedItens = []
var actImagePrev = 0;

$(document).ready(function () {

    $.get("/api/get/getCategory.php", function (category) {
        $("#Categories").empty();
        var item = '<option value="0" >Selecionar Categoria</option>';
        $("#Categories").append(item);

        $.each(category, function (index, value) {
            var item = '<option value="' + value['id'] + '" name="' + value['name'] + '" >' + value['name'] + '</option>';
            $("#Categories").append(item);
        })
    })

    startGliders()
    $("#radioAuto").attr("disabled", true);
    $("#radioId").attr("disabled", true);
    $("#autoType").attr("disabled", true);

    $("#OpenCaroselModal").on("click", function () {
        $("#Categories").val(0);
        getModal()
        $('#Categories').trigger('mousedown')

    })
    $("#closeCarouselModal").on("click", function () {
        $("#AdminCarouselModal").css("display", "none");
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

        $.post("/admin/api/post/carousel.php", { category: category, SelectType: SelectType, select: select }, function (data) {
            if (data.status >= 200 && data.status < 300) {
                $("#addCarousel").addClass("doneButton")
                setTimeout(() => {
                    $("#addCarousel").attr("disabled", false);
                    $("#autoType").attr("disabled", false);
                    $("#radioAuto").attr("selected", "selected");
                    $("#ProdAddCount").css("display", "none")
                    $("#AdminCarouselModal").css("display", "none");
                    startGliders()
                    $(".doneButton").removeClass("doneButton")
                }, 1500);
            } else {
                //alert(data.message);
                $("#addCarousel").addClass("alertButton")
                setTimeout(() => {
                    $(".alertButton").removeClass("alertButton")
                }, 1500);
            }
        })
    })


    $("#closeProdList").click(function () {
        $("#modalProdList").css("display", "none")
        $("#ProdAddCount").css("display", "flex")
        $("#ProdAddCount b").html(SelectedItens.length)
        SelectedItens.length > 0 ? $("#addCarousel").attr("disabled", false) : $("#addCarousel").attr("disabled", true);
    })

    $("#radioAuto").on("click", function () {
        SelectedItens = []
        $("#addCarousel").attr("disabled", false);
        $("#autoType").attr("disabled", false);
        $("#ProdAddCount").css("display", "none")

    })
    $("#radioId").on("click", function () {
        SelectedItens = UsedSelectedItens
        $("#modalProdList").css("display", "flex");
        var catI = $("#Categories").val();
        $("#autoType").attr("disabled", true);

        callProds(catI);
    })

    $("#Categories").on("change", function () {
        $("#autoType").attr("selected", "selected");
        var catI = $(this).val();
        getModal(catI)
    })


})


function getModal(cat_) {
    $("#AdminCarouselModal").css("display", "flex");
    $("#ProdAddCount").css("display", "none")
    $("#radioAuto").attr("disabled", false);
    $("#radioId").attr("disabled", false);
    $("#autoType").attr("disabled", false);

    SelectedItens = []
    UsedSelectedItens = []
    $(".categoryAlert").hide();

    if (UsedGlider.indexOf(parseInt(cat_)) > -1) { // * Used
        
        $("#Categories").val(cat_);
        editGlider(cat_);

    } else if (cat_ > 0) { //? New

        $("#Categories").val(cat_);
        $("#radioAuto").prop("checked", true);
        $("#radioId").prop("checked", false);
        $("#addCarousel").attr("disabled", false);
        $("#autoType").val("price");
        $("#radioId").attr("disabled", true);
        $("#ProdAddCount").hide();


    } else { // ! Non
        $(".categoryAlert").show();
        $("#radioAuto").attr("disabled", true);
        $("#radioId").attr("disabled", true);
        $("#autoType").attr("disabled", true);
        $("#addCarousel").attr("disabled", true);
        $("#ProdAddCount").hide();
    }
}

function editGlider(cat_) {
    $.get("/admin/api/get/getAglider.php", { id: cat_ }, function (data) {
        if (data["SelectType"] == "id") {
            $("#radioId").prop("checked", true);
            var ids_ = (data["select"]);
            $.each(ids_, function (index, value) {
                UsedSelectedItens.push(parseInt(value));
            })
            $("#ProdAddCount").css("display", "flex")
            $("#ProdAddCount b").html(UsedSelectedItens.length)
        } else {
            $("#radioAuto").prop("checked", true)
            data["select"].type == "price" ? $("#autoType").val("price") : $("#autoType").val("promo")
            $("#addCarousel").attr("disabled", false);

        }
    })
}



function callProds(cat_) {
    $.get("/admin/api/get/getFiltered.php", { cat: cat_ }, function (r) {
        $("#prodList").empty();
        $.each(r, function (index, value) {
            var prod = (value);
            var images = JSON.parse(prod["imgs"]);
            var item = '<label>' +
                '<input type="checkbox" name="selectedProducts" ' + (UsedSelectedItens.indexOf(parseInt(prod["id"])) >= 0 ? "checked" : '') + ' id="selectingProd' + prod["id"] + '" onclick="selectItems(this,' + prod["id"] + ')" >' +
                '<img src="' + images[1] + '" alt="">' +
                "</label>"
            $("#prodList").append(item);

        })
    })
}





function startGliders() {
    UsedGlider = [];
    $("#CarouselList").empty();
    $.get("/api/get/getGlider.php", function (gliders) {
        $.each(gliders, function (index, glider) {
            UsedGlider.push(glider['category']);

            var prods = (glider['prod_ids']);
            var carousel = '<div class="carousel" style="order:' + glider['id'] + ';" id="Glider' + glider['id'] + '">'
                + '<span class="name">' + glider["name"] + '</span>'
            carousel += '<div class="items"></div>'
            $("#CarouselList").append(carousel);
            //Adiciona Carrossel

            $.each(prods, function (index, data) {
                var images = (data["imgs"]);
                $("#Glider" + glider['id'] + " .items").append('<div class="item"><img src="' + images[1] + '" alt=""></div>')
            })
            //Adiciona Imagens Dos Produtos

            if (prods.length < 7) {
                for (let index = 0; index < 7 - prods.length; index++) {
                    $("#Glider" + glider['id'] + " .items").append('<div class="item"><img src="img/noImage.png" alt=""></div>')
                }
            }
            //Completa com imagens de noImage

            var sel = glider["SelectType"] == "auto" ? (glider["select"] == "price" ? "Menores Preços" : "Em Promoção") : "";
            var sType = glider["SelectType"] == "id" ? "Seleção Manual" : "Seleção Automática Por " + sel;
            $("#Glider" + glider['id']).append(`<div class="Cinfo">
                                            <span>Itens Selecionados por:</span>
                                            <span><b>${sType}</b></span>
                                            <span>Quantidade de Itens: <b>${prods.length}</b></span>
                                            <label>
                                            <span class="deleteGlider" onclick="getModal(${glider.category})"><i class="fa-solid fa-edit"></i></span>
                                            <span class="deleteGlider" onclick="deleteGlider(` + glider['id'] + `)"><i class="fa-solid fa-trash-can"></i></span>
                                            </label>
                                            </div>`)
        })
        //Adiciona Informaçoes

    })
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
        //X Ajustar
    }
}




var timerCat;
function deleteGlider(id) {
    $(".deleteConfirm").remove();
    console.log("aaa")
    $("#Glider" + id).find(".info").find("label").find("span").next().append(`
        <div class="deleteConfirm">
        <div onclick='delCarouselConfirmed("${id}")'>Deletar?</div>
        </div>
    `)
    clearTimeout(timerCat);
    timerCat = setTimeout(() => {
        $(".deleteConfirm").fadeOut(500, function () {
            $(".deleteConfirm").remove();
        })
    }, 3000);
}


function delCarouselConfirmed(id) {
    $.post("/admin/api/post/carousel.php", { deleteGlider: id }, function (data) {
        startGliders();
    })
}
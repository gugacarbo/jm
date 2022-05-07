
var UsedGlider = []
var SelectedItens = []

var actImagePrev = 0;



$(document).ready(function () {


    startGliders()


    $("#Categories").on("change", function () {
        var catI = $(this).val();
        resetPreview()
        if (UsedGlider.indexOf(parseInt(catI)) > -1) {
            editGlider(catI);
        } else {
            $("#radioAuto").prop("checked", true);
            $("#autoType").val("price");

            auto();
        }
    })

    $("#autoType").on("change", function () {
        var cat = $("#Categories").val();


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
                    console.log(data.length);
                    for (let index = data.length; index < 7; index++) {
                        $(".preview .items .item")[index].innerHTML = `<img src="noImage.png">`
                    }
                }
            })
        })

    })

    $("input[name='type']").on("change", function () {

        var type = $(this).val();
        SelectedItens = [];
        var cat = $("#Categories").val();

        if (type == "auto") {
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
        } else {
            var id_ = $("#Categories").val();
            $.get("getAglider.php", { id: id_ }, function (data) {
                if (UsedGlider.indexOf(parseInt(cat)) > -1){
                    data = JSON.parse(data);

                    var glider = $("#Glider" + data.id);
                    $(glider).addClass("preview")
                    $(glider).appendTo(".previewBox");
                    //$(glider).css("top" , $(glider).offset().top)

                    $("#prodList").empty();

                    var select = JSON.parse(data["select"]);

                    if (data["SelectType"] == "auto") {

                        $("#radioAuto").prop("checked", true);
                        if (select['type'] == "price") {
                            $("#autoType").val('price');
                        } else {
                            $("#autoType").val('promo');
                        }
                        $("#autoType").attr("disabled", false);

                        // !!
                        // !!


                        // !!
                        // !!


                    } else {

                        $("#radioId").prop("checked", true);
                        $("#autoType").attr("disabled", true);
                        $("#addCarousel").attr("disabled", true);

                        SelectedItens = [];

                        
                        $.get("getFiltered.php", { cat: cat }, function (r) {

                            r = JSON.parse(r);

                            actImagePrev = 0;

                            $.each(r, function (index, value) {
                                var prod = (value);
                                var images = JSON.parse(prod["imgs"]);

                                //if id is in json select
                                var isIn = 0

                                $.each(select, function (index, value) {
                                    if (value == prod["id"]) {
                                        isIn = 1;
                                        SelectedItens.push(parseInt(value));

                                    }
                                })
                                if (selectItems.length > 0) {
                                    $("#addCarousel").attr("disabled", false);
                                }
                                var item = '<div>' +
                                    '<input type="checkbox" name="selectedProducts" onclick="selectItems(this,' + prod["id"] + ')" ' + ((isIn == 1) ? 'checked' : '') + '>' +
                                    '<img src="' + images[1] + '" alt="">' +
                                    "</div>"
                                $("#prodList").append(item);

                                if (isIn == 1) {
                                    if (actImagePrev < 7) {
                                        var images = JSON.parse(prod["imgs"]);
                                        var images = JSON.parse(prod["imgs"]);
                                        $(".preview .items .item")[actImagePrev].innerHTML = `<img src="${images[1]}" class="selecting">`
                                        actImagePrev++
                                    }
                                }

                                if (data.length < 7) {

                                    for (let index = data.length; index < 7; index++) {
                                        $(".preview .items .item")[index].innerHTML = `<img src="noImage.png" class="selecting">`
                                    }
                                }
                            })
                        })
                    }
                    $("input[name='type']").attr("disabled", false);
                    $("checkbox[name='selectedProducts']").attr("disabled", false);
                }else {
                    $.get("getFiltered.php", { cat: cat }, function (r) {

                        r = JSON.parse(r);

                        actImagePrev = 0;

                        $.each(r, function (index, value) {
                            var prod = (value);
                            var images = JSON.parse(prod["imgs"]);

                          
                            $.each(select, function (index, value) {
                                if (value == prod["id"]) {
                                    isIn = 1;
                                    SelectedItens.push(parseInt(value));

                                }
                            })
                            if (selectItems.length > 0) {
                                $("#addCarousel").attr("disabled", false);
                            }
                            var item = '<div>' +
                                '<input type="checkbox" name="selectedProducts" onclick="selectItems(this,' + prod["id"] + ')" >' +
                                '<img src="' + images[1] + '" alt="">' +
                                "</div>"
                            $("#prodList").append(item);


                            if (data.length < 7) {

                                for (let index = 0; index < 7; index++) {
                                    $(".preview .items .item")[index].innerHTML = `<img src="noImage.png" class="selecting">`
                                }
                            }
                        })
                    })
                } 
            })

        }
    })


    $("#addCarousel").on("click", function () {
        var cat = $("#Categories").val();
        var type = $("input[name='type']:checked").val();
        var selectAutoType = ($("#autoType").val());
        var category = cat;

        if (type == "id") {
            var SelectType = "id";
            var select = SelectedItens;
        } else if (type == "auto") {
            var SelectType = "auto";
            var select = selectAutoType;
        }

        $.get("createCarousel.php", { category: category, SelectType: SelectType, select: select }, function (data) {
            data = JSON.parse(data);
            console.log(data);
            if (data.status == "success") {
                alert("Carousel created");
                $(".previewBox").html("");
                startGliders()
            }
        })



    })


})

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
    } else {
        $("#addCarousel").attr("disabled", false);

    }
}


function auto() {

    $("input[name='type']").attr("disabled", true);
    $("#autoType").attr("disabled", true);
    var cat = $("#Categories").val();
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
        var createC = `<div class="carousel preview">
                        <div class="items">`
        $.each(data, function (index, prod) {
            if (actImagePrev < 7) {
                actImagePrev++
                var images = JSON.parse(prod["imgs"]);
                createC += `<div class="item">
                <img src="${images[1]}">
                </div>
                `
            }
        })
        if (data.length < 7) {
            for (let index = 0; index < 7 - data.length; index++) {
                createC += `<div class="item">
                <img src="noImage.png">
            </div>
            `
            }
        }
        createC += `<\div><\div>`
        $(".previewBox").html(createC);
    }).then((value) => {
        $("input[name='type']").attr("disabled", false);
        $("#autoType").attr("disabled", false);
        $("#addCarousel").attr("disabled", false);




    })

}


function resetPreview() {
    if ($(".preview").attr("id")) {

        $(".preview").appendTo("#CarouselList");
        $(".preview").removeClass("preview")
    } else {

        $(".previewBox").html("");
    }
}


function editGlider(id_) {
    $.get("getAglider.php", { id: id_ }, function (data) {
        data = JSON.parse(data);

        var glider = $("#Glider" + data.id);
        $(glider).addClass("preview")
        $(glider).appendTo(".previewBox");
        //$(glider).css("top" , $(glider).offset().top)

        $("#prodList").empty();

        var select = JSON.parse(data["select"]);

        if (data["SelectType"] == "auto") {

            $("#radioAuto").prop("checked", true);
            if (select['type'] == "price") {
                $("#autoType").val('price');
            } else {
                $("#autoType").val('promo');
            }
            $("#autoType").attr("disabled", false);

            // !!
            // !!


            // !!
            // !!


        } else {

            $("#radioId").prop("checked", true);
            $("#autoType").attr("disabled", true);
            $("#addCarousel").attr("disabled", true);

            SelectedItens = [];

            var cat = $("#Categories").val();
            $.get("getFiltered.php", { cat: cat }, function (r) {

                r = JSON.parse(r);

                actImagePrev = 0;

                $.each(r, function (index, value) {
                    var prod = (value);
                    var images = JSON.parse(prod["imgs"]);

                    //if id is in json select
                    var isIn = 0

                    $.each(select, function (index, value) {
                        if (value == prod["id"]) {
                            isIn = 1;
                            SelectedItens.push(parseInt(value));

                        }
                    })
                    if (selectItems.length > 0) {
                        $("#addCarousel").attr("disabled", false);
                    }
                    var item = '<div>' +
                        '<input type="checkbox" name="selectedProducts" onclick="selectItems(this,' + prod["id"] + ')" ' + ((isIn == 1) ? 'checked' : '') + '>' +
                        '<img src="' + images[1] + '" alt="">' +
                        "</div>"
                    $("#prodList").append(item);

                    if (isIn == 1) {
                        if (actImagePrev < 7) {
                            var images = JSON.parse(prod["imgs"]);
                            var images = JSON.parse(prod["imgs"]);
                            $(".preview .items .item")[actImagePrev].innerHTML = `<img src="${images[1]}">`
                            actImagePrev++
                        }
                    }

                    if (data.length < 7) {

                        for (let index = data.length; index < 7; index++) {
                            $(".preview .items .item")[index].innerHTML = `<img src="noImage.png">`
                        }
                    }
                })
            })
        }
        $("input[name='type']").attr("disabled", false);
        $("checkbox[name='selectedProducts']").attr("disabled", false);
    })
}









function deleteGlider(id_) {
    if (confirm("Are you sure you want to delete this glider?")) {
        $.get("deleteGlider.php", { id: id_ }, function (data) {

            resetPreview();
            startGliders();
        })
    }
}



function startGliders() {

    $("input[name='type']").attr("disabled", true);
    $("#autoType").attr("disabled", true);
    $("#addCarousel").attr("disabled", true);
    $("#Categories").val(0);
    $("#prodList").empty();
    $("#CarouselList").empty();

    $.get("/php/getGlider.php", function (data) {

        var gliders = JSON.parse(data);
        UsedGlider = []
        $.each(gliders, function (index, glider) {
            UsedGlider.push(glider['category']);

            var prods = JSON.parse(glider['prod_ids']);
            var carousel = '<div class="carousel" id="Glider' + glider['id'] + '">'
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
                                            <span class="deleteGlider" onclick="deleteGlider(` + glider['id'] + `)"><i class="fa-solid fa-trash-can"></i></span></div>`)
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
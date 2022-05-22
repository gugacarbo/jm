var cities = []
var states = []
var StatesList = ["AC", "AL", "AP", "AM", "BA", "CE", "DF", "ES", "GO", "MA", "MT", "MS", "MG", "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RS", "RO", "RR", "SC", "SP", "SE", "TO",];

$("#addStateInput").mask("AA");

$(document).ready(function () {

    $("#freteGratisCheck").on("change", () => {
        $(".selectFree").toggleClass("noFreteGratis", !$("#freteGratisCheck").prop("checked"));
    })
    $("#SaveCupom").on("click", () => {

    })

    var timerShipSizes;
    $("input[type='number']").on("paste, focusout", function () {
        var this_ = this;
        var maxV = parseFloat($(this_).attr("max"));
        var minV = parseFloat($(this_).attr("min"));
        var val = parseFloat($(this_).val());

        timerShipSizes = setTimeout(function () {
            if (val > maxV) {
                $(this_).val(maxV);
                alert("Valor máximo é " + maxV);
            } else if (val < minV) {
                $(this_).val(minV);
                alert("Valor mínimo é " + minV);
            }
        }, 200);
    })

    takeCupons()

    $.get("/admin/api/get/getFreeShipConfig.php", (data) => {

        var freteGratis = JSON.parse(data.data.freteGratis);
        $("#freteGratisCheck").prop("checked", freteGratis.use == "true");
        $(".selectFree").toggleClass("noFreteGratis", freteGratis.use == "false");

        $.each(freteGratis.cidades, (i, v) => {
            $("#freeCity").append(`<label><p>${v}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'city')"></i></label>`);
            cities.push(v);
        })
        if (freteGratis.estados.length == 27) {
            $("#freeStateAll").prop("checked", "true")
        } else {
            $.each(freteGratis.estados, (i, v) => {
                $("#freeState").append(`<label><p>${v}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'state')"></i></label>`);
                states.push(v);
            })
        }
    })

    $("#addCepInput").mask("99.999-999");

    $("#addCep").on("click", () => {
        var cep = $("#addCepInput").val();
        cep = cep.replace(/\D/g, '');
        if (cep.length == 8) {
            $.get("/admin/api/get/getAddress.php", {
                cep: cep
            }, function (data) {
                if (data.erro) {
                } else {
                    var c_ = data.cidade[0];
                    var s_ = data.uf[0];
                    var newCity = UPFisrt(c_.toLowerCase())
                    var newState = (s_.toUpperCase())

                    var exist = 0;
                    $(cities).each(function (i, v) {
                        if (newCity == v) {
                            exist = 1;
                        }
                    })
                    if (exist == 0) {
                        $("#freeCity").append(`<label><p>${newCity}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'city')"></i></label>`);
                        cities.push(newCity);
                    }

                    var exist = 0;
                    $(states).each(function (i, v) {
                        if (newState == v) {
                            exist = 1;
                        }
                    })
                    if (exist == 0) {
                        $("#freeState").append(`<label><p>${newState}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'state')"></i></label>`);
                        states.push(newState);
                    }

                    $("#addCepInput").val("");
                }
            })
        }
    })


    /**
     * 
     * 
     * O comprimento não pode ser maior que 105 cm.
    -16 A largura não pode ser maior que 105 cm.
    -17 A altura não pode ser maior que 105 cm.
    -18 A altura não pode ser inferior a 2 cm.
    -20 A largura não pode ser inferior a 11 cm.
    -22 O comprimento não pode ser inferior a 16 cm.
    -23 A soma resultante do comprimento + largura + altura não deve superar a 200 cm
     */

    $("#SaveFreeShipConfig").click(function () {
        var settings = {
            "freteGratis": $("#freteGratisCheck").is(":checked"),
            "cidades": cities || [],
            "estados": $("#freeStateAll").prop("checked") == 1 ? StatesList : states || []
        }
        $.post("/admin/api/post/editFreeShippingConfig.php", settings, function (data) {
            $(".doneButton").removeClass("doneButton")
            $(".alertButton").removeClass("alertButton")
            if (data.status >= 200 && data.status < 300) {
                $("#SaveFreeShipConfig").addClass("doneButton")
                setTimeout(() => {
                    $(".doneButton").removeClass("doneButton")
                }, 1500);
            } else {
                $("#SaveFreeShipConfig").addClass("alertButton")
                setTimeout(() => {
                    $(".alertButton").removeClass("alertButton")
                }, 1500);
            }
        })
    })

    $("#addCity").click(() => {
        var city = $("#addCityInput").val();
        if (city.length > 0) {

            var newCity = UPFisrt(city.toLowerCase());
            var exist = 0;

            $(cities).each(function (i, v) {
                if (newCity == v) {
                    exist = 1;
                }
            })
            if (exist == 0) {
                $("#freeCity").append(`<label><p>${newCity}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'city')"></i></label>`);
                $("#addCityInput").val("");
                cities.push(newCity);
            } else {
                alert("Cidade já existe");
                $("#addCityInput").val("");
            }
        }
    })

    $("#addState").click(() => {
        var state = $("#addStateInput").val();
        if (state.length > 0) {

            var newState = state.toUpperCase();
            addState(newState);
        }
    })
    $("#SaveCupom").click(function () {
        var id = $(this).attr("data-id");
        var type = $("#CupomRelType").prop("checked") == true ? "percent" : "absolute";
        var newCupom = {
            'id': id || 0,
            "ticker": $("#NewCupomTicker").val(),
            "value": $("#NewCupomValue").val(),
            "type": type,
            "quantity": $("#NewCupomQuantity").val(),
            "firstPurchase": $("#singleUseCumpom").prop("checked") == true ? 1 : 0,
        }
        $.post("/admin/api/post/editCupom.php", newCupom, function (data) {
            if (data.status >= 200 && data.status < 300) {
                $(".doneButton").removeClass("doneButton")
                $(".alertButton").removeClass("alertButton")
                $("#SaveCupom").addClass("doneButton")
                setTimeout(() => {
                    closeCupomModal()
                }, 800);
                setTimeout(() => {
                    $(".doneButton").removeClass("doneButton")
                    takeCupons();
                }, 1500);
                
            } else {
                $(".doneButton").removeClass("doneButton")
                $(".alertButton").removeClass("alertButton")
                $("#SaveCupom").addClass("alertButton")
                setTimeout(() => {
                    $(".alertButton").removeClass("alertButton")
                }, 1500);
            }

        })
    })


    $("#closeModalCupom").on("click", () => {
        closeCupomModal()

    
    })

    $("#newCupom").on("click", () => {
        cupomModal()
        openCupomModal()
    })
})
function closeCupomModal() {
    $("#CupomModal").removeClass("modalCupomActive");
    $("#SaveCupom").attr("data-id", '');

    setTimeout(() => {
        $("#CupomModal").css('display', 'none');
    }, 900);

}

function openCupomModal() {
    $("#CupomModal").css('display', 'flex');
    setTimeout(() => {
        $("#CupomModal").addClass("modalCupomActive");
    }, 10);
}


function cupomModal(id_ = 0) {
    if (id_ == 0) {
        $("#modalCupomHeader").html("Adicionar Cupom");
        $("#CupomAbsType").prop("selected", true);
        $("#singleUseCumpom").prop("checked", true);
        $("#NewCupomQuantity").val('');
        $("#NewCupomValue").val('');
        $("#NewCupomTicker").val('');
        $("#SaveCupom").attr("data-id", '');
        $("#NewCupomTicker").attr("disabled", false);
        openCupomModal()
    } else {
        $.get("/admin/api/get/getCupom.php", { id: id_ }, function (data) {
            if (data.status >= 200 && data.status < 300) {
                var cup = data.cupom
                $("#modalCupomHeader").html("Editar Cupom");
                cup.type == "percent" ? $("#CupomAbsType").prop("selected", true) : $("#CupomRelType").prop("selected", true);
                $("#singleUseCumpom").prop("checked", cup.firstPurchase);
                $("#NewCupomQuantity").val(cup.quantity);
                $("#NewCupomValue").val(cup.value);
                $("#SaveCupom").attr("data-id", id_);
                $("#NewCupomTicker").val(cup.ticker);
                $("#NewCupomTicker").attr("disabled", true);

            } else {
                closeCupomModal()
            }
        }).then((value) => {
            openCupomModal()

        })
    }
}

function ifAllState() {
    var allIn = 1;
    $(StatesList).each(function (i, v) {
        if (states.indexOf(v) == -1) {
            allIn = 0;
        }
    })
    console.log(allIn)
}

function deleteAllCity() {
    $("#freeCity").html("");
    cities = [];
}

function deleteAllState() {
    $("#freeState").html("");
    states = [];
}
function addAllState() {
    $(StatesList).each(function (i, v) {
        addState(v);
    })
}
function addState(newState) {
    var exist = 0;

    $(states).each(function (i, v) {
        if (newState == v) {
            exist = 1;
        }
    })
    if (exist == 0) {
        if (StatesList.indexOf(newState) == -1) {
            alert("Estado não existe");
            $("#addStateInput").val("");
        } else {
            $("#freeState").append(`<label><p>${newState}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'state')"></i></label>`);
            $("#addStateInput").val("");
            states.push(newState);
        }
    } else {
        $("#addStateInput").val("");
    }
}



function takeCupons() {
    $("#CuponsList").html("");

    $.get("/admin/api/get/getCupons.php", function (data) {
        if (data.status >= 200 && data.status < 300) {
            $.each(data.cupons, function (i, cupom) {
                $("#CuponsList").append(`
                                        <div class="showCupom">
                                        <span class="ticker">${cupom.ticker}</span>
                                        <span class="useTimes">${cupom.firstPurchase ? "Uso Único" : "Uso Livre"}</span>
                                        <span class="edit" onclick="cupomModal(${cupom.id})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                        <span class="edit delete" onclick="deleteCupom(${cupom.id}, this)"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
                                        <div class="quantityBox">
                                            <div class="loading" style="--L:${cupom.clientIds.length / (cupom.quantity + cupom.clientIds.length) * 100}%">
                                            </div>
                                            <span class="used">Usados: <b>${cupom.clientIds.length}</b></span>
                                            <span class="total">Total: <b>${cupom.clientIds.length + cupom.quantity}</b></span>
                                        </div>
                                        <span class="value">${cupom.type == "percent" ? "" : 'R$'}${cupom.value}${cupom.type == "percent" ? "%" : ''}</span>
                                    </div>
                                    `)
            })
        }
    })
}

function deleteCupom(id, el) {

    $(el).append(`<div class="deleteConfirm" onclick="deleteCupomConfirmed(${id})"><div>Deletar</div></div>`);
    setTimeout(() => {
        $(".deleteConfirm").fadeOut(500, () => {
            $(".deleteConfirm").remove();
        });
    }, 3000);
}
function deleteCupomConfirmed(id){
    $.post("/admin/api/delete/deleteCupom.php", { id: id }, function (data) {
        if (data.status >= 200 && data.status < 300) {
            takeCupons()
        }
    })
}

function del(this_, cs) {
    var p = $(this_).parent().find("p").text();
    if (cs == "city") {
        cities.splice(cities.indexOf(p), 1);
    } else {
        states.splice(states.indexOf(p), 1);
    }
    $(this_).parent().remove();

}

function UPFisrt(string) {
    const arr = string.split(" ");
    for (var i = 0; i < arr.length; i++) {
        arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
    }
    return str2 = arr.join(" ");
}
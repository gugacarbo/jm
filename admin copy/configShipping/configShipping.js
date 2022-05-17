var cities = []
var states = []
var StatesList = ["AC", "AL", "AP", "AM", "BA", "CE", "DF", "ES", "GO", "MA", "MT", "MS", "MG", "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RS", "RO", "RR", "SC", "SP", "SE", "TO",];
$("#addStateInput").mask("AA");

$(document).ready(function () {
    $("body").append($("<div class='adminHeader'>").load("../header.html"));
    $("body").append($("<div class='adminMenu'>").load("../menu.html"));

    $("#freteGratisCheck").on("change", () => {
        $(".selectFree").toggleClass("noFreteGratis", !$("#freteGratisCheck").prop("checked"));
    })

    $.get("getShippingConfig.php", (data) => {
        data = JSON.parse(data);

        var freteGratis = JSON.parse(data.data.freteGratis);
        $("#freteGratisCheck").prop("checked", freteGratis.use == "true");
        $(".selectFree").toggleClass("noFreteGratis", freteGratis.use == "false");

        $.each(freteGratis.cidades, (i, v) => {
            $("#freeCity").append(`<label><p>${v}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'city')"></i></label>`);
            cities.push(v);
        })
        $.each(freteGratis.estados, (i, v) => {
            $("#freeState").append(`<label><p>${v}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'state')"></i></label>`);
            states.push(v);
        })

        var ship = data.data
        $("#cepOrigemFrete").val(ship.cepOrigemFrete);
        $("#alturaFrete").val(ship.alturaFrete);
        $("#larguraFrete").val(ship.larguraFrete);
        $("#comprimentoFrete").val(ship.comprimentoFrete);
        $("#aditionalWeight").val(ship.aditionalWeight);

    })

    $("#addCepInput").mask("99.999-999");

    $("#addCep").on("click", () => {
        var cep = $("#addCepInput").val();
        cep = cep.replace(/\D/g, '');
        if (cep.length == 8) {
            $.get("getAddress.php", {
                cep: cep
            }, function (data) {
                data = JSON.parse(data);
                if (data.erro) {
                    alert(data.erro);
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




    $("#SaveShipConfig").click(function () {
        var settings = {
            "cepOrigemFrete": $("#cepOrigemFrete").val(),
            "aditionalWeight": $("#aditionalWeight").val(),
            "alturaFrete": $("#alturaFrete").val(),
            "larguraFrete": $("#larguraFrete").val(),
            "comprimentoFrete": $("#comprimentoFrete").val(),
            "freteGratis": $("#freteGratisCheck").is(":checked"),
            "cidades": cities || [],
            "estados": states || []

        }
        console.log(settings)
        $.get("saveShipConfig.php", settings, function (data) {
            data = JSON.parse(data)
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
})

function ifAllState(){
    var allIn = 1;
    $(StatesList).each(function(i,v){
        if(states.indexOf(v) == -1){
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
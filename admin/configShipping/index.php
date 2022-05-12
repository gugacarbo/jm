<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="configShipping.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div class="content">
        <label>
            <span>Cep Origem: </span>
            <input type="text" id="cepOrigemFrete" placeholder="00000-000">
        </label>
        <div>
            <div class="previewSize">
                <div class="box">
                </div>
            </div>
            <div>
                <label>
                    <span>Altura do Pacote: </span>
                    <input type="number" min="10" value="10" id="alturaFrete">
                    <b>cm</b>
                </label>
                <label>
                    <span>Largura do Pacote: </span>
                    <input type="number" min="10" value="10" id="larguraFrete">
                    <b>cm</b>
                </label>
                <label>
                    <span>Comprimento do Pacote: </span>
                    <input type="number" min="10" value="10" id="comprimentoFrete">
                    <b>cm</b>
                </label>
            </div>
        </div>
        <label>
            <span>Peso da Embalagem: </span>
            <input type="number" min="0" step="0.1" value="0.0" id="aditionalWeight">
            <b>Kg</b>
        </label>
        <div class="freteGratis">
            <label>
                <h4>Frete Gratis</h4>
                <input type="checkbox" id="freteGratisCheck">
                <span></span>
            </label>
            <div class="selectFree">
                <div>
                    <h3>Cidades</h3>
                    <div id="freeCity">

                    </div>

                    <label class="add">
                        <input type="text" id="addCityInput" placeholder="Cidade">
                        <i class="fa-solid fa-plus" id="addCity"></i>
                    </label>
                </div>

                <div>
                    <h3>Estados</h3>
                    <div id="freeState">
                    </div>

                    <label class="add">
                        <input type="text" maxlength="2" id="addStateInput" placeholder="UF">
                        <i class="fa-solid fa-plus" id="addState"></i>
                    </label>

                </div>
            </div>
            <div>
                <p>Adicionar por Cep</p>
                <input type="text" maxlength="10" id="addCepInput" placeholder="CEP">
                <i class="fa-solid fa-plus" id="addCep"></i>
            </div>
            <button id="save">
                Salvar
            </button>
        </div>
        <script>
            var cities = []
            var states = []
            $("#addStateInput").mask("AA");

            $(document).ready(function() {

                $.get("getShippingConfig.php", (data) => {
                    data = JSON.parse(data);

                    var freteGratis = JSON.parse(data.data.freteGratis);
                    $("#freteGratisCheck").prop("checked", freteGratis.use == "true" );

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
                        }, function(data) {
                            data = JSON.parse(data);
                            if (data.erro) {
                                alert(data.erro);
                            } else {
                                var c_ = data.cidade[0];
                                var s_ = data.uf[0];
                                var newCity = UPFisrt(c_.toLowerCase())
                                var newState = (s_.toUpperCase())

                                var exist = 0;
                                $(cities).each(function(i, v) {
                                    if (newCity == v) {
                                        exist = 1;
                                    }
                                })
                                if (exist == 0) {
                                    $("#freeCity").append(`<label><p>${newCity}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'city')"></i></label>`);
                                    cities.push(newCity);
                                }

                                var exist = 0;
                                $(states).each(function(i, v) {
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




                $("#save").click(function() {
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
                    $.get("saveShipConfig.php", settings, function(data) {
                        data = JSON.parse(data)
                    })
                })

                $("#addCity").click(() => {
                    var city = $("#addCityInput").val();
                    if (city.length > 0) {

                        var newCity = UPFisrt(city.toLowerCase());
                        var exist = 0;

                        $(cities).each(function(i, v) {
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
                        var exist = 0;

                        $(states).each(function(i, v) {
                            if (newState == v) {
                                exist = 1;
                            }
                        })
                        if (exist == 0) {
                            $("#freeState").append(`<label><p>${newState}</p><i class="fa-solid fa-trash-can" onclick="del(this, 'state')"></i></label>`);
                            $("#addStateInput").val("");
                            states.push(newState);
                        } else {
                            alert("Estado já existe");
                            $("#addStateInput").val("");
                        }
                    }
                })

            })

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
        </script>
</body>

</html>

$("header").load("/includes/header.html");
$("footer").load("/includes/footer.html");
$('#DataTelefone').mask('(00) 0.0000-0000');
$('#DataCPF').mask('000.000.000-00');
$('#ShippingCEP').mask('00.000-000');
$('#ShippingNumero').mask('00000');
$(document).ready(() => {
    getBuyerLocalData();
    $("#ShippingCEP").on('keyup', () => {
        if ($("#ShippingCEP").val().length == 10) {
            var cep = $("#ShippingCEP").val();
            $.ajax({
                url: "http://viacep.com.br/ws/" + ((cep).replace('.', '').replace('-', '')) + "/json/",
                type: "GET",
                crossDomain: true,
                dataType: "json",
                mode: 'no-cors'
            }).done((data) => {
                //console.log(data)
                $('#ShippingBairro').val(data['bairro'])
                $('#ShippingCidade').val(data['localidade'])
                $('#ShippingRua').val(data['logradouro'])
                $('#ShippingUF').val(data['uf'])
            })
        }
    })
    $("#FormContinue").on("click", function () {
        var vazios = $("#buyerForm input").filter(function () {
            return !this.value;
        }).get();
        var cpf = $("#DataCPF").val().replace(".", "").replace(".", "").replace("-", "");
        var date = ($('#DataNascimento').val());
        if (vazios.length) {
            $(vazios).addClass('vazio');
            return false;
        } else if (!validarCPF(cpf)) {
            $("#DataCPF").addClass('vazio');
            alert("Cpf inválido");
            return false;
        } else if (!verificaData(date)) {
            $("#DataNascimento").addClass('vazio');
            alert("Data inválida");
            return false;
        } else {
            $("#buyerForm").submit();
        }
    });
    $("#buyerForm input").on("focus", function () {
        $(this).removeClass('vazio');
    })


})
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf == '') return false;
    // Elimina CPFs invalidos conhecidos	
    if (cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999")
        return false;
    // Valida 1o digito	
    add = 0;
    for (i = 0; i < 9; i++)
        add += parseInt(cpf.charAt(i)) * (10 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(9)))
        return false;
    // Valida 2o digito	
    add = 0;
    for (i = 0; i < 10; i++)
        add += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (add % 11);
    if (rev == 10 || rev == 11)
        rev = 0;
    if (rev != parseInt(cpf.charAt(10)))
        return false;
    return true;
}

var minYear = 2006;
var maxYear = 1900;

function verificaData(input) {
    var Data = formatDate(input)
    Data = Data.substring(0, 10);
    var dma = -1;
    var data = Array(3);
    var ch = Data.charAt(0);
    for (i = 0; i < Data.length && ((ch >= '0' &&
        ch <= '9') || (ch == '/' && i != 0));) {
        data[++dma] = '';
        if (ch != '/' && i != 0) return false;
        if (i != 0) ch = Data.charAt(++i);
        if (ch == '0') ch = Data.charAt(++i);
        while (ch >= '0' && ch <= '9') {
            data[dma] += ch;
            ch = Data.charAt(++i);
        }
    }
    if (ch != '') return false;
    if (data[0] == '' || isNaN(data[0]) || parseInt(data[0]) < 1) return false;
    if (data[1] == '' || isNaN(data[1]) || parseInt(data[1]) < 1 ||
        parseInt(data[1]) > 12) return false;
    if (data[2] == '' || isNaN(data[2]) || ((parseInt(data[2]) < 0 ||
        parseInt(data[2]) > 99) && (parseInt(data[2]) <
            maxYear || parseInt(data[2]) > minYear))) return false;
    if (data[2] < 50) data[2] = parseInt(data[2]) + 2000;
    else if (data[2] < 100) data[2] = parseInt(data[2]) + maxYear;
    switch (parseInt(data[1])) {
        case 2: {
            if (((parseInt(data[2]) % 4 != 0 ||
                (parseInt(data[2]) % 100 == 0 && parseInt(data[2]) % 400 != 0))
                && parseInt(data[0]) > 28) || parseInt(data[0]) >
                29) return false; break;
        }
        case 4: case 6: case 9: case 11: {
            if (parseInt(data[0]) >
                30) return false; break;
        }
        default: { if (parseInt(data[0]) > 31) return false; }
    }
    return true;
}
function formatDate(input) {
    input.replace("-", "/").replace("-", "/");
    var datePart = input.match(/\d+/g),
        year = datePart[0], // get only two digits
        month = datePart[1], day = datePart[2];
    return day + '/' + month + '/' + year;
}

var buyerLocalData;
function getBuyerLocalData() {

    var buyerLocalData_ = localStorage.getItem("buyerLocalData");
    if (buyerLocalData_) {
        buyerLocalData = JSON.parse(buyerLocalData_)
        console.log(buyerLocalData);

        $("#DataNome").val(buyerLocalData.nome);
        $("#DataSobrenome").val(buyerLocalData.sobrenome);
        $("#DataCPF").val(buyerLocalData.cpf);
        $("#DataNascimento").val(buyerLocalData.nascimento);
        $("#DataTelefone").val(buyerLocalData.telefone);
        $("#DataEmail").val(buyerLocalData.email);
        $("#ShippingNumero").val(buyerLocalData.numero);
        $("#ShippingComplemento").val(buyerLocalData.complemento);
        $("#SelectGender").val(buyerLocalData.gender);
        $("#ShippingBairro").val(buyerLocalData.bairro);
        $("#ShippingCidade").val(buyerLocalData.cidade);
        $("#ShippingUF").val(buyerLocalData.UF);
        $("#ShippingCEP").val(buyerLocalData.cep);
        $("#ShippingRua").val(buyerLocalData.rua);

     


    } else {
        console.log("noBuyer");

    }
}

/**UF: "SC"
bairro: "Copacabana"
cep: "88.504-110"
cidade: "Lages"
complemento: "33333"
cpf: "011.894.469-01"
email: "guga_carbo@hotmail.com"
gender: "M"
nascimento: "2000-02-12"
nome: "aaa"
numero: "643"
rua: "Rua Visconde de Maua"
sobrenome: "bbb"
telefone: "(55) 4.9999-0149" */
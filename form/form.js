


$("header").load("/includes/header.html");
$("footer").load("/includes/footer.html");

$('#DataTelefone').mask('(00) 0.0000-0000');
$('#DataCPF').mask('000.000.000-00');
$('#DataNascimento').mask('00/00/0000');
$('#ShippingCEP').mask('00.000-000');
$('#ShippingNumero').mask('00000');

$(document).ready(() => {

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



})




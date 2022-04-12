$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");
    if ($.urlParam("cpf") == null || $.urlParam("code") == null) {
        alert("um erro ocorreu");
        window.location.href = "/login/";
    } else {
        getData($.urlParam("cpf"), $.urlParam("code"))
    }
    $("#requestCancel").click(() => {
        $("#cancelBox").css("display", "flex");
        setTimeout(() => {
            $(".cancel").toggleClass("show")
        }, 100);
    })
    $("#closeCancel").click(() => {
        $(".cancel").toggleClass("show")
        setTimeout(() => {
            $("#cancelBox").css("display", "none");
        }, 800);
    })
    $("#cancelButton").click(() => {
        var reason = ($("#cancelText").val());
        var message = "Solicitação de Cancelamento: \n"
            + "Telefone: " + $("#BuyerPhone").html() + "\n"
            + "Nome: " + $("#BuyerName").html() + "\n"
            + "Código da Compra: " + $.urlParam("code") + "\n"
            + "Motivo: " + reason + "\n"

        if (reason) {
            //var sendToDb = $.get("/php/sendContact.php", { name: nome, phone, message }, data => {
            //})

            let url = "https://wa.me/+5549999604384?text=";
            console.log(url + encodeURI(message))

            window.open(
                url + encodeURI(message),
                '_blank'
            )
        }
    })
})





function getData(cpf, code) {
    //ajax get to getProductStatus
    $.get("/php/getProductStatus.php", { "cpf": cpf, "code": code }, function (data) {
        data = JSON.parse(data)
        var pagData = JSON.parse(data.rawPayload);



        //var prods = ((typeof(pagData.items.item) != "object") ? pagData.items.item:  [pagData.items.item]);
        var items = pagData.items.item;
        items = Object.prototype.toString.call(items) === '[object Array]' ? items : [items];

        $("#BuyDate").html("Data da Compra " + data.buyDate);
        $("#BuyerName").html("Nome: "+pagData.sender.name);
        $("#BuyerCPF").html("CPF: "+data.cpf);
        $("#BuyerEmail").html("email: " + pagData.sender.email);
        $("#BuyerPhone").html("(" + pagData.sender.phone.areaCode + ")" + pagData.sender.phone.number);
        $("#BuyerStreet").html(pagData.shipping.address.street + " " + pagData.shipping.address.number);
        $("#BuyerComplement").html(pagData.shipping.address.complement);
        $("#BuyerDistrict").html(pagData.shipping.address.district);
        $("#BuyerCity").html(pagData.shipping.address.city);
        $("#BuyerState").html(pagData.shipping.address.state);
        $("#BuyerCountry").html(pagData.shipping.address.country);
        $("#BuyerPostalCode").html(pagData.shipping.address.postalCode);
        $("#BuyerBornDate").html(data.bornDate);
        $("#ShippingValue").html("Frete: R$" + parseFloat(pagData.shipping.cost).toFixed(2).replace(".", ","));
        (pagData.discountAmount > 0) ?  $("#TotalDiscount").html("Desconto: R$" + parseFloat(pagData.discountAmount).toFixed(2).replace(".", ",")) : $("#TotalDiscount").css("display", "none");
        $("#TotalAmount").html("Total: R$" + (parseFloat(data.totalAmount).toFixed(2)).replace(".", ","));
        $.each(items, function (i, item) {
            var x = $.get("/php/getProdById.php", { "id": item.id }, async function (data) {
                imgs = (JSON.parse(data)["imgs"]);
                imgs = JSON.parse(imgs);
                var pAppend = '<div class="prod">' +
                    '<div class="image">' +
                    '<img src="' + imgs[1] + '">' +
                    '</div>' +
                    '<div class="info">' +
                    '<span class="prodName">' + item.description + '</span>' +
                    '<span class="prodQuantity">Qtd ' + item.quantity + '</span>' +
                    '<span class="prodTotalPrice">R$ ' + (parseFloat(item.amount).toFixed(2)).replace(".", ",") + '</span>' +
                    '</div>' +
                    '</div>';

                $("#StatusProdShow").append(pAppend);
            })
        });

        switch (pagData.paymentMethod.type) {
            case "1":
                $("#PaymentMethod").html(" Forma de pagamento: Cartão de Crédito");
                break;
            case "2":
                $("#PaymentMethod").html("Forma de pagamento: Boleto");
                $("#BoletoLink").html("<a target='blank' href='" + pagData.paymentLink + "'>Visualizar Boleto</a>");

                break;
            case "3":
                $("#PaymentMethod").html("Forma de pagamento: Débito online");
                break;
            case "4":
                $("#PaymentMethod").html("Forma de pagamento: Saldo PagSeguro");
                break;
            case "5":
                $("#PaymentMethod").html("Forma de pagamento: Oi Paggo ");
                break;
            case "7":
                $("#PaymentMethod").html("Forma de pagamento: Transferência bancária");
                break;
            case "8":
                $("#PaymentMethod").html("Forma de pagamento: Cartão Emergencial Caixa ");
                break;
            case "11":
                $("#PaymentMethod").html("Forma de pagamento: PIX");
                break;
            default:
        }
        var statusS;
        switch (pagData.status) {
            case "1":
                statusS = "Aguardando pagamento";
                $("#WaitingPayment").addClass("doing")
                break;
            case "2":
                statusS = "Em análise";
                $("#WaitingPayment").addClass("doing")
                $("#preparingText").html(statusS)
                break;
            case "3":
                statusS = "Paga";
                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("doing")
                break;
            case "4":
                statusS = "Disponível";
                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("doing")
                break;
            case "5":
                statusS = "Em disputa";
                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("done")
                $("#Sended").addClass("done")
                $("#Arrived").addClass("done")

                $("#finiShedIcon").css("display", "none");
                $("#disputText").html(statusS)
                $("#disputIcon").css("display", "flex");;
                break;
            case "6":
                statusS = "Devolvida";
                $("#finiShedIcon").css("display", "none");

                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("done")
                $("#Sended").addClass("done")
                $("#Arrived").addClass("done")

                $("#disputText").html(statusS)
                $("#disputIcon").css("display", "flex");;
                break;
            case "7":
                statusS = "Cancelada";
                $("#finiShedIcon").css("display", "none");
                $("#canceledIcon").css("display", "flex");;
                break;
            case "8":
                statusS = "Debitado";
                $("#finiShedIcon").css("display", "none");

                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("done")
                $("#Sended").addClass("done")
                $("#Arrived").addClass("done")

                $("#disputText").html(statusS)
                $("#disputIcon").css("display", "flex");;
                break;
            case "9":
                statusS = "Retenção temporária";
                $("#finiShedIcon").css("display", "none");

                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("done")
                $("#Sended").addClass("done")
                $("#Arrived").addClass("done")

                $("#disputText").html(statusS)
                $("#disputIcon").css("display", "flex");;
                break;
            default:

        }
    })
}

$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    }
    else {
        return results[1] || 0;
    }
}
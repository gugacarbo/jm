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


    //x Cancelar Compra
    $("#cancelButton").click(() => {
        var reason = ($("#cancelText").val());

        if (reason) {

            $.post("/api/post/requestPurchaseCancel.php", {
                'code': $.urlParam("code"),
                'reason': reason
            }, (data) => {
                if (data.status >= 200 && data.status < 300) {
                    $("#requestCancel").prop("disabled", 'disabled');
                    $("#requestCancel").prop("id", '')

                    alert("Compra cancelada com sucesso")
                    setTimeout(() => {
                        $(".cancel").removeClass("show")
                        setTimeout(() => {
                            $("#cancelBox").css("display", "none");
                        }, 800);
                    }, 200);
                } else {
                    alert("Erro ao cancelar compra")
                }
            })


            /*
            var message = "Solicitação de Cancelamento: \n"
            + "Telefone: " + $("#BuyerPhone").html() + "\n"
            + "Nome: " + $("#BuyerName").html() + "\n"
            + "Código da Compra: " + $.urlParam("code") + "\n"
            + "Motivo: " + reason + "\n"

            * Whatsapp
            let url = "https://wa.me/+5549999604384?text=";
    
            window.open(
                url + encodeURI(message),
                '_blank'
            )*/


        } else {
            alert("Por favor, preencha o motivo do cancelamento")
        }
    })

})



function moreTracking() {
    $("#TrackingDiv").toggleClass("moreTracking");
    $("#moreTracking").toggleClass("rotate");

}

function getData(cpf, code) {
    //ajax get to getProductStatus
    $.get("/api/get/ProductStatus.php", { "cpf": cpf, "code": code }, function (data) {
        console.log(data)
        var pagData = data.rawPayload;
        //var prods = ((typeof(pagData.items.item) != "object") ? pagData.items.item:  [pagData.items.item]);
        var items = pagData.items.item;

        items = Object.prototype.toString.call(items) === '[object Array]' ? items : [items];

        var date = new Date(data.bornDate.replace(/-/g, '\/'));
        var d = date.getDate();
        var m = date.getMonth();
        m += 1;  // JavaScript months are 0-11
        var y = date.getFullYear();

        var today = new Date();
        var dd = new Date(data.buyDate.replace(/-/g, '\/'));
        var timeDiff = Math.abs(dd.getTime() - today.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

        if (diffDays > 30 || data.internalStatus == 5 || data.status > 4) { //! asdasd
            $("#requestCancel").prop("disabled", 'disabled')
            $("#requestCancel").prop("id", '')
        }
        if(data.status < 3){
            $("#requestCancel").css("display", "none")
            $("#requestCancel").prop("id", '')
        }

        var date2 = new Date(data.buyDate.replace(/-/g, '\/'));
        var d2 = date2.getDate();
        var m2 = date2.getMonth();
        m2 += 1;  // JavaScript months are 0-11
        var y2 = date2.getFullYear();

        $("#BuyDate").html("Data da Compra " + d2 + "/" + m2 + "/" + y2);

        $("#BuyerBornDate b").html(d + "/" + m + "/" + y);
        $("#BuyerName b").html(+ data.name);
        $("#BuyerCPF b").html(data.cpf);
        $("#BuyerEmail b").html(pagData.sender.email);
        $("#BuyerPhone b").html("(" + pagData.sender.phone.areaCode + ")" + pagData.sender.phone.number);
        $("#BuyerStreet b").html(pagData.shipping.address.street + " " + pagData.shipping.address.number);
        $("#BuyerComplement b").html(pagData.shipping.address.complement);
        $("#BuyerDistrict b").html(pagData.shipping.address.district);
        $("#BuyerCity b").html(pagData.shipping.address.city);
        $("#BuyerState b").html(pagData.shipping.address.state);
        $("#BuyerCountry b").html(pagData.shipping.address.country);
        $("#BuyerPostalCode b").html(pagData.shipping.address.postalCode);
        $("#ShippingValue b").html(parseFloat(pagData.shipping.cost).toFixed(2).replace(".", ","));
        (pagData.discountAmount > 0) ? $("#TotalDiscount b").html(parseFloat(pagData.discountAmount).toFixed(2).replace(".", ",")) : $("#TotalDiscount").css("display", "none");
        $("#TotalAmount b").html((parseFloat(data.totalAmount).toFixed(2)).replace(".", ","));
        $.each(items, function (i, item) {
            var x = $.get("/api/get/getProdById.php", { "id": item.id }, async function (data) {
                if (data.id != null) {
                    imgs = data["imgs"];
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
                } else {
                    var pAppend = '<div class="prod">' +
                        '<div class="image">' +
                        '<img src="noImage.png">' +
                        '</div>' +
                        '<div class="info">' +
                        '<span class="prodName">' + item.description + '</span>' +
                        '<span class="prodQuantity">Qtd ' + item.quantity + '</span>' +
                        '<span class="prodTotalPrice">R$ ' + (parseFloat(item.amount).toFixed(2)).replace(".", ",") + '</span>' +
                        '</div>' +
                        '</div>';
                    $("#StatusProdShow").append(pAppend);
                }
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
                $("#disputIcon").css("display", "flex");
                $("#requestCancel").attr("disabled", true);
                break;
            case "6":
                statusS = "Devolvida";
                $("#finiShedIcon").css("display", "none");

                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("done")
                $("#Sended").addClass("done")
                $("#Arrived").addClass("done")

                $("#disputText").html(statusS)
                $("#disputIcon").css("display", "flex");
                $("#requestCancel").attr("disabled", true);

                break;
            case "7":
                statusS = "Cancelada";
                $("#finiShedIcon").css("display", "none");
                $("#canceledIcon").css("display", "flex");
                break;
            case "8":
                statusS = "Debitado";
                $("#finiShedIcon").css("display", "none");

                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("done")
                $("#Sended").addClass("done")
                $("#Arrived").addClass("done")

                $("#disputText").html(statusS)
                $("#disputIcon").css("display", "flex");
                $("#requestCancel").attr("disabled", true);

                break;
            case "9":
                statusS = "Retenção temporária";
                $("#finiShedIcon").css("display", "none");

                $("#WaitingPayment").addClass("done")
                $("#PreparingSending").addClass("done")
                $("#Sended").addClass("done")
                $("#Arrived").addClass("done")
                $("#requestCancel").attr("disabled", true);

                $("#disputText").html(statusS)
                $("#disputIcon").css("display", "flex");;
                break;
            default:

        }
        var trackingCode = data.trackingCode;
        if (trackingCode != "") {
            $("#TrackingDiv").css("display", "flex");
            $("#WaitingPayment").addClass("done")
            $("#PreparingSending").addClass("done")
            $("#Sended").addClass("doing")

            $("#TrackingDiv h3 span").html(trackingCode);
            //ajax to /php/tracking.php
            if (trackingCode == "_ENTREGUE____") {
                $("#Arrived").addClass("done")
                $("#Sended").addClass("done")
            }
            $.ajax({
                url: "/api/get/tracking.php",
                data: {
                    "trackingCode": trackingCode
                },
                success: function (data) {
                    if (data != null) {
                        console.log(data);
                        //data.eventos for each
                        $.each(data.eventos, function (i, evento) {
                            if (evento.status == "Objeto entregue ao destinatário") {
                                $("#Arrived").addClass("done")
                                $("#Sended").addClass("done")
                                console.log(evento.status)

                            } else {
                            }
                            var step = "<section>" +
                                "<span>" + evento.status + "</span>" +
                                "<label>" + evento.local + "</label>" +
                                "<small>" + evento.data + " " + evento.hora + "</small>" +
                                (i == 0 ? "<i class='fa-solid fa-chevron-down' id='moreTracking' onclick='moreTracking()'></i>" : "") +
                                "</section>";
                            $("#TrackingDiv").append(step);
                        })
                    }

                }
            })
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



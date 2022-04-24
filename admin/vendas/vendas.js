var BuyCode_ = "";
$(document).ready(function () {
    $.get("/admin/vendas/getVendas.php", function (data) {
        data = JSON.parse(data);

        $.each(data, function (i, item) {
            var statusS;

            switch (item.status) {
                case 1:
                    statusS = "Aguardando pagamento";
                    break;
                case 2:
                    statusS = "Em análise";
                    break;
                case 3:
                    statusS = "Paga";
                    break;
                case 4:
                    statusS = "Disponível";
                    break;
                case 5:
                    statusS = "Em disputa";
                    break;
                case 6:
                    statusS = "Devolvida";
                    break;
                case 7:
                    statusS = "Cancelada";
                    break;
                case 8:
                    statusS = "Debitado";
                    break;
                case 9:
                    statusS = "Retenção temporária";
                    break;
                default:
            }
            var date = new Date(item.buyDate);

            $("#vendas").append("<tr><td>" + statusS + "</td><td>" + item.client.name +
                "</td><td>" + item.code + "</td><td>" + item.totalAmount + "</td><td>" + item.buyDate + "</td><td onclick='getSale(\"" + item.code + "\")'>-></td></tr>");
        })
    })

    $("#shippingCodeBtn").click(function () {
        var shipCode = $("#shippingCode").val();
        $.get("/admin/vendas/setTrackingCode.php?code=" + BuyCode_ + "&shippingCode=" + shipCode, function (data) {

        })
    })
})

function getSale(code) {
    $.get("/admin/vendas/getSale.php?code=" + code, function (data) {
        data = JSON.parse(data);
        BuyCode_ = code;
        console.log(data);
        $("#clientName").html(data.client.name + " " + data.client.lastName);
        $("#clientEmail").html(data.client.email);
        $("#clientPhone").html(data.client.phone);
        $("#clientCPF").html(data.client.cpf);
        $("#clientDate").html(data.client.Borndate);
        $("#ShippingStreet").html(data.sale.shipping.address.street) + " " + data.sale.shipping.address.number;
        $("#ShippingCity").html(data.sale.shipping.address.city + " " + data.sale.shipping.address.state);
        $("#ShippingCEP").html(data.sale.shipping.address.postalCode);
        $("#ShippingComplement").html(data.sale.shipping.address.complement);
        $("#shippingCode").val(data.sale.trackingCode);

        if ((data.sale.items.item.id) == undefined) {
            var items = data.sale.items.item;
        } else {
            var items = [data.sale.items.item];
        }
        $("#prodList").html("");
        $.each(items, function (i, item) {
            console.log(item);
            $.get("/admin/product/getProdById.php?id=" + item.id, function (p) {
                p = JSON.parse(p);
                var images = JSON.parse(p.imgs);
                var apItem = "<div class='prod'>"
                    + "<div class='prodImg'>"
                    + "<img src='" + images["1"] + "'>"
                    + "</div>"
                    + "<span>" + item.description + "</span>"
                    + "<span>" + item.quantity + "</span>"
                    + "<span>" + item.amount + "</span>"
                    + "</div>";
                $("#prodList").append(apItem);

            })
        })
        var sale = data.sale;
        var tipoEnvio = (sale.shipping.type == "1" ? "PAC" : (sale.shipping.type == "2" ? "SEDEX" : "Outro"));

        var statusS;
        switch (parseInt(sale.status)) {
            case 1:
                statusS = "Aguardando pagamento";
                break;
            case 2:
                statusS = "Em análise";
                break;
            case 3:
                statusS = "Paga";
                break;
            case 4:
                statusS = "Disponível";
                break;
            case 5:
                statusS = "Em disputa";
                break;
            case 6:
                statusS = "Devolvida";
                break;
            case 7:
                statusS = "Cancelada";
                break;
            case 8:
                statusS = "Debitado";
                break;
            case 9:
                statusS = "Retenção temporária";
                break;
            default:
        }

        $("#BuyDate").html("Data de compra: " + sale.date);
        $("#LastEventDate").html("Última Atualização" + sale.lastEventDate);
        $("#BuyStatus").html("Status: " + statusS);
        $("#BuyCode").html("Código da Compra: " + sale.code);
        $("#FeeAmount").html("Taxas: R$" + (parseFloat(sale.creditorFees.intermediationFeeAmount) + parseFloat(sale.creditorFees.intermediationRateAmount)));
        $("#Discount").html("Desconto: R$" + sale.discountAmount);
        $("#TotalAmount").html("Valor Total: R$" + sale.grossAmount);
        $("#NetAmount").html("Lucro Líquido: R$" + (parseFloat(sale.netAmount) - parseFloat(sale.shipping.cost)).toFixed(2));
        $("#PaymentLink").html("<a target='blank' href='" + sale.paymentLink + "'>" + "Link de Pagamento" + "</a>");
        $("#ShippingType").html("Tipo de Envio: " + tipoEnvio);
        $("#ShippingCost").html("Valor do Envio: R$" + sale.shipping.cost);

        switch (sale.paymentMethod.type) {
            case "1":
                $("#PaymentMethod").html(" Forma de pagamento: Cartão de Crédito");
                break;
            case "2":
                $("#PaymentMethod").html("Forma de pagamento: Boleto");
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
    })
}

/* 
<div class="prod">

                    <div class="prodImg">
                        <img src="">
                    </div>
                    <span>Nome</span>
                    <span>Quantidade</span>
                    <span>Valor</span>
                    <span>Total</span>
                </div>
                */
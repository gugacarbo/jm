
var orderG = 'true';
var filterG = 'id'
var textSearch = ''
var maxpPage = 20;
var page = 0;

$(document).ready(function () {
    page = 0;
    search();
    $("#btnSearch").click(function () {
        page = 0;
        search();

    })
    $('#textSearch').keyup(function (e) {
        if (e.keyCode == 13) {
            page = 0;
            search();
        }
    });


    $(".filterI").on("click", function () {
        if ($(this).hasClass("selected")) {
            $(this).find("i").toggleClass("up");
        } else {
            $("i").each(function (i, d) {
                $(d).removeClass("up")
            })
            $(".filterI").each(function (i, d) {
                $(d).removeClass("selected")
            })
            $(this).addClass("selected");
        }

        page = 0;
        search($(this).find("i").hasClass("up"), $(this).attr("name"));
    })

    $("#addTrackingCode").on("click", function () {
        var code = $("#TrackingCode").val();
        if (code.length == 13) {
            var id = $(this).attr("data-id");
            $.get("addTrackingCode.php", { code, id }, function (data) {
            })
        }
    })
    $("#closeModalPurshase").on("click", function () {
        console.log("close")
        $("#ModalPurshase").removeClass("modalOpen");

    })
})


function search(order_ = orderG, filter_ = filterG,) {
    var textSearch = $("#textSearch").val() || '';
    orderG = order_;
    filterG = filter_;

    var c = {
        "filter": filterG,
        "order": orderG,
        "text": textSearch
    }

    $("#purshasesList").fadeOut(400, function () {
        $.get("getPurshases.php", c, function (data) {
            var compras = JSON.parse(data);

            $("#totalPurshases b").html(compras.length);

            if (page == 0) {
                $("#PageCounter").empty();
                for (var i = 0; i < Math.ceil((compras.length) / maxpPage); i++) {
                    $("#PageCounter").append(`<span onclick="changePage(${i})">${i + 1}</span>`)
                }
            }

            $(".pageSelected").removeClass("pageSelected");
            $("#PageCounter span:nth-child(" + (page + 1) + ")").addClass("pageSelected");


            $("#purshasesList").empty();
            var purshNum = compras.length;
            if (purshNum == 0) {
                $("#purshasesList").append(`<span style='width:100%; text-align:center; padding: 10px 0; '>Nenhum resultado encontrado</span>`);

            }
            $.each(compras.slice(page * maxpPage, (page + 1) * maxpPage), function (index, compra) {
                createPurshase(compra)
            })
        }).then(_ => {
            $("#purshasesList").fadeIn(500);
        })
    })

}


function changePage(i) {
    page = i;
    search();

}


function createPurshase(compra) {
    var status = "";
    switch (compra.status) {
        case 1:
            status = "Aguardando Pagamento";
            break;
        case 2:
            status = "Pagamento em Análise";
            break;
        case 3:
            status = "Pagamento Aprovado";
            break;
        case 4:
            status = "Finalizada";
            break;
        case 5:
            status = "Em Disputa";
            break;
        case 6:
            status = "Devolvida";
            break;
        case 7:
            status = "Cancelada";
            break;
        case 8:
            status = "Debitado";
            break;
        case 9:
            status = "Retenção Temporária";
            break;
        default:
            status = "";
            break;
    }
    var date = new Date(compra.buyDate);
    var d = date.getDate();
    var m = date.getMonth();
    m += 1;  // JavaScript months are 0-11
    var y = date.getFullYear();
    var pursh = `
    <div class="purshase" id="Purshase${compra.id}">
        <span>${compra.id}</span>
        <span>${d}/${m}/${y}</span>
        <span onclick="modalPurshase(${compra.id})">${compra.client.name + " " + compra.client.lastName}</span>
        <span>R$ ${compra.totalAmount.toFixed(2).replace(".", ",")}</span>
        <span>${status}</span>
        <span>${compra.code}</span>
        <span><i class="fas fa-ellipsis-h" onclick="modalPurshase(${compra.id})"></i></span>
</div>`
    $("#purshasesList").append(pursh);
}

function modalPurshase(id = 0) {
    if (id > 0) {
        $.get("getPurshase.php", { "Bid": id }, function (data) {
            data = JSON.parse(data);
            console.log(data)
            var purshase = (data["purshase"]);
            var client = (data["client"]);
            var products = (data["products"]);
            var payload = JSON.parse(purshase.rawPayload);

            $("#addTrackingCode").attr("data-id", id)
            console.log(client)
            $("#ClientName b").html(client.name + " " + client.lastName);
            $("#ClientEmail b").html(client.email);
            $("#ClientPhone b").html(client.phone);
            $("#ClientCPF b").html(client.cpf);

            console.log(client.bornDate)
            var date = new Date(client.bornDate.replace(/-/g, '\/'));
            console.log(date)
            var d = date.getDate();
            var m = date.getMonth();
            m += 1;  // JavaScript months are 0-11
            var y = date.getFullYear();

            $("#ClientBorndate b").html(d + "/" + m + "/" + y);

            console.log(products)
            $("#PurshaseProducts").empty();

            $.each(products, function (index, product) {
                if (product.id == null) {


                    $("#PurshaseProducts").append(`
                <div class="product">
                <div class="image">    
                <img src="noImage.png" alt="">
                </div>
                <label class="name">
                    <span>${product.description}</span>
                </label>
                <label>
                    <span>${product.quantity}</span>
                </label>
                <label>
                    <span>R$ ${(product.amount * product.quantity).toFixed(2).replace(".", ",")}</span>
                    </label>
                    </div>
                `)
                } else {

                    var images = JSON.parse(product.imgs);
                    $("#PurshaseProducts").append(`
                <div class="product">
                <div class="image">    
                <img src="${images['1']}" alt="">
                </div>
                <label class="name">
                    <span>${product.description}</span>
                </label>
                <label>
                    <span>${product.quantity}</span>
                </label>
                <label>
                    <span>R$ ${(product.amount * product.quantity).toFixed(2).replace(".", ",")}</span>
                    </label>
                    </div>
                `)
                }
            })
            console.log(purshase)

            purshase.trackingCode != "" ? $("#TrackingCode").val(purshase.trackingCode) : $("#TrackingCode").val("");
            var status = "";
            $("#StatusSelect").html("");
            switch (parseInt(purshase.status)) {
                case 1:
                    $("#PurshaseStatus b").html("Aguardando Pagamento");
                    $("#StatusSelect").append(`<option selected>Aguardando Pagamento</option>`)
                    $("#StatusSelect").append(`<option value="3" >Paga</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 2:
                    $("#PurshaseStatus b").html("Pagamento em Análise");
                    $("#StatusSelect").append(`<option value="1" selected>Pagamento em Análise</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 3:

                    $("#PurshaseStatus b").html("Pagamento Aprovado");
                    $("#StatusSelect").append(`<option value="1" selected>Pagamento Aprovado</option>`)
                    $("#StatusSelect").append(`<option value="5" >Em Disputa</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 4:
                    $("#PurshaseStatus b").html("Finalizada");
                    $("#StatusSelect").append(`<option value="1" selected>Finalizada</option>`)
                    $("#StatusSelect").append(`<option value="5" >Em Disputa</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 5:
                    $("#PurshaseStatus b").html("Em Disputa");
                    $("#StatusSelect").append(`<option  selected>Em Disputa</option>`)
                    $("#StatusSelect").append(`<option value="4" >Finalizada</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 6:
                    $("#PurshaseStatus b").html("Devolvida");
                    $("#StatusSelect").append(`<optionselected>Devolvida</option>`)
                    break;
                case 7:
                    $("#PurshaseStatus b").html("Cancelada");
                    $("#StatusSelect").append(`<option  selected>Cancelada</option>`)

                    break;
                case 8:
                    $("#PurshaseStatus b").html("Debitado");
                    $("#StatusSelect").append(`<option selected>Debitado</option>`)
                    $("#StatusSelect").append(`<option value="3" >Paga</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)

                    break;
                case 9:
                    $("#PurshaseStatus b").html("Retenção Temporária");
                    $("#StatusSelect").append(`<option value="1" selected>Retenção Temporária</option>`)
                    break;
                default:
                    console.log("anao")
                    break;
            }

            console.log(payload)

            var d = new Date(payload.date);
            var d2 = new Date(payload.lastEventDate);
            $("#PurshaseDate b").html(d.toLocaleString());
            $("#PurshaseLastUpdate b").html(d2.toLocaleString());
            $("#PurshaseCode a").html(payload.code);
            $("#PurshaseCode a").attr("href", "http://localhost/checkStatus?code=" + payload.code);
            $("#PurshaseTotalAmount b").html((payload.grossAmount));
            $("#PurshaseFeeAmount b").html(payload.creditorFees.intermediationFeeAmount);
            $("#PurshaseDiscount b").html(payload.discountAmount);
            $("#PurshaseNetAmount b").html(payload.netAmount);
            $("#PurshaseShippingPrice b").html(payload.shipping.cost);

            switch (parseInt(payload.paymentMethod.type)) {
                case 1:
                    $("#PurshasePaymentMethod b").html("Cartão de Crédito");
                    break;
                case 2:
                    $("#PurshasePaymentMethod b").html("Boleto");
                    break;
                case 3:
                    $("#PurshasePaymentMethod b").html("Débito Online (TEF)");
                    break;
                case 4:
                    $("#PurshasePaymentMethod b").html("Saldo PagSeguro");
                    break;
                case 5:
                    $("#PurshasePaymentMethod b").html("Oi Paggo");
                    break;
                case 6:
                    $("#PurshasePaymentMethod b").html("Depósito em Conta");
                    break;
                case 11:
                    $("#PurshasePaymentMethod b").html("Pix");
                    break;
                default:
            }
            $("#AddressStreet b").html(payload.shipping.address.street + " - " + payload.shipping.address.number);
            $("#AddressComplement b").html(payload.shipping.address.complement);
            $("#AddressDistrict b").html(payload.shipping.address.district);
            $("#AddressCity b").html(payload.shipping.address.city + " - " + payload.shipping.address.state)
            $("#AddressCep b").html(payload.shipping.address.postalCode);
            $("#ShippingType b").html(payload.shipping.type == "1" ? "PAC" : "SEDEX");

        }).then(function () {
            $("#ModalPurshase").addClass("modalOpen");
        })
    }
}

var timerDel;
function deletePurshase(id) {
    $(".deleteConfirm").remove();
    $("#Purshase" + id + " span:last-child").append(`
        <div class="deleteConfirm">
        <button onclick='del(${id})'>Deletar?</button>
        </div>
    `)
    clearTimeout(timerDel);
    timerDel = setTimeout(() => {
        $(".deleteConfirm").fadeOut(500, function () {
            $(".deleteConfirm").remove();
        })
    }, 3000);

}

function del(id) {

    $.get("deletePurshase.php", { "id": id }, function (data) {
        data = JSON.parse(data);
        if (data.status == "success") {
            $("#Purshase" + id).remove();
        } else {
        }
    })
}

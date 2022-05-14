
var orderG = '';
var filterG = ''
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
    $("#closeModalPurshcase").on("click", function () {
        console.log("close")
        $("#ModalPurshcase").removeClass("modalOpen");

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
    $("#purshcasesList").fadeOut(400, function () {
        $.get("getPurshcases.php", c, function (data) {
            var compras = JSON.parse(data);

            $("#totalPurshcases b").html(compras.length);

            if (page == 0) {
                $("#PageCounter").empty();
                for (var i = 0; i < Math.ceil((compras.length) / maxpPage); i++) {
                    $("#PageCounter").append(`<span onclick="changePage(${i})">${i + 1}</span>`)
                }
            }

            $(".pageSelected").removeClass("pageSelected");
            $("#PageCounter span:nth-child(" + (page + 1) + ")").addClass("pageSelected");


            $("#purshcasesList").empty();
            var purshNum = compras.length;
            if (purshNum == 0) {
                $("#purshcasesList").append(`<span style='width:100%; text-align:center; padding: 10px 0; '>Nenhum resultado encontrado</span>`);

            }
            $.each(compras.slice(page * maxpPage, (page + 1) * maxpPage), function (index, compra) {
                createPurshcase(compra)
            })
        }).then(_ => {
            $("#purshcasesList").fadeIn(500);
        })
    })

}


function changePage(i) {
    page = i;
    search();

}


function createPurshcase(compra) {
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
    <div class="purshcase" id="Purshcase${compra.id}">
        <span>${compra.id}</span>
        <span>${d}/${m}/${y}</span>
        <span onclick="modalPurshcase(${compra.id})">${compra.client.name + " " + compra.client.lastName}</span>
        <span>R$ ${compra.totalAmount.toFixed(2).replace(".", ",")}</span>
        <span>${status}</span>
        <span>${compra.code}</span>
        <span><i class="fas fa-ellipsis-h" onclick="modalPurshcase(${compra.id})"></i></span>
</div>`
    $("#purshcasesList").append(pursh);
}

function modalPurshcase(id = 0) {
    if (id > 0) {
        $.get("getPurshcase.php", { "Bid": id }, function (data) {
            data = JSON.parse(data);
            console.log(data)
            var purshcase = (data["purshcase"]);
            var client = (data["client"]);
            var products = (data["products"]);
            var payload = JSON.parse(purshcase.rawPayload);

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
            $("#PurshcaseProducts").empty();

            $.each(products, function (index, product) {
                if (product.id == null) {


                    $("#PurshcaseProducts").append(`
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
                    $("#PurshcaseProducts").append(`
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
            console.log(purshcase)

            purshcase.trackingCode != "" ? $("#TrackingCode").val(purshcase.trackingCode) : $("#TrackingCode").val("");
            var status = "";
            $("#StatusSelect").html("");
            switch (parseInt(purshcase.status)) {
                case 1:
                    $("#PurshcaseStatus b").html("Aguardando Pagamento");
                    $("#StatusSelect").append(`<option selected>Aguardando Pagamento</option>`)
                    $("#StatusSelect").append(`<option value="3" >Paga</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 2:
                    $("#PurshcaseStatus b").html("Pagamento em Análise");
                    $("#StatusSelect").append(`<option value="1" selected>Pagamento em Análise</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 3:

                    $("#PurshcaseStatus b").html("Pagamento Aprovado");
                    $("#StatusSelect").append(`<option value="1" selected>Pagamento Aprovado</option>`)
                    $("#StatusSelect").append(`<option value="5" >Em Disputa</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 4:
                    $("#PurshcaseStatus b").html("Finalizada");
                    $("#StatusSelect").append(`<option value="1" selected>Finalizada</option>`)
                    $("#StatusSelect").append(`<option value="5" >Em Disputa</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 5:
                    $("#PurshcaseStatus b").html("Em Disputa");
                    $("#StatusSelect").append(`<option  selected>Em Disputa</option>`)
                    $("#StatusSelect").append(`<option value="4" >Finalizada</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)
                    break;
                case 6:
                    $("#PurshcaseStatus b").html("Devolvida");
                    $("#StatusSelect").append(`<optionselected>Devolvida</option>`)
                    break;
                case 7:
                    $("#PurshcaseStatus b").html("Cancelada");
                    $("#StatusSelect").append(`<option  selected>Cancelada</option>`)

                    break;
                case 8:
                    $("#PurshcaseStatus b").html("Debitado");
                    $("#StatusSelect").append(`<option selected>Debitado</option>`)
                    $("#StatusSelect").append(`<option value="3" >Paga</option>`)
                    $("#StatusSelect").append(`<option value="3" >Cancelada</option>`)

                    break;
                case 9:
                    $("#PurshcaseStatus b").html("Retenção Temporária");
                    $("#StatusSelect").append(`<option value="1" selected>Retenção Temporária</option>`)
                    break;
                default:
                    console.log("anao")
                    break;
            }

            console.log(payload)

            var d = new Date(payload.date);
            var d2 = new Date(payload.lastEventDate);
            $("#PurshcaseDate b").html(d.toLocaleString());
            $("#PurshcaseLastUpdate b").html(d2.toLocaleString());
            $("#PurshcaseCode a").html(payload.code);
            $("#PurshcaseCode a").attr("href", "http://localhost/checkStatus?code=" + payload.code);
            $("#PurshcaseTotalAmount b").html((payload.grossAmount));
            $("#PurshcaseFeeAmount b").html(payload.creditorFees.intermediationFeeAmount);
            $("#PurshcaseDiscount b").html(payload.discountAmount);
            $("#PurshcaseNetAmount b").html(payload.netAmount);
            $("#PurshcaseShippingPrice b").html(payload.shipping.cost);

            switch (parseInt(payload.paymentMethod.type)) {
                case 1:
                    $("#PurshcasePaymentMethod b").html("Cartão de Crédito");
                    break;
                case 2:
                    $("#PurshcasePaymentMethod b").html("Boleto");
                    break;
                case 3:
                    $("#PurshcasePaymentMethod b").html("Débito Online (TEF)");
                    break;
                case 4:
                    $("#PurshcasePaymentMethod b").html("Saldo PagSeguro");
                    break;
                case 5:
                    $("#PurshcasePaymentMethod b").html("Oi Paggo");
                    break;
                case 6:
                    $("#PurshcasePaymentMethod b").html("Depósito em Conta");
                    break;
                case 11:
                    $("#PurshcasePaymentMethod b").html("Pix");
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
            $("#ModalPurshcase").addClass("modalOpen");
        })
    }
}

var timerDel;
function deletePurshcase(id) {
    $(".deleteConfirm").remove();
    $("#Purshcase" + id + " span:last-child").append(`
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

    $.get("deletePurshcase.php", { "id": id }, function (data) {
        data = JSON.parse(data);
        if (data.status == "success") {
            $("#Purshcase" + id).remove();
        } else {
        }
    })
}

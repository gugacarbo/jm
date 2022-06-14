

$(document).ready(function () {

    if ($.urlParam("page")) {
        $(".menuSelect").removeClass("menuSelect");
        var page = $.urlParam("page");
        $('.relatoryDisplay .relatoryBox').each(function () {
            $(this).hide();
        })
        $("." + page).css('display', 'grid');
        goToSearch('');
    }

    getFinances();
    getNps();
    getOrders()




})


function relelatoryMenu(el, page) {
    $(".menuSelect").removeClass("menuSelect");
    $(el).addClass("menuSelect");
    $('.relatoryDisplay .relatoryBox').each(function () {
        $(this).hide();
    })
    $("." + page).css('display', 'grid');
}

function getFinances() {
    $.get("/admin/api/get/relatory/finances.php", function (data) {
        $("#relatoryTotalInvoicing").text(data.totalInvoicing.toFixed(2).replace(".", ','));
        $("#relatoryMonthInvoicing").text(data.monthInvoicing.toFixed(2).replace(".", ','));

        $("#relatoryFutureReceivments").text(data.toReceive.toFixed(2).replace(".", ','));
        $("#relatoryMargin").text((data.av_margin).toFixed(1).replace(".", ','));
        $("#relatoryMonthNet").text(data.monthNet.toFixed(2).replace(".", ','));
        $("#relatoryTicket").text(data.av_ticket.toFixed(2).replace(".", ','));
        $("#relatoryAvCost").text(data.av_cost.toFixed(2).replace(".", ','));
        $("#relatoryAvPrice").text(data.av_price.toFixed(2).replace(".", ','));

        $.each(data.listData, function (index, item) {


            var totalCost = parseFloat(item.totalCost) - parseFloat(item.rawPayload.shipping.cost);
            totalCost += parseFloat(item.rawPayload.creditorFees.intermediationRateAmount);
            totalCost += parseFloat(item.rawPayload.creditorFees.intermediationFeeAmount);

            var FinalNet = parseFloat(item.rawPayload.netAmount) - parseFloat(item.totalCost);
            var paymentDate = new Date(item.paymentDate);
            var escrowEndDate = new Date(item.rawPayload.escrowEndDate);

            $("#RelatoryPurchaseList").append(`
                                                <div class="listItem">
                                                    <span>${item.id}</span>
                                                    <span>${paymentDate.getDate()}/${paymentDate.getMonth() + 1}/${paymentDate.getFullYear()}</span>
                                                    <span>${escrowEndDate.getDate()}/${escrowEndDate.getMonth() + 1}/${escrowEndDate.getFullYear()}</span>
                                                    <span>${parseFloat(item.rawPayload.grossAmount).toFixed(2).replace(".", ',')}</span>
                                                    <span>${parseFloat(totalCost).toFixed(2).replace(".", ',')}</span>
                                                    <span>${parseFloat(item.rawPayload.shipping.cost).toFixed(2).replace(".", ',')}</span>
                                                    <span>${parseFloat(FinalNet).toFixed(2).replace(".", ',')}</span>
                                                    <span><i onclick="changePage('purchases');goToSearch('id=${item.id}');" class="fa-solid fa-ellipsis"></i></span>
                                                </div>
                                                `)
        })
    })
}


function getOrders() {
    $.get("/admin/api/get/relatory/orders.php", function (data) {



        $.each(data.ordersList, function (index, item) {
            item['rawPayload'] = JSON.parse(item['rawPayload']);
            var FinalNet = parseFloat(item.rawPayload.netAmount) - parseFloat(item.totalCost);
            var buyDate = new Date(item.buyDate);
            var label = '';

            switch (parseInt(item['status'])) {
                case 1:
                    label = 'Aguardando Pagamento'
                    break;
                case 2:
                    label = 'Aguardando Pagamento'
                    break;
                case 3:
                    label = 'Pagamento Aprovado'
                    break;
                case 4:
                    label = 'Pagamento Aprovado'
                    break;
                case 5:
                    label = 'Em Disputa'
                    break;
                case 6:
                    label = 'Devolvida'
                    break;
                case 7:
                    label = 'Pagamento Cancelado'
                    break;
                case 8:
                    label = 'Devolvida'
                    break;
                case 9:
                    label = 'Em Disputa'
                    break;
            }
            var TotalProds = 0;
            $.each(item.rawPayload.items, function (index, item) {
                TotalProds += parseInt(item.quantity);
            })

            $("#RelatoryOrdersList").append(`
            <div class="listItem">
                <span>${item.id}</span>
                <span>${buyDate.getDate()}/${buyDate.getMonth() + 1}/${buyDate.getFullYear()}</span>
                <span>${label}</span>
                <span>${TotalProds}</span>
                <span>${parseFloat(item.rawPayload.grossAmount).toFixed(2).replace(".", ',')}</span>
                <span><i onclick="changePage('purchases');goToSearch('id=${item.id}');" class="fa-solid fa-ellipsis"></i></span>
            </div>
            `)
        })

        removeData(ordersChart);
        Object.keys(data.ordersChart).forEach(function (key) {
            var label = '';
            switch (parseInt(key)) {
                case 1:
                    label = 'Aguardando Pagamento'
                    break;
                case 2:
                    label = 'Aguardando Pagamento'
                    break;
                case 3:
                    label = 'Pagamento Aprovado'
                    break;
                case 4:
                    label = 'Pagamento Aprovado'
                    break;
                case 5:
                    label = 'Em Disputa'
                    break;
                case 6:
                    label = 'Devolvida'
                    break;
                case 7:
                    label = 'Pagamento Cancelado'
                    break;
                case 8:
                    label = 'Devolvida'
                    break;
                case 9:
                    label = 'Em Disputa'
                    break;
            }

            ordersChart.data.labels.push(label)
            ordersChart.data.datasets[0].data.push(data.ordersChart[key])

        })
    })
}


function getNps() { //* + Fechamentos (Relatórios)

    $.get("/admin/api/get/relatory/nps.php", function (data) {

        $.each(data.relatoryList, function (index, item) {
            var date = new Date(item.date);

            if (date.getDate() > 15) {
                var refMonth = (date.toLocaleString("pt-BR", { month: "short" })).replace(".", "") + '/' + date.getFullYear().toString().substr(-2);
                refMonth = refMonth + ".1"
            } else {
                var pastMonth = new Date(date.getFullYear(), date.getMonth() - 1, date.getDate());
                var refMonth = (pastMonth.toLocaleString("pt-BR", { month: "short" })).replace(".", "") + '/' + pastMonth.getFullYear().toString().substr(-2);
            }

            //! Fechamento

            $("#RelatoryFinalRelatoryList").append(`
                <div class="listItem">

                    <span>${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}</span>
                    <span>${refMonth}</span>
                    <span>R$ ${(parseFloat(item.invoicing) + parseFloat(item.canceled)).toFixed(2).replace('.', ',')}</span>
                    <span>R$ ${item.cost.toFixed(2).replace('.', ',')}</span>
                    <span>R$ ${item.canceled.toFixed(2).replace('.', ',')}</span>
                    <span>R$ ${item.netAmount.toFixed(2).replace('.', ',')}</span>
                    <span>R$ ${item.netAmountDs.toFixed(2).replace('.', ',')}</span>
                    <span>R$ ${(parseFloat(item.netAmount) - parseFloat(item.netAmountDs)).toFixed(2).replace('.', ',')}</span>
                    <span><a href="${item.fileName}"><i class="fa-solid fa-ellipsis"></i></a></span>

                </div>
            
            `)
        })

        $("#relatoryNpsCanceling").text(data.CanceladasMes)
        $("#relatoryNpsCanceled").text(data.totalCanceladas)
        $("#relatoryNpsMonthMailing").text(data.newsletterMes)
        $("#relatoryNpsMailing").text(data.totalNewsletter)
        $("#relatoryNpsNps").text(data.nps)
        $("#relatoryNpsMonthNps").text(data.monthNps)
        $("#relatoryNpsClientMonth").text(data.monthClient)
        $("#relatoryNpsTotalClient").text(data.totalClient)


        $.each(data.visitorChart.visitor, function (index, item) {
            addData(VisitorsChart, 0, item.date, item.value);
            addData(VisitorsChart, 1, data.visitorChart.mailling[index].date, data.visitorChart.mailling[index].value);
        })



        $.each(data.listCanceladas, function (index, item) {
            item['rawPayload'] = JSON.parse(item['rawPayload']);
            var FinalNet = parseFloat(item.rawPayload.netAmount) - parseFloat(item.totalCost);
            var paymentDate = new Date(item.paymentDate);
            var lastEventDate = new Date(item.rawPayload.lastEventDate);

            $("#RelatoryNpsCanceledList").append(`
            <div class="listItem">
                <span>${item.id}</span>
                <span>${paymentDate.getDate()}/${paymentDate.getMonth() + 1}/${paymentDate.getFullYear()}</span>
                <span>${lastEventDate.getDate()}/${lastEventDate.getMonth() + 1}/${lastEventDate.getFullYear()}</span>
                <span>${parseFloat(item.rawPayload.grossAmount).toFixed(2).replace(".", ',')}</span>
                <span>${parseFloat(item.rawPayload.shipping.cost).toFixed(2).replace(".", ',')}</span>
                <span><i onclick="changePage('purchases');goToSearch('id=${item.id}');" class="fa-solid fa-ellipsis"></i></span>
            </div>
            `)
        })

        var max = 0;

        $.each(data.genderChart.male, function (index, item) {
            addData(GenderChart, 1, '', data.genderChart.female[index])
            addData(GenderChart, 0, '', data.genderChart.male[index])
            if (data.genderChart.male[index] > max) {
                max = data.genderChart.male[index];
            }
            if (data.genderChart.female[index] > max) {
                max = data.genderChart.female[index];
            }

            GenderChart.config.options.scales.x.max = parseInt(max);
            GenderChart.config.options.scales.x.min = -parseInt(max);
        })
    })
}



function addData(chart, dataset = 0, label, data) {
    if (label == '') {
        chart.data.datasets[dataset].data.push(data);
    } else {

        chart.data.datasets[dataset].data.push({ x: label, y: data });
    }
    chart.update();
}

function removeData(chart) {
    chart.data.labels = [];
    chart.data.datasets.forEach((dataset) => {
        dataset.data = [];
    });
    chart.update();
}



//* Pedidos Chart
var ordersChart = new Chart(document.getElementById('OrdersChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: [
        ],
        datasets: [{
            label: 'My First Dataset',
            data: [],
            backgroundColor: [
                'rgb(220, 20, 86)',
                '#0066d4',
                '#0066d4',
                '#98d400',
                '#d45500',
                '#00d4aa',
            ],
            hoverOffset: 4
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});





//> Visitors Chart

var VisitorsChart = new Chart(document.getElementById('VisitorsChart').getContext('2d'), {
    data: {
        datasets: [{
            type: 'line',
            label: ['Visitantes'],
            data: [],
            backgroundColor: ["#00cbda"],

            color: ["#00cbda"],

            borderColor: '#00cbda',
            tension: 0.10

        },
        {
            type: 'line',
            label: ['Mailing'],
            data: [],

            backgroundColor: ["#ff7e00"],
            color: ["#ff7e00"],

            borderColor: '#ff7e00',
            tension: 0.10

        }],
    },
    options: {
        plugins: {
            legend: {
                display: true,
                labels: {
                    color: '#000',
                    weight: "bold",
                    family: "Kdam Thmor Pro",
                    size: '13pt',

                }
            },
            title: {
                display: true,
                text: 'Visitantes e inscrições na lista de email',
                font: {
                    size: 15,
                    family: 'tahoma',
                    weight: 'normal',
                },
            }
        },
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'month'
                },
                ticks: {
                    // Here's where the magic happens:
                    callback: function (label, index, labels) {
                        return translate_this_label(label);
                    }
                },
                min: '',
                max: '',
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                min: 0,

                ticks: {
                    beginAtZero: true,

                }
            },

        }
    }
});



//? Gender Chart

var GenderChart = new Chart(document.getElementById('GenderChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ["70 anos +", "65 a 69 anos", "60 a 64 anos", "65 a 59 anos", "50 a 54 anos", "45 a 49 anos", "40 a 44 anos", "35 a 39 anos", "30 a 34 anos", "29 a 30 anos", "25 a 29 anos", "20 a 24 anos", "15 a 19 anos"],
        datasets: [
            {
                label: 'Masc',
                data: [],
                backgroundColor: "#0aa2ee",
                stack: 'Stack 0',
                barPercentage: 0.5,
                borderRadius: 010,

            },
            {
                label: 'Fem',
                data: [],
                backgroundColor: "#b800d9",
                stack: 'Stack 0',
                stacked: true,
                barPercentage: 0.5,
                borderRadius: 010,
            },
        ]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,

        indexAxis: 'y',

        scales: {
            x: {
                stacked: true,
                ticks: {
                    callback: function (label, index, labels) {


                        return ((parseFloat(label) < 0 ? (parseFloat(label) * -1) : label) + "%");
                    }
                },
                offset: true,
            },
            y: {
                stacked: true,
            }

        },


        interaction: {
            intersect: false,
        },



        elements: {
            bar: {
                borderWidth: 2,
                width: 30,
            }
        },

    }

});
/**
var HomeChart = new Chart(
    document.getElementById('HomeChart'),
    {
        type: 'line',

        data: {
            datasets: [{
                data: [],
                fill: {
                    target: 'origin',
                    above: 'rgba(7, 158, 57, 0.15)'
                },
                borderColor: 'rgb(7, 158, 57)',
                tension: 0.15
            }],
        },
        options: {
            scales: {
                x: {
                    min: '2022-05-01 00:00:00',
                    max: '2022-05-29 00:00:00',
                },
                y:{
                    min: 0,
                    max: 1000
                }
                
            }
        }

    }
);
 */


var HomeChart = new Chart(
    document.getElementById('HomeChart'), {
    type: 'line',
    data: {
        datasets: [{
            label: 'Faturamento',
            data: [],
            backgroundColor: [],
            fill: {
                target: 'origin',
                above: 'rgba(7, 158, 57, 0.15)'
            },
            borderColor: 'rgb(7, 158, 57)',
            tension: 0.15

        }],
    },
    options: {
        plugins: {
            legend: {
                display: false,
                labels: {
                    color: 'rgb(255, 99, 132)'
                }
            }
        },
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'day'
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
                min: 0,
                title: {
                    color: '#000',
                    display: true,
                    text: 'Faturamento'
                },
                ticks: {
                    beginAtZero: true,
                    callback: function (value, index, values) {
                        if (parseInt(value) >= 1000) {
                            return 'R$ ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        } else {
                            return 'R$ ' + value;
                        }
                    }
                }
            }
        }
    }
});
function getHomeInfo() {
    $.get("/admin/api/get/getHomeInfo.php", function (data) {
        if (data.status >= 200 && data.status < 300) {
            $("#HomeInfoAprovadas").text(data.Aprovadas)
            $("#HomeInfoCanceladas").text(data.Canceladas)
            $("#HomeInfoAguardandoEnvio").text(data.AguardandoEnvio)
            $("#HomeInfoNaoFinalizados").text(data.NaoPagos)
            $("#HomeInfoAguardandoPagamento").text(data.AguardandoPagamento)
            $("#HomeInfoNps").text((data.Nps).toFixed(2))
            $("#HomeInfoCanceling").text((data.canceling))
            $("#HomeInfoVisitas").text((data.visitas))
        }
    })
}

$(document).ready(function () {
    getHomeChart("month")
    getHomeInfo()
})


function goToSearch(search) {
    window.history.pushState("object or string", "Title", "?" + search);
}


function getHomeChart(interval, el = "#monthFirst") {

    $(".chartBtnActive").removeClass("chartBtnActive");
    $(el).addClass("chartBtnActive");

    $.get("/admin/api/get/getChart.php", { interval }, function (data) {
        if (data.status >= 200 && data.status < 300) {
            removeData(HomeChart);
            Object.keys(data.data).forEach(function (key) {
                addData(HomeChart, data.labels[key], data.data[key]);
            })
            switch (interval) {

                case "month":
                    var today = new Date();
                    HomeChart.options.scales.x.time.unit = 'day';
                    break;
                case "year":
                    HomeChart.options.scales.x.time.unit = 'month';
                    break;
            }
            HomeChart.update();

        } else {

        }
    })
}





function addData(chart, label, data) {
    chart.data.datasets.forEach((dataset) => {
        dataset.data.push({ x: label, y: data });
        dataset.backgroundColor.push("#0d0");

    });
    chart.update();
}

function removeData(chart) {
    chart.data.labels = [];
    chart.data.datasets.forEach((dataset) => {
        dataset.data = [];
    });
    chart.update();
}


function translate_month(month) {

    var result = month;

    switch (month) {

        case 'Feb':
            result = 'Fev';
            break;
        case 'Apr':
            result = 'Abr';
            break;
        case 'May':
            result = 'Mai';
            break;
        case 'Aug':
            result = 'Ago';
            break;
        case 'Sep':
            result = 'Set';
            break;
        case 'Dec':
            result = 'Dez';
            break;

    }

    return result;
}


function translate_this_label(label) {

    month = label.match(/Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Nov|Dec/g);

    if (!month)
        return label;

    translation = translate_month(month[0]);
    return label.replace(month, translation, 'g');
}
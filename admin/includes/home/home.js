


var HomeChart = new Chart(
    document.getElementById('HomeChart'),
    {
        type: 'line',
        data: {
            labels: null,
            datasets: [{
                label: 'Faturamento',
                data: null,
                fill: {
                    target: 'origin',
                    above: 'rgba(7, 158, 57, 0.15)'
                },
                borderColor: 'rgb(7, 158, 57)',
                tension: 0.15
            }]
        },
    }
);
function getHomeInfo(){
    $.get("/admin/api/get/getHomeInfo.php", function (data) {
        if(data.status >= 200 && data.status < 300){
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
    $.get("/admin/api/get/getHomeChart.php", { interval }, function (data) {
        if (data.status >= 200 && data.status < 300) {
            removeData(HomeChart);
            Object.keys(data.data).forEach(function (key) {
                addData(HomeChart, data.labels[key], data.data[key]);

            })
        } else {

        }
    })
}





function addData(chart, label, data) {
    chart.data.labels.push(label);
    chart.data.datasets.forEach((dataset) => {
        dataset.data.push(data);
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

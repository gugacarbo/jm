

var restrictChart = new Chart(
    document.getElementById('restrictChart'), {
    data: {
        datasets: [{
            type: 'bar',
            label: ['Lucro'],
            data: [],
            backgroundColor: ["#0f0"],
            color: ["#fff", "#fff", "#fff", "#fff", "#fff", "#fff",],
            fill: {
                target: 'origin',
                above: 'rgba(7, 158, 57, 0.15)'
            },
            borderColor: 'rgb(7, 158, 57)',
            tension: 0.10

        },
        {
            type: 'bar',
            label: ['Cancelamentos'],
            data: [],
            backgroundColor: ["#f00"],
            color: ["#fff", "#fff", "#fff", "#fff", "#fff", "#fff",],
            fill: {
                target: 'origin',
                above: 'rgba(158, 7, 57, 0.15)'
            },
            borderColor: 'rgb(158, 7, 57)',
            tension: 0.10

        }],
    },
    options: {
        plugins: {
            legend: {
                display: true,
                labels: {
                    color: '#ffffff88',
                    weight: "bold",
                    family: "Kdam Thmor Pro",
                    size: '13pt',

                }
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
                min: 0,
                title: {
                    color: '#fff',
                    display: true,
                    text: 'Valor'

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


$(document).ready(function () {
    $.get("api/get/list.php", { 'action': 'json' }, (data) => {
        setHomeLists(data);

        $.each(data.mes, (i, value) => {
            var payDate = new Date(Date.parse(value['paymentDate']))
            var paymentDate = payDate.getDate() + '/' + (payDate.getMonth() + 1) + '/' + payDate.getFullYear()

            var listItem = (`
                <div class="listItem">
                    <span class="date">${paymentDate}</span>
                    
                    <span class="value">${value['id']}</span>
                    <span class="value">${value['rawPayload']['itemCount']}</span>
                    <span class="value">R$${value['rawPayload']['grossAmount']}</span>
                    <span class="value">R$${(parseFloat(value['rawPayload']['creditorFees']['intermediationFeeAmount']) + parseFloat(value['rawPayload']['creditorFees']['intermediationRateAmount']) + parseFloat(value['totalCost'])).toFixed(2)}</span>
                    <span class="value">R$${value['rawPayload']['extraAmount']}</span>
                    <span class="value">R$${value['rawPayload']['shipping']['cost']}</span>
                    <span class="netAmount">R$${(parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])).toFixed(2)}</span>
                    <span class="finalNet">R$${((parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])) * 0.15).toFixed(2)}</span>
                </div>
            `)

            $("#relatoryList").prepend(listItem)
        })
        $.each(data.canceladas, (i, value) => {
            var payDate = new Date(Date.parse(value['paymentDate']))
            var paymentDate = payDate.getDate() + '/' + (payDate.getMonth() + 1) + '/' + payDate.getFullYear()

            var listItem = (`
                <div class="listItem">
                    <span class="date">${paymentDate}</span>
                    <span class="value">${value['id']}</span>
                    <span class="value">${value['rawPayload']['itemCount']}</span>
                    <span class="value">R$${value['rawPayload']['grossAmount']}</span>
                    <span class="value">R$${(parseFloat(value['rawPayload']['creditorFees']['intermediationFeeAmount']) + parseFloat(value['totalCost'])).toFixed(2)}</span>
                    <span class="value">R$${value['rawPayload']['extraAmount']}</span>
                    <span class="value">R$${value['rawPayload']['shipping']['cost']}</span>
                    <span class="netAmount">R$${(parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])).toFixed(2)}</span>
                    <span class="finalNet --redVal">R$${((parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])) * 0.15).toFixed(2)}</span>
                </div>
            `)

            $("#relatoryList").prepend(listItem)
        })

    })

    $.get("api/get/home.php", (data) => {

        setNumerUp($("#InfoRelatoryCancel"), data.canceladasMesDS);
        setNumerUp($("#InfoRelatoryNetDS"), data.lucroMesDS);
        setNumerUp($("#InfoRelatoryNet"), data.lucroMes);
        setNumerUp($("#InfoRelatoryCosts"), data.custos);
        setNumerUp($("#InfoRelatoryInvoicing"), data.faturamentoMes);

        setNumerUp($("#InfoTotalAmount"), data.totalDS);
        setTimeout(() => {
            setNumerUp($("#InfoMonth"), data.lucroMesDS);
        }, 400);
        setTimeout(() => {
            setNumerUp($("#InfoFuture"), data.futuroDS);
        }, 600);
        setTimeout(() => {
            setNumerUp($("#InfoCanceled"), data.canceladas);
            setNumerUp($("#InfoCanceledMonth"), data.canceladasMesDS);
        }, 800);
    })

    $("input[name='selectHome']").on("change", function () {
        var listName = $(this).val()

        $(".listBox .list").css("opacity", "0")
        $(".listBox .list").css("display", "none")
        $(".listBox ." + listName).css("opacity", "0")
        $(".listBox ." + listName).css("display", "flex")
        setTimeout(() => {
            $(".listBox ." + listName).css("opacity", "1")
        }, 100);

    });

    $("#GenerateRelatory").on('click', () => {
        if ($("#GenerateRelatory").hasClass("confirmGenerateButton")) {
            confirmGenerate()
        } else {
            $("#GenerateRelatory").addClass("confirmGenerateButton");
            setTimeout(() => {
                $("#GenerateRelatory").removeClass("confirmGenerateButton")
            }, 4000);
        }
    })

});

function confirmGenerate() {
    $("#GenerateRelatory").removeClass("confirmGenerateButton")
    $("#GenerateRelatory").addClass("getting")

    $.post("api/post/generateReatory.php", (data) => {
        if (data.status >= 200 && data.status < 300) {
            $("#GenerateRelatory").removeClass("getting")
            $("#GenerateRelatory").addClass("generated")
            setTimeout(() => {
                $("#GenerateRelatory").removeClass("generated")
            }, 4000);

        } else {
            $("#GenerateRelatory").removeClass("getting")
            $("#GenerateRelatory").addClass("generatedError")
            setTimeout(() => {
                $("#GenerateRelatory").removeClass("generatedError")
            }, 4000);
        }
    }).catch((value) => {
        $("#GenerateRelatory").removeClass("getting")
        $("#GenerateRelatory").addClass("generatedError")
        setTimeout(() => {
            $("#GenerateRelatory").removeClass("generatedError")
        }, 4000);
    })
}


function logout() {
    window.location.href = "../login/logout.php";
}


var lastOverPage = 0;
function changeOverPage(page = 0) {
    if (page == 0 || lastOverPage == page) {
        $(".overPage").each(function () {
            lastOverPage = 0;
            $(this).removeClass("overPageOpen")
        })
    } else {
        lastOverPage = page;

        if ($(".overPageOpen").length > 0) {

            $(".overPage").removeClass("overPageOpen")
            $(".overPage").on('transitionend', () => {
                $(".overPage:nth-child(" + [page] + ")").addClass("overPageOpen")
                $(".overPage").unbind();
            });

        } else {
            $(".overPage:nth-child(" + [page] + ")").addClass("overPageOpen")
        }



    }
}



function setNumerUp($this, val) {
    $this.text('')
    if (val - parseInt(val) > 0) {
        var value = val.toFixed(2).split('.')
        numUp($this, value[0]);
        numUp($this.next('small').next('b'), value[1]);
    } else {
        numUp($this, val);
        $this.next('small').next('b').text('00');

    }
}

function numUp($this, value) {

    jQuery({ Counter: 0 }).animate({ Counter: value }, {
        duration: 3200,
        easing: 'swing',
        step: function () {
            $this.text(Math.ceil(this.Counter));
        }
    });
}

function addData(chart, dataset = 0, label, data) {
    chart.data.datasets[dataset].data.push({ x: label, y: data });
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


function setHomeLists(data) {
    $.each(data.mes, (i, value) => {

        var esDate = new Date(Date.parse(value['rawPayload']['escrowEndDate']))
        var receiveDate = esDate.getDate() + '/' + (esDate.getMonth() + 1) + '/' + esDate.getFullYear()
        var payDate = new Date(Date.parse(value['paymentDate']))
        var paymentDate = payDate.getDate() + '/' + (payDate.getMonth() + 1) + '/' + payDate.getFullYear()

        var listItem = (`
        <div class="listItem">
        <span class="date">${paymentDate}</span>
        <span class="date">${receiveDate}</span>
        <span class="value">R$${value['rawPayload']['grossAmount']}</span>
        <span class="value">R$${(parseFloat(value['rawPayload']['creditorFees']['intermediationFeeAmount']) + parseFloat(value['rawPayload']['creditorFees']['intermediationRateAmount']) + parseFloat(value['totalCost'])).toFixed(2)}</span>
        <span class="netAmount">R$${(parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])).toFixed(2)}</span>
        <span class="finalNet">R$${((parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])) * 0.15).toFixed(2)}</span>
    </div>
        `)
        $("#MonthList").prepend(listItem)
    })
    $.each(data.futuro, (i, value) => {

        var esDate = new Date(Date.parse(value['rawPayload']['escrowEndDate']))
        var receiveDate = esDate.getDate() + '/' + (esDate.getMonth() + 1) + '/' + esDate.getFullYear()
        var payDate = new Date(Date.parse(value['paymentDate']))
        var paymentDate = payDate.getDate() + '/' + (payDate.getMonth() + 1) + '/' + payDate.getFullYear()

        var listItem = (`
        <div class="listItem">
        <span class="date">${paymentDate}</span>
        <span class="date">${receiveDate}</span>
        <span class="value">R$${value['rawPayload']['grossAmount']}</span>
        <span class="value">R$${(parseFloat(value['rawPayload']['creditorFees']['intermediationFeeAmount']) + parseFloat(value['totalCost'])).toFixed(2)}</span>
        <span class="netAmount">R$${(parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])).toFixed(2)}</span>
        <span class="finalNet ">R$${((parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])) * 0.15).toFixed(2)}</span>
    </div>
        `)

        $("#FutureList").append(listItem)

    })
    $.each(data.canceladas, (i, value) => {

        var esDate = new Date(Date.parse(value['rawPayload']['escrowEndDate']))
        var receiveDate = esDate.getDate() + '/' + (esDate.getMonth() + 1) + '/' + esDate.getFullYear()
        var payDate = new Date(Date.parse(value['paymentDate']))
        var paymentDate = payDate.getDate() + '/' + (payDate.getMonth() + 1) + '/' + payDate.getFullYear()

        var listItem = (`
            <div class="listItem">
                <span class="date">${paymentDate}</span>
                <span class="date">${receiveDate}</span>
                <span class="value">R$${value['rawPayload']['grossAmount']}</span>
                <span class="value">R$${(parseFloat(value['rawPayload']['creditorFees']['intermediationFeeAmount']) + parseFloat(value['totalCost'])).toFixed(2)}</span>
                <span class="netAmount">R$${(parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])).toFixed(2)}</span>
                <span class="finalNet --redVal">R$${((parseFloat(value['rawPayload']['netAmount']) - parseFloat(value['totalCost'])) * 0.15).toFixed(2)}</span>
            </div>
        `)

        $("#CancelList").append(listItem)

    })
    $.each(data.totals, (i, value) => {

        addData(restrictChart, 0, value['date'], value['invoicing'])
        addData(restrictChart, 1, value['date'], value['canceled'])

        var listItem = (`
            <div class="listItem">
                <span class="date">${value.date}</span>
                <span class="date">${value.invoicing}</span>
                <span class="finalNet --redVal">R$${value.canceled}</span>
                <span class="date">${value.netAmount}</span>
                <span class="date">${value.DS_netAmount}</span>
                <span class="download"><a href='${value.fileName}' target='blank'><i class="fa-solid fa-ellipsis"></i></a></span>
            </div>
        `)

        $("#TotalList").append(listItem)

    })
}
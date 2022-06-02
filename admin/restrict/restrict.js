const ctx = document.getElementById('myChart').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgb(255, 99, 132 )',
                'rgb(54, 162, 235 )',
                'rgb(255, 206, 86 )',
                'rgb(75, 192, 192 )',
                'rgb(153, 102, 255 )',
                'rgb(255, 159, 64 )'
            ],

            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],

            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        legend: {
            labels: {
                fontColor: "blue",
                fontSize: 55
            }
        }
    }
});

$(document).ready(function () {
    $.get("relatorio", { 'action': 'json' }, (data) => {
        //data data[][3]
        //Total data[][6]
        //Net data[][11]
        $.each(data, (i, value) => {
            const d = new Date();
            const dd = new Date(d.getFullYear(), d.getMonth(), 5);
            
            if (Date.parse(value[12]) > dd.getTime()) {
                var esDate = new Date(Date.parse(value[12]))
                var receiveDate = esDate.getDate() + '/' + (esDate.getMonth() + 1) + '/' + esDate.getFullYear()
                var payDate = new Date(Date.parse(value[3]))
                var paymentDate = payDate.getDate() + '/' + (payDate.getMonth() + 1) + '/' + payDate.getFullYear()
                $("#totalList").append(`
                    <div class="listItem">
                        <span class="date">${paymentDate}</span>
                        <span class="date">${receiveDate}</span>
                        <span class="value">R$${value[6].toFixed(2)}</span>
                        <span class="value">R$${(value[10]+value[7]).toFixed(2)}</span>
                        <span class="netAmount">R$${value[11].toFixed(2)}</span>
                        <span class="finalNet">R$${(value[11]*0.15).toFixed(2)}</span>
                    </div>

                `)
            }
        })
    })

    $.get("api/get/home.php", (data) => {
        setNumerUp($("#InfoTotalAmount"), data.lucroMes);
        setNumerUp($("#InfoMonth"), data.lucroMes);
        setNumerUp($("#InfoFuture"), data.futuro);
        setNumerUp($("#InfoCanceled"), data.canceladas);
    })

});



function setNumerUp($this, val) {
    $this.text('')
    var value = val.toFixed(2).split('.')
    numUp($this, value[0]);
    numUp($this.next('small').next('b'), value[1]);
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
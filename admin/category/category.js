var categories = [];

var myChart;
$(document).ready(function () {
    getCat();

    var cdata = {
        labels: [],
        datasets: [{
            label: 'Categorias',
            data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            color: [
                "#fff",
                "#fff",
                "#fff",
                "#fff",
                "#fff",
                "#fff"
            ],
            borderColor: [
                "#ffffff55"
            ],
            borderWidth: 1
        }]
    };

    var config = {
        type: 'doughnut',
        data: cdata,

        options: {}
    };
    myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
    console.log(myChart);

})










function deleteCategory(n) {
    $.get("deleteCat.php", { cat: n }, function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
            alert("Categoria Deletada");
            getCat()
        }
    })

}

function edit(c) {
    var newCat = $("input[name='" + c + "']").val();
    var oldCat = c;
    $.get("editCat.php", { newCat, oldCat: c }, function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
            getCat()
            alert("Categoria Editada");

        }
    })
}

function addCat() {
    var newCat = $("#newCat").val();
    if (newCat != "") {
        if (categories.includes(newCat)) {
            alert("Categoria Já Existe");
        } else {
            $.get("addCat.php", { newCat }, function (data) {
                data = JSON.parse(data);
                if (data["status"] == "success") {
                    getCat()
                    alert("Categoria Adicionada");

                }
            })
        }
    }
}

function getCat() {
    $.get("getCategory.php", function (data) {
        var category = JSON.parse(data);
        //category foreach
        removeData(myChart);
        $("#CatList").empty();
        $("#CatList").append(`  <div class="item header">
                                    <span>Categoria</span>
                                    <span>Opções</span>
                                </div>
                                <div class="item add">
                                    <input type="text" value="" id="newCat">
                                    <i class="fa-solid fa-plus" id="addCat" onclick="addCat()"></i>
                                </div>`)

        $.each(category, function (index, value) {
            var item = '<div class="item">'
                + '<input type="text" value="' + value['name'] + '"  name="' + value['name'] + '" >'
                + '<i class="fa-solid fa-pen"  onclick="edit(' + "'" + value['name'] + "'" + ')"></i>'
                +
                (value.numProds > 0 ? '<i class="fa-solid fa-trash cantDelete"></i>' :
                    '<i class="fa-solid fa-trash" onclick="deleteCategory(' + "'" + value['name'] + "'" + ')"></i>'
                )
                + '</div>';
            categories.push(value['name']);
            $("#CatList").append(item);


            addData(myChart, value["name"], value["numProds"]);



        })

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
    console.log(chart);

    chart.data.labels = [];


    chart.data.datasets.forEach((dataset) => {
        dataset.data = [];
    });
    chart.update();
    console.log(chart);
}
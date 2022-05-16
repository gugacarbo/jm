var categories = [];

var myChart;
$(document).ready(function () {
    $("body").append($("<div class='adminHeader'>").load("../header.html"));
    $("body").append($("<div class='adminMenu'>").load("../menu.html"));
    

    $("input").on("keydown", function (e) {
        if (e.keyCode == 13) {
        }
        console.log("aa")
    })
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


})










function deleteCategory(n) {
    $.get("deleteCat.php", { cat: n }, function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
            alert("Categoria Deletada");
            categories.splice(categories.indexOf(n), 1);
            getCat()
        }
    })

}

function edit(c) {
    var newCat = UPFisrt($("input[name='" + c + "']").val());
    var oldCat = c;
    if (newCat != "") {

        $.get("editCat.php", { newCat, oldCat: c }, function (data) {
            data = JSON.parse(data);
            if (data["status"] == "success") {
                getCat()
            }
        })
    }
}

function addCat() {
    var newCat = UPFisrt($("#newCat").val());
    if (newCat != "") {
        if (categories.includes(newCat)) {
            alert("Categoria Já Existe");
        } else {
            $.get("addCat.php", { newCat }, function (data) {
                data = JSON.parse(data);
                if (data["status"] == "success") {
                    getCat()

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
                                    <input type="text" value="" id="newCat" placeholder="Nova Categoria">
                                    <i class="fa-solid fa-plus" id="addCat" onclick="addCat()"></i>
                                </div>`)

        $.each(category, function (index, value) {
            var item = `<div class="item">
                <input type="text" onkeydown="editing(this)" value="${value.name}"  name="${value.name}" >
                <i class="fa-solid fa-save" onclick="edit('${value.name}')"></i>
                `+ (value.numProds > 0 ? '<i class="fa-solid fa-trash cantDelete"></i>' :
                    '<i class="fa-solid fa-trash" onclick="deleteCategory(' + "'" + value['name'] + "'" + ')"></i>') +
                `</div>`

            categories.push(value['name']);
            $("#CatList").append(item);


            addData(myChart, value["name"], value["numProds"]);



        })

    })
}

function editing(this_) {
    $(this_).next("i").addClass("canSave");
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



function UPFisrt(string) {
    string = string.toLowerCase()
    const arr = string.split(" ");
    for (var i = 0; i < arr.length; i++) {
        arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
    }
    return str2 = arr.join(" ");
}
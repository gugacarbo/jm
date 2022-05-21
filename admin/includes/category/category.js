var categories = [];
var CategoryChart;
$(document).ready(function () {
    $("input").on("keydown", function (e) {
        if (e.keyCode == 13) {

        }
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
    CategoryChart = new Chart(
        document.getElementById('CategoryChart'),
        config
    );
})

var timerCat;
function deleteCategory(n) {
    $(".deleteConfirm").remove();
    console.log("aaa")
    $("input[name='" + n + "']  + span + span").append(`
        <div class="deleteConfirm">
        <div onclick='delConfirmed("${n}")'>Deletar?</div>
        </div>
    `)
    clearTimeout(timerCat);
    timerCat = setTimeout(() => {
        $(".deleteConfirm").fadeOut(500, function () {
            $(".deleteConfirm").remove();
        })
    }, 3000);
}


function delConfirmed(n){
    $.post("/admin/api/delete/deleteCategory.php", { cat: n }, function (data) {
        if (data["status"] >= 200 && data["status"] < 300) {
            categories.splice(categories.indexOf(n), 1);
            getCat()
        }
    })
}
function edit(c) {
    var newCat = UPFisrt($("input[name='" + c + "']").val());
    var oldCat = c;
    if (newCat != "") {
        $.post("/admin/api/post/editCategory.php", { newCat, oldCat: c }, function (data) {
            if (data["status"] >= 200 && data["status"] < 300) {
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
            $.post("/admin/api/post/createCategory.php", { newCat }, function (data) {
                if (data["status"] >= 200 && data["status"] < 300) {
                    getCat()
                }
            })
        }
    }
}

function getCat() {
    $.get("/admin/api/get/getCategory.php", function (category) {
        //category foreach
        removeData(CategoryChart);
        $("#CatList").empty();
        $("#CatList").append(`  <div class="item header">
                                    <span>Catsegoria</span>
                                    <span>Opções</span>
                                </div>
                                <div class="item add">
                                    <input type="text" value="" id="newCat" placeholder="Nova Categoria">
                                    <i class="fa-solid fa-plus" id="addCat" onclick="addCat()"></i>
                                </div>`)
        $.each(category, function (index, value) {
            var item = `<div class="item">
                <input type="text" onkeydown="editing(this)" value="${value.name}"  name="${value.name}" >
                <span><i class="fa-solid fa-save" onclick="edit('${value.name}')"></i></span><span>
                `+ (value.numProds > 0 ? '<i class="fa-solid fa-trash cantDelete"></i>' :
                    '<i class="fa-solid fa-trash" onclick="deleteCategory(' + "'" + value['name'] + "'" + ')"></i>') +
                `</span></div>`
            categories.push(value['name']);
            $("#CatList").append(item);
            addData(CategoryChart, value["name"], value["numProds"]);
        })
    })
}

function editing(this_) {
    $(this_).next("span").find("i").addClass("canSave");
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
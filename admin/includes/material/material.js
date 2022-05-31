var materials = [];

var MaterialChart;
$(document).ready(function () {
    $("input").on("keydown", function (e) {
        if (e.keyCode == 13) {
        }
        console.log("aa")
    })
    getMat();
    var cdata = {
        labels: [],
        datasets: [{
            label: 'Mategorias',
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
    MaterialChart = new Chart(
        document.getElementById('MaterialChart'),
        config
    );
})

var timerMat;
function deleteMaterial(n, i) {
    $(".deleteConfirm").remove();
    $("input[name='" + n + "']  + span + span").append(`
        <div class="deleteConfirm">
        <div onclick='delMatConfirmed("${i}")'>Deletar?</div>
        </div>
    `)
    clearTimeout(timerMat);
    timerMat = setTimeout(() => {
        $(".deleteConfirm").fadeOut(500, function () {
            $(".deleteConfirm").remove();
        })
    }, 3000);
}


function delMatConfirmed(n) {
    $.post("/admin/api/post/material.php", { delMat: n }, function (data) {
        if (data["status"] >= 200 && data["status"] < 300) {
            getMat()
        } else {
            $(".deleteConfirm div").html("Erro!")
            $(".deleteConfirm div").css("background-color", "#ff0")
            $(".deleteConfirm div").css("color", "#000")

        }
    })
}


function editMat(c) {
    var newMat = UPFisrt($("input[name='" + c + "']").val());
    var oldMat = c;
    if (newMat != "") {

        $.post("/admin/api/post/material.php", { newMat, oldMat: c }, function (data) {
            if (data["status"] >= 200 && data["status"] < 300) {
                getMat()
            }else{
                var btc = $("input[name='" + c + "'] + span i").css("color");
                console.log(btc)
                $("input[name='" + c + "'] + span i").css("color", "#f00");
                setTimeout(() => {
                    $("input[name='" + c + "'] + span i").css("color", btc);
                }, 500);
            }
        })
    }
}
function addMat() {
    var newMat = UPFisrt($("#newMat").val());
    if (newMat != "") {
        if (materials.includes(newMat)) {
            alert("Mategoria Já Existe");
        } else {
            $.post("/admin/api/post/material.php", { newMat }, function (data) {
                if (data["status"] >= 200 && data["status"] < 300) {
                    getMat()

                }
            })
        }
    }
}
function getMat() {
    $.get("/admin/api/get/getMaterial.php", function (material) {
        //material foreach
        removeData(MaterialChart);
        $("#MatList").empty();
        $("#MatList").append(`  <div class="item header">
                                    <span>Materiais</span>
                                    <span>Opções</span>
                                </div>
                                <div class="item add">
                                    <input type="text" value="" id="newMat" placeholder="Novo Material">
                                    <i class="fa-solid fa-plus" id="addMat" onclick="addMat()"></i>
                                </div>`)
        $.each(material, function (index, value) {
            var item = `<div class="item">
                <input type="text" onkeydown="editing(this)" value="${value.name}"  name="${value.name}" >
                <span><i class="fa-solid fa-save" onclick="editMat('${value.name}')"></i></span><span>
                `+ (value.numProds > 0 ? '<i class="fa-solid fa-trash cantDelete"></i>' :
                    '<i class="fa-solid fa-trash" onclick="deleteMaterial(' + "'" + value['name'] + "'" + "," + "'" + value['id'] + "'" + ')"></i>') +
                `</span></div>`
                materials.push(value['name']);
            $("#MatList").append(item);
            addData(MaterialChart, value["name"], value["numProds"]);
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
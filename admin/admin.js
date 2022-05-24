
time()
setInterval(time, 1000);
$(document).ready(function () {

    $("#MenuToggle").on("click", function () {
        $("#MenuContent").toggleClass("active");
    });
    $(document).click(function (e) {
        if ($(".adminMenu").find(e.target).length > 0 && $("#MenuContent").hasClass("active") && $(e.target).find(".bars").length > 0) {
        } else {
            $("#MenuContent").removeClass("active");
        }

    });



    changePage("home");
})



function time() {
    var date = new Date();
    var time = date.toLocaleTimeString();
    var d = date.getDate();
    var m = date.toLocaleString('pt-br', {
        month: 'long'
    });
    var y = date.getFullYear();
    var h = date.getHours();
    var min = date.getMinutes();
    var sec = date.getSeconds();
    var day = date.getDay();
    var day_name = date.toLocaleString('pt-br', {
        weekday: 'long'
    });

    $("#dateH").text(d + " de " + m + " de " + y + " " + day_name);
    $("#dateT").text(h + ":" + min + ":" + sec);

}

function getHomeInfo(){
    $.get("/admin/api/get/getHomeInfo.php", function (data) {
        if(data.status >= 200 && data.status < 300){
            $("#HomeInfoAprovadas").text(data.Aprovadas)
            $("#HomeInfoCanceladas").text(data.Canceladas)
            $("#HomeInfoAguardandoEnvio").text(data.AguardandoEnvio)
            $("#HomeInfoNaoFinalizados").text(data.NaoPagos)
            $("#HomeInfoAguardandoPagamento").text(data.AguardandoPagamento)
        }
    })
}

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null) {
       return null;
    }
    return decodeURI(results[1]) || 0;
}


function changePage(adminPage) {
    var labelIndex = (switchLabels.indexOf(adminPage))
    $("#TitleHeader").html(switchLabelsMirror[labelIndex]);
    $("#AllContainer").html("");
    $.get("includes/" + adminPage + "/index.php", function (data) {
        $("#AllContainer").html(data);
    })
}

var switchLabels = ["home", "products",
    "banner",
    "category",
    "material",
    "carousel",
    "about",
    "purchases",
    "configFree",
    "reviewPurchases",
    "config",]
var switchLabelsMirror = ["Home", "Produtos",
    "Banners",
    "Categorias",
    "Materiais",
    "Carrosséis de Produtos",
    "Página Sobre",
    "Vendas",
    "Configurações de Descontos",
    "Compras Não Finalizadas",
    "Configurações",]



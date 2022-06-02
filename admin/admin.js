

var LastPages = [];

time()
setInterval(time, 1000);

$(document).ready(function () {


    $("#MenuToggle").on("click", function () {
        $("#MenuContent").toggleClass("active");
    });

    $(document).click(function (e) {
        if ($(".adminMenu").find(e.target).length > 0 && $("#MenuContent").hasClass("active")) {
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



$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
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

    if (LastPages.length > 0) {
        if (LastPages[LastPages.length - 1] == adminPage) {

            return;
        }
    }
    LastPages.push(adminPage);

    if (LastPages.length == 1) {
        $(".arrowback").addClass('nobackpage')
    } else {
        $(".arrowback").removeClass('nobackpage')

    }

}

function pageBack() {
    if (LastPages.length > 1) {
        LastPages.pop()
        var lastPage = LastPages.pop();
        changePage(lastPage);
    } else {
        changePage('home')
    }
}

var switchLabels = ["home", "products",
    "banner",
    "category",
    "material",
    "carousel",
    "about",
    "purchases",
    "configFree",
    "relatory",
    "config",]
var switchLabelsMirror = ["Home", "Produtos",
    "Banners",
    "Categorias",
    "Materiais",
    "Carrosséis de Produtos",
    "Página Sobre",
    "Vendas",
    "Configurações de Descontos",
    "Relatórios",
    "Configurações",]



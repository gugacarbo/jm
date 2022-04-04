var Box = [];

$(document).ready(() => {
    $.get("php/getAll.php", (data) => {
        var Prod = {};
        Prod = (JSON.parse(data));
        Prod.forEach((v, k) => {
            Prod[k]['images'] = JSON.parse(Prod[k]['images']);
            //console.log(k)
            CreateProduct(Prod[k]);
        });
    })


    $(".toggleMenu").on('click', () => {
        $(".menu").toggleClass("menuOpen")
    
    })

    $('.menuCheck').change(function () {
        menu();
    })

    $(".toggleBox").on('click', () => {
        $("#BoxContainer").toggleClass("open")
        createBox(Box)
    })

    $(document).on("click", ".send", function () {
        sendBox();
    })

    $(document).on("click", ".add, .zoomAdd", function () {
        //console.log($(this).attr('name'))
        if (jQuery.inArray($(this).attr('name'), Box) == -1) {
            var n = $(this).attr('name')
            $("div[name|='" + n + "']").css('background-color', "#00ffbf70")
            add($(this).attr('name'));
        } else {
            var n = $(this).attr('name')
            $("div[name|='" + n + "']").css('background-color', "#43005e56")
            add($(this).attr('name'));
        }
    })

})




function add(id) {
    jQuery.inArray(id, Box) == -1 ? Box.push(id) : Box = Box.filter(function (elem) {
        return elem != id;
    });;
    $("#BoxNumber").text(Box.length);
    createBox(Box);
}


function CreateProduct(prod) {
    var prodScope =
        "<div class='product' >"
        + "<div class='image' onclick=ShowZoomProd(" + prod['id'] + ")>"
        + "<img src='" + prod['images'][1] + "'>"
        + "</div>"
        + "<div class='info'>"
        + "<span class='nome' >" + prod['nome'] + "</span>"
        + "<span class='tamanho'>" + prod['tamanho'] + "</span>"
        + "<span class='preco'> R$" + prod['preco'] + "</span>"
        + "</div>"
        + "<div class='add' name='" + prod['id'] + "' " + (jQuery.inArray(prod['id'], Box) != -1 ? "style='background-color: #00ffbf70;'" : '') + ">"
        + "<i class='fas fa-arrow-right'></i>"
        + "<i class='fas fa-box'></i>"
        + "<i class='fas fa-plus'></i>"
        + "</div>"
        + "</div>";

    $(".container").append(prodScope);
}



function closeZoom() {
    $("#ZoomDisplay").removeClass("zoomProdActive");
}


function ShowZoomProd(id) {
    $("#ZoomDisplay").text('');
    $.get("php/getById.php", { 'id': id }, (data) => {
        var prod = JSON.parse(data);
        prod[0]['images'] = JSON.parse(prod[0]['images']);
        var img = prod[0]['images'];

        var images = "";
        Object.entries(img).forEach(([k, v]) => {
            images +=
                "<img src='" + v + "' onclick='setZoomImage(this)'>";
        })

        var zoomProd =
            '<div class="zoomProduct">'
            + '<div class="zoomImage">'
            + '<img src="' + prod[0]['images'][1] + '">'
            + '</div>'
            + '<div class="zoomInfo">'
            + '<div class="close" onclick="closeZoom()"><i class="far fa-times-circle"></i></div>'
            + '<span class="zoomNome">' + prod[0]['nome'] + '</span>'
            + '<span class="zoomTamanho">' + prod[0]['tamanho'] + '</span>'
            + '<span class="zoomPreco">R$ ' + prod[0]['preco'] + '</span>'
            + '<div class="secImg">'
            + images
            + '</div>'
            + '<div class="zoomObs">'
            + '<span>Ref.: ' + prod[0]['id'] + '</span>'
            + '<span>Observações:</span>'
            + '<p>' + prod[0]['descricao'] + '</p>'
            + '</div>'
            + '<div class="zoomAdd" name="' + prod[0]['id'] + '" ' + (jQuery.inArray(prod[0]['id'], Box) != -1 ? "style='background-color: #00ffbf70;'" : '') + '>'
            + '<i clas="fas fa-arrsow-right"></i>'
            + '<i class="fas fa-box"></i>'
            + '<i class="fas fa-plus"></i>'
            + '</div>'
            + '</div>'
            + '</div>';
        $("#ZoomDisplay").append(zoomProd)
        $("#ZoomDisplay").addClass("zoomProdActive");
    })
}

function setZoomImage(obj) {
    $(".zoomImage img").attr('src', obj.src)
}



var message = "";
function createBox(Box) {
    message = "";
    message += ("Olá! Gostei Destas Roupas! \n");
    $(".show").text('')
    Box.forEach((value, index) => {
        $.get("php/getById.php", { 'id': value }, (data) => {
            var prod = JSON.parse(data);
            prod[0]['images'] = JSON.parse(prod[0]['images']);
            var img = prod[0]['images'];

            message += ("Cod:" + prod[0]['id'] + " - " + prod[0]['nome'] + "\n");


            var scope = '<div class="item">'
                + '<div class="image">'
                + '<img src=" ' + prod[0]['images'][1] + '">'
                + '</div>'
                + '<span>'
                + prod[0]['nome']
                + '<i class="fas fa-trash-alt add"  name="' + prod[0]['id'] + '"></i>'
                + '</span>'
                + '</div>'

            $(".show").append(scope)
        })
    })
}

function sendBox() {
    let url = "https://wa.me/+5549999183435?text=";
    console.log(url + encodeURI(message))
    window.open(
        url + encodeURI(message),
        '_blank'
    );
}



function menu() {
    var chkbx = [($("#shirt").is(':checked') | 0),
    ($("#jacket").is(':checked') | 0),
    ($("#pant").is(':checked') | 0),
    ($("#foot").is(':checked') | 0),
    ($("#acessorios").is(':checked') | 0)]
    $("#containerProduts").text('');


    if (!chkbx.includes(1)) {
        $.get("php/getAll.php", (data) => {
            Prod = (JSON.parse(data));
            Prod.forEach((v, k) => {
                Prod[k]['images'] = JSON.parse(Prod[k]['images']);
                CreateProduct(Prod[k]);
            });
        })
    }else{

        
        $.get("getByCat.php", { 'cat': chkbx }, (data) => {
            if (data == '{}') {
                $(".container").append("<span class='notFound'> <i class='fas fas fa-box-open'></i> Nenhum Produto Encontrado</span>");
            }else{
                Prod = (JSON.parse(data));
                Prod.forEach((v, k) => {
                    Prod[k]['images'] = JSON.parse(Prod[k]['images']);
                    CreateProduct(Prod[k]);
                });
            }       
        })
    }
}



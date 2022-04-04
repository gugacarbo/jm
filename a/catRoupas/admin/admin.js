$(document).ready(() => {
    $.get("../php/getAll.php", (data) => {
        var Prod = {};
        Prod = (JSON.parse(data));
        Prod.forEach((v, k) => {
            Prod[k]['images'] = JSON.parse(Prod[k]['images']);

            createProduct(Prod[k]);
        });
    })

    $('.menuCheck').change(function () {
        menu();
    })
    $("#closeZoom").click(()=>{
        $(".zoom").removeClass("open")
    })
})


function createProduct(Prod){
    var P = '<div class="product" onclick="Z('+Prod['id']+')">'
            +'<span class="nome">'+Prod['nome']+'</span>'
            +'<div class="mainImg">'
            +'<img src="'+Prod['images'][1]+'">'
            +'</div>'
            +'<span class="images"><i class="far fa-images"></i>'+(Object.keys(Prod['images']).length)+'</span>'
            +'<span class="cod">Cod.: '+Prod['id']+'</span></div>';
            $(".products").append(P);
}


function changeImg(x){
    $("#ZImg").attr('src', x)
}

function Z(id){
    
    $.get("../php/getById.php", { 'id': id }, (data) => {
        var prod = JSON.parse(data);
        prod[0]['images'] = JSON.parse(prod[0]['images']);
        var img = prod[0]['images'];
        
        $("#zName").val(prod[0]['nome'])
        $("#zSize").val(prod[0]['tamanho'])
        $("#zPrice").val(prod[0]['preco'])
        $("#zDescription").text(prod[0]['descricao'])
        $("#ZImg").attr('src', prod[0]['images'][1])
        
        $("#zSecImg").text('');
        Object.entries(img).forEach(([k, v]) => {
            $("#zSecImg").append("<div onclick='changeImg("+ '"' +prod[0]['images'][k]+ '"'+")'><img src='"+prod[0]['images'][k]+"'></div>")
        })
        $("#zSecImg").append('<div id="add"><i class="fas fa-plus"></i></div>')

        $(".zoom").addClass("open")
    })

}

function menu() {
    var chkbx = [($("#shirt").is(':checked') | 0),
    ($("#jacket").is(':checked') | 0),
    ($("#pant").is(':checked') | 0),
    ($("#foot").is(':checked') | 0),
    ($("#acessorios").is(':checked') | 0)]
    $(".products").text('');


    if (!chkbx.includes(1)) {
        $.get("../php/getAll.php", (data) => {
            Prod = (JSON.parse(data));
            Prod.forEach((v, k) => {
                Prod[k]['images'] = JSON.parse(Prod[k]['images']);
                createProduct(Prod[k]);
            });
        })
    }else{

        
        $.get("../getByCat.php", { 'cat': chkbx }, (data) => {
            if (data == '{}') {
                //$(".container").append("<span class='notFound'> <i class='fas fas fa-box-open'></i> Nenhum Produto Encontrado</span>");
            }else{
                Prod = (JSON.parse(data));
                Prod.forEach((v, k) => {
                    Prod[k]['images'] = JSON.parse(Prod[k]['images']);
                    createProduct(Prod[k]);
                });
            }       
        })
    }
}


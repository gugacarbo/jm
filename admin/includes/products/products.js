
var orderG = '';
var filterG = ''
var textSearch = ''
var maxpPage = 20;
var ProdsPage = 0;

$(document).ready(function () {
    ProdsPage = 0;
    search();

    $("#btnSearch").click(function () {
        ProdsPage = 0;
        search();

    })
    $('#textSearch').keyup(function (e) {
        if (e.keyCode == 13) {
            ProdsPage = 0;
            search();
        }
    });


    $(".filterI").on("click", function () {
        if ($(this).hasClass("selected")) {
            $(this).find("i").toggleClass("up");
        } else {
            $("i").each(function (i, d) {
                $(d).removeClass("up")
            })
            $(".filterI").each(function (i, d) {
                $(d).removeClass("selected")
            })
            $(this).addClass("selected");
        }

        ProdsPage = 0;
        search($(this).find("i").hasClass("up"), $(this).attr("name"));
    })

})


function search(order_ = orderG, filter_ = filterG,) {
    var textSearch = $("#textSearch").val() || '';
    orderG = order_;
    filterG = filter_;

    var c = {
        "filter": filterG,
        "order": orderG,
        "text": textSearch
    }
    $("#productsList").fadeOut(400, function () {
        $.get("/admin/api/get/getProducts.php", c, function (produtos) {
            $("#totalProducts b").html(produtos.length);
            if (ProdsPage == 0) {
                $("#PageCounter").empty();
                for (var i = 0; i < Math.ceil((produtos.length) / maxpPage); i++) {
                    $("#PageCounter").append(`<span onclick="changeProductsPage(${i})">${i + 1}</span>`)
                }
            }

            $(".pageSelected").removeClass("pageSelected");
            $("#PageCounter span:nth-child(" + (ProdsPage + 1) + ")").addClass("pageSelected");

            $("#productsList").empty();

            var prodNum = produtos.length;
            if (prodNum == 0) {
                $("#purshcasesList").append(`<span style='width:100%; text-align:center; padding: 10px 0; '>Nenhum resultado encontrado</span>`);

            }
            $.each(produtos.slice(ProdsPage * maxpPage, (ProdsPage + 1) * maxpPage), function (index, produto) {
                createProd(produto)
            })
        }).then(_ => {
            $("#productsList").fadeIn(500);
        })
    })

}


function changeProductsPage(i) {
    ProdsPage = i;
    search();

}


function createProd(produto) {
    var prod = `
    <div class="product" id="Prod${produto.id}">
        <span>${produto.id}</span>
        <span  onclick="modalProductShow(${produto.id})">${produto.name}</span>
        <span>${"R$ " + produto.price.toFixed(2).replace(".", ",")}</span>
        <span ${(produto.promo > 0 ? " style='color:#31AF33;' " : " style='color:#AF314E;' ")}  >${(produto.promo > 0 ? ("(" + ((1 - (produto.price / produto.promo)) * 100).toFixed(2) + "%)") : "Nao")}</span>
        
        <span>${produto.category}</span>
        ${produto.totalQuantity <= 0 ? '<span style="font-weight: bold; color: #f00;">' + produto.totalQuantity + '</span>' : '<span>' + produto.totalQuantity + '</span>'}
        <span>${produto.sold}</span>
        <span><i class="fas fa-pencil-alt" onclick="modalProductShow(${produto.id})"></i></span>
        <span><i class="fas fa-trash-alt" onclick="deleteProduct(${produto.id})"></i></span>
</div>`
    $("#productsList").append(prod);
}



var timerDel;
function deleteProduct(id) {
    $(".deleteConfirm").remove();
    $("#Prod" + id + " span:last-child").append(`
        <div class="deleteConfirm">
        <div onclick='del(${id})'>Deletar?</div>
        </div>
    `)
    clearTimeout(timerDel);
    timerDel = setTimeout(() => {
        $(".deleteConfirm").fadeOut(500, function () {
            $(".deleteConfirm").remove();
        })
    }, 3000);




}

function del(id) {
    $.post("/admin/api/post/product.php", { deleteProduct: id }, function (data) {
        if (data.status >= 200 && data.status < 300) {
            $("#Prod" + id).remove();
        } else {
        }
    })
}

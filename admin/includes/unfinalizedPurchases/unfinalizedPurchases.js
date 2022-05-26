$("#ReviewButton").click(function () {
    $.post("/admin/api/delete/deleteUnfinalizedPurchases.php", function (data) {
        if (data.status >= 200 && data.status < 300) {
            $("#reviewList").html("");
            $("#totalOrders b").html(0)
            $(this).prop("disabled", true);
        }
    })
});

$.get("/admin/api/get/getNoFinalizedPurshases.php?get", function (data) {
    $("#totalOrders b").html(data.length)
    if (data.length > 0) {
        $.each(data, function (index, value) {
            var buyer = (value['buyer']);
            var products = (value['products']);

            var now = Date.now();
            var date = new Date(value.buy_date);
            var int = parseInt((now - date.getTime()) / 1000 / 60 / 60 / 24) // Em dias;
            var dateString = date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear();

            var totalP = 0;
            $.each(products, function (index, value) {
                totalP = totalP + value.qtd;
            });
            $("#reviewList").append(`<div class="item">
                                <span ${int > 3 ? "style='color:#f00;'" : ""}>${dateString} Há ${int} Dias</span>
                                <span>${buyer.nome} ${buyer.sobrenome}</span>
                                <span> ${totalP}</span>
                                <span> ${value.totalValue}</span>
                            </div>`);
        })
    } else {
        $("#ReviewButton").prop("disabled", true);

    }
})
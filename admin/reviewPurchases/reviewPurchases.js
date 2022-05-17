$("#ReviewButton").click(function () {
    $.get("php/review.php", function (data) {
        data = JSON.parse(data);
        console.log(data);
        $("#reviewList").html("");
        $("#totalOrders b").html(0)
        $(this).prop("disabled", true);
    })
});

$.get("php/noFinalizedPurshases.php?get", function (data) {
    data = JSON.parse(data);
    $("#totalOrders b").html(data.length)
    if (data.length > 0) {
        $.each(data, function (index, value) {
            var buyer = JSON.parse(value['buyer']);
            var products = JSON.parse(value['products']);

            var now = Date.now();
            var date = new Date(value.buy_date);
            var int = parseInt((now - date.getTime()) / 1000 / 60 / 60 / 24) // Em dias;
            var dateString = date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear();


            $("#reviewList").append(`<div class="item">
                                <span ${int > 3 ? "style='color:#f00;'" : ""}>${dateString} Há ${int} Dias</span>
                                <span>${buyer.nome} ${buyer.sobrenome}</span>
                                <span> ${products.length}</span>
                                <span> ${value.totalValue}</span>
                            </div>`);
        })
    }else{
        $("#ReviewButton").prop("disabled", true);

    }
})
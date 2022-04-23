$(document).ready(function () {
    $.get("/php/getCategory.php", function (data) {
        var category = JSON.parse(data);
        //category foreach
        $.each(category, function (index, value) {
            console.log(value);
            var item = '<div class="item">'
                + '<input type="text" value="' + value['name'] + '"  >'
                + '<i class="fa-solid fa-pen" value="' + value['name'] + '" onclick="edit('+ "'" + value['name']  + "'" + ')"></i>'
                + '<i class="fa-solid fa-trash" onclick="deleteCategory('+ "'" + value['name']  + "'" + ')"></i>'

                + '</div>';

            $("#CatList").append(item);
        })
    })

    $("#addCat").click(function () {
        var newCat = $("#newCat").val();
        $.get("addCat.php", { newCat }, function (data) {
            data = JSON.parse(data);
            if (data["status"] == "success") {
                var item = '<div class="item">'
                    + '<input type="text" value="' + newCat + '"  >'
                    + '<i class="fa-solid fa-pen" value="' + newCat + '" onclick="edit('+ "'" + newCat  + "'" + ')"></i>'
                    + '<i class="fa-solid fa-trash" onclick="deleteCategory("' + newCat  + ')"></i>'
                    + '</div>';

                $("#CatList").append(item);
                $("#newCat").val("");
            }
        })
    })
})


function deleteCategory(n) {
    $.get("deleteCat.php", { cat: n }, function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
            $("#CatList").find("[value='" + n + "']").parent().remove();
        }
    })

}

function edit(c){
    var newCat = $("input[value='"+c+"']").val();
    var oldCat = c;
    $.get("editCat.php", { newCat , oldCat : c}, function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
            $("#CatList").find("input[value='" + n + "']").val(newCat);
        }
    })
}
$(document).ready(function () {
    $.get("../getMaterial.php", function (data) {
        var materials = JSON.parse(data);
        //materials foreach
        $.each(materials, function (index, value) {
            console.log(value);
            var item = '<div class="item">'
                + '<input type="text" value="' + value['name'] + '"  >'
                + '<i class="fa-solid fa-pen" value="' + value['name'] + '" onclick="edit('+ "'" + value['name']  + "'" + ')"></i>'
                + '<i class="fa-solid fa-trash" onclick="deleteMat('+ "'" + value['name']  + "'" + ')"></i>'

                + '</div>';

            $("#MatList").append(item);
        })
    })

    $("#addMat").click(function () {
        var newMat = $("#newMat").val();
        $.get("addMat.php", { newMat }, function (data) {
            data = JSON.parse(data);
            if (data["status"] == "success") {
                var item = '<div class="item">'
                    + '<input type="text" value="' + newMat + '"  >'
                    + '<i class="fa-solid fa-pen" value="' + newMat + '" onclick="edit('+ "'" + newMat  + "'" + ')"></i>'
                    + '<i class="fa-solid fa-trash" onclick="deleteMat("' + newMat  + ')"></i>'
                    + '</div>';

                $("#MatList").append(item);
                $("#newMat").val("");
            }
        })
    })
})


function deleteMat(n) {
    $.get("deleteMat.php", { mat: n }, function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
            $("#MatList").find("[value='" + n + "']").parent().remove();
        }
    })

}

function edit(c){
    var newMat = $("input[value='"+c+"']").val();
    var oldMat = c;
    $.get("editMat.php", { newMat , oldMat : c}, function (data) {
        data = JSON.parse(data);
        if (data["status"] == "success") {
            $("#MatList").find("input[value='" + newMat + "']").val(newMat);
        }
    })
}
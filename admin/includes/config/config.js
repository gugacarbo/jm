
$(document).ready(function () {
    $.get("/admin/api/get/getConfigs.php", function (data) {
        $("#adminMail").val(data.adminMail);
        $("#adminMail").attr("placeholder", data.adminMail);
        
        $("#contactMail").val(data.contactMail);
        $("#contactMail").attr("placeholder", data.contactMail);

        $("#automaticMail").val(data.automaticMail);
        $("#automaticMail").attr("placeholder", data.automaticMail);
        $("#automaticPass").val("");
        $("#automaticPass").attr("placeholder", "oculto por segurança");

        $.each(data.sendToAdminMail, function (index, value) {
            $(".toggle input[value='" + value + "']").prop("checked", true);
        })

        $("#cepOrigemFrete").val(data.cepOrigemFrete);
        $("#alturaFrete").val(data.alturaFrete);
        $("#larguraFrete").val(data.larguraFrete);
        $("#comprimentoFrete").val(data.comprimentoFrete);
        $("#aditionalWeight").val(data.aditionalWeight*1000);

    })

    $("#saveShipingConfig").click(function () {
        var shipConfig = {
            "cepOrigemFrete": $("#cepOrigemFrete").val(),
            "aditionalWeight": $("#aditionalWeight").val()/1000,
            "alturaFrete": $("#alturaFrete").val(),
            "larguraFrete": $("#larguraFrete").val(),
            "comprimentoFrete": $("#comprimentoFrete").val()
        }

        $.post("/admin/api/post/editShipingConfig.php", shipConfig, function (data) {
            
            $(".doneButton").removeClass("doneButton")
            $(".alertButton").removeClass("alertButton")

            if (data.status >= 200 && data.status < 300) {                
                $("#saveShipingConfig").addClass("doneButton")
                setTimeout(() => {
                    $(".doneButton").removeClass("doneButton")
                }, 1500);
            } else {
                alert(data.message);
                $("#saveShipingConfig").addClass("alertButton")
                setTimeout(() => {
                    $(".alertButton").removeClass("alertButton")
                }, 1500);
            }
        })
    })

    $("#saveEmailConfig").click(function () {
        var adminMail = $("#adminMail").val();
        var contactMail = $("#contactMail").val();
        var automaticMail = $("#automaticMail").val();
        var automaticPass = $("#automaticPass").val();
        var sendToAdminMail = [];
        $(".toggle input:checked").each(function () {
            sendToAdminMail.push($(this).val());
        })

        $.post("/admin/api/post/editEmailConfig.php", {
            adminMail: adminMail,
            contactMail: contactMail,
            automaticMail: automaticMail,
            automaticPass: automaticPass,
            sendToAdminMail: sendToAdminMail  || []
        }, function (data) {
            $(".doneButton").removeClass("doneButton")
            $(".alertButton").removeClass("alertButton")
            if (data.status >= 200 && data.status < 300) {
                $("#adminMail").attr("placeholder", adminMail);
                $("#contactMail").attr("placeholder", contactMail);
                $("#automaticMail").attr("placeholder", automaticMail);
                $("#automaticPass").attr("placeholder", "oculto por segurança");
                $("#saveEmailConfig").addClass("doneButton")
                setTimeout(() => {
                    $(".doneButton").removeClass("doneButton")
                }, 1500);
            } else {
                alert(data.message);
                $("#saveEmailConfig").addClass("alertButton")
                setTimeout(() => {
                    $(".alertButton").removeClass("alertButton")
                }, 1500);
            }
        })
    })


    $("#automaticPass").val("");

})
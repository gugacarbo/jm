var comp;

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
        $("#aditionalWeight").val(data.aditionalWeight * 1000);

    })

    $("#saveShipingConfig").click(function () {
        var shipConfig = {
            "cepOrigemFrete": $("#cepOrigemFrete").val(),
            "aditionalWeight": $("#aditionalWeight").val() / 1000,
            "alturaFrete": $("#alturaFrete").val(),
            "larguraFrete": $("#larguraFrete").val(),
            "comprimentoFrete": $("#comprimentoFrete").val()
        }

        $.post("/admin/api/post/config.php", shipConfig, function (data) {

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

        $.post("/admin/api/post/config.php", {
            adminMail: adminMail,
            contactMail: contactMail,
            automaticMail: automaticMail,
            automaticPass: automaticPass,
            sendToAdminMail: sendToAdminMail || []
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

    $("#newPassword").on('keydown', function () {
        var currentPass = $("#newPassword").val();

        $("#newPassword").complexify({}, function (valid, complexity) {
            comp = complexity;
            verifyPass();
            if (complexity > 40) {
                $("#passStrong").removeClass("fraca");
                $("#passStrong").removeClass("media");
                $("#passStrong").removeClass("forte");
                $("#passStrong").addClass("muitoForte");
                $("#passStrong + small").html("Senha muito forte");
            }
            else if (complexity > 30) {
                $("#passStrong").removeClass("fraca");
                $("#passStrong").removeClass("media");
                $("#passStrong").addClass("forte");
                $("#passStrong").removeClass("muitoForte");
                $("#passStrong + small").html("Senha forte");
            }
            else if (complexity > 20) {
                $("#passStrong").removeClass("fraca");
                $("#passStrong").addClass("media");
                $("#passStrong").removeClass("forte");
                $("#passStrong").removeClass("muitoForte");
                $("#passStrong + small").html("Senha média");
            }
            else if (complexity > 10) {
                $("#passStrong").addClass("fraca");
                $("#passStrong").removeClass("media");
                $("#passStrong").removeClass("forte");
                $("#passStrong").removeClass("muitoForte");
                $("#passStrong + small").html("Senha fraca");
            } else {
                $("#passStrong").removeClass("fraca");
                $("#passStrong").removeClass("media");
                $("#passStrong").removeClass("forte");
                $("#passStrong").removeClass("muitoForte");
                $("#passStrong + small").html("");
            }
        });
    })

    $("#confirmPassword").on("keydown keyup paste", function () {
        verifyPass()
    })

    $("#currentPassword").on("keydown keyup paste", function () {
        verifyPass()
    })


    $("#saveAdminPassword").on("click", function () {
        verifyPass();
        if (!$("#saveAdminPassword").prop("disabled")) {
            $.post("/admin/api/post/config.php", {
                user: $("#userName").val().replace(" ", ''),
                currentPassword: $("#currentPassword").val(),
                newPassword: $("#newPassword").val()
            }, function (data) {

                $(".doneButton").removeClass("doneButton")
                $(".alertButton").removeClass("alertButton")

                if (data.status >= 200 && data.status < 300) {
                    $("#currentPassword").val()
                    $("#newPassword").val()
                    $("#confirmPassword").val()

                    $("#saveAdminPassword").addClass("doneButton")
                    setTimeout(() => {
                        $(".doneButton").removeClass("doneButton")
                    }, 1500);
                } else {
                    $("#passStrong + small").html(data.message);

                    $("#saveAdminPassword").addClass("alertButton")
                    setTimeout(() => {
                        $(".alertButton").removeClass("alertButton")
                    }, 1500);
                }
            })
        }
    })
})

$("#saveAdminPassword").prop("disabled", true)

function verifyPass() {
    var newPass = $("#newPassword").val();
    var confirmPass = $("#confirmPassword").val();
    if (comp > 25 && $("#currentPassword").val() != '') {
        if (newPass != confirmPass) {
            $("#saveAdminPassword").prop("disabled", true)
            $("#passStrong + small").html("senhas não conferem");
            $("#newPassword").addClass("is-not-equal");
            $("#confirmPassword").addClass("is-not-equal");
            setTimeout(() => {
                $("#newPassword").removeClass("is-not-equal");
                $("#confirmPassword").removeClass("is-not-equal");
            }, 1500);
        } else {
            $("#saveAdminPassword").prop("disabled", false)
        }
    } else {
        $("#passStrong + small").html(" ");

        $("#saveAdminPassword").prop("disabled", true)
    }
}

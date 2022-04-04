
$(document).ready(() => {

    var user = '';

    $("#login").on('click', () => {
        if ($("#git_user").val() == '') {

            $("#git_user").attr("placeholder", "Digite um Nome de Usuario")
            $("#git_user").css("border-bottom", "1px solid #f00")
            $("#user_name").text("Digite um Nome de Usuario")
            $("#user_avatar").attr("src", "https://media.istockphoto.com/vectors/profile-picture-vector-illustration-vector-id587805156?k=20&m=587805156&s=612x612&w=0&h=Ok_jDFC5J1NgH20plEgbQZ46XheiAF8sVUKPvocne6Y=");
            $("#user_avatar").css("border-color", "#ddd");
            $("#acess").remove()

        } else {

            $("#git_user").css("border-bottom", "1px solid #337")
            
            
            user = $("#git_user").val();
            $.get("https://api.github.com/users/" + user, (data) => {

                $("#user_avatar").attr("src", data.avatar_url);
                $("#git_user").attr("href", data.html_url);
                $("#user_name").text(data.login)
            })
            .fail(function () {
                $("#user_avatar").attr("src", "https://media.istockphoto.com/vectors/profile-picture-vector-illustration-vector-id587805156?k=20&m=587805156&s=612x612&w=0&h=Ok_jDFC5J1NgH20plEgbQZ46XheiAF8sVUKPvocne6Y=");
                $("#user_name").text("Usuário Não Encontrado")
                $("#git_user").css("border-bottom", "1px solid #f00")
                $("#acess").remove()
                $("#user_avatar").css("border-color", "#ddd");

            })
            .done(function() {
                $("#acess").remove()
                $(".login").append("<a id='acess' href='index.html?user="+user+"'>Acessar <i class='fas fa-angle-right'></i></a>")
                $("#user_avatar").css("border-color", "#8418ff");

              })


        }
    })
})




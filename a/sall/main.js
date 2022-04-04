
//Variavel Global Usuario
var user;

$(document).ready(() => {

    /*
    *   Pega Usuário por GET
    */
    var url_string = window.location.href;
    var url = new URL(url_string);
    var user = url.searchParams.get("user");
    if (!user) {
        window.location.replace("login.html");
    }
    /*********************************** */


    getAllRepos(user);


    /**
     *  Controle da seta de troca de tela Mobile
     */
    var showArrow = 0;
    $("#change").on('click', function () {
        if ($(document).width() <= 650) {

            $("#change").toggleClass("fa-arrow-right");
            $("#change").toggleClass("fa-arrow-left");

            $(".user").css("display", showArrow == 0 ? "none" : "flex");
            $(".repos").css("display", showArrow == 1 ? "none" : "flex");
            showArrow = (showArrow == 1 ? 0 : 1);
        }
    })
    /*********************************** */




    /**
     *  Recursividade para esconder a seta quando nao mobile
     */
    $(window).resize(() => {
        if ($(document).width() > 650) {
            $(".user").css("display", "flex");
            $(".repos").css("display", "flex");
        }
    })
    /*********************************** */




    /**
     *  Controle para Pesquisa Automática
     */
    var typingTimer;
    var doneTypingInterval = 400;

    $('#searchText').keyup(function () {
        clearTimeout(typingTimer);
        if ($('#searchText').val) {
            typingTimer = setTimeout(done, doneTypingInterval);
        }
        if ($('#searchText').val == '') {
            getAllRepos(user)
        }
    });
    function done() {
        if ($("#searchText").val() != '') {
            search(user);
        } else {
            getAllRepos(user)
        }
    }
    /*********************************** */

    //Botao Pesquisar
    $("#goSearch").on('click', () => {
        if ($("#searchText").val() != '') {
            search(user);
        } else {
            getAllRepos(user)
        }
    })
    //***/


})


/**
 * 
 *  Pega Todos os Repositórios
 * 
 */
function getAllRepos(user) {


    $.get("https://api.github.com/users/" + user, (data) => {

        $("#repContent").text('');
        $("#searchResp").text('');

        $("#avatar").attr("src", data.avatar_url);
        $("#gitUrl").attr("href", data.html_url);
        $("#user_name").text(data.login)
        $("#followers").text(data.followers)
        $("#following").text(data.following)

        var bio = data.bio == null ? "Usuário Sem Bio" : data.bio;
        $("#bio").text(bio)

        $.get("https://api.github.com/users/" + user + "/repos", (data) => {
            data.forEach(x => {

                let item = "<div class='item'>"
                    + "<span class='repName'>" + x.name + "</span>"
                    + "<p class='repDesc'>" + x.description + "</p>"
                    + "<a target='_blank' href='" + x.html_url + "'> Go to <i class='fas fa-external-link-alt'></i> </a>"
                    + "</div>";


                $("#repContent").append(item)
            });
        })
    })
        .fail(function () {
            alert("Erro");
            window.location.replace("login.html");

        })

}



/**
 * 
 *  Pesquisa Por Repositorio
 * 
 */
function search(user) {


    var searchText = $("#searchText").val();
    var query = searchText + " user:" + user;
    var queryString = 'q=' + encodeURIComponent(query);



    $.get("https://api.github.com/search/repositories", queryString, (data) => {

        var StringData = Object.values(data);

        $("#repContent").text('');
        $("#searchResp").text('');

        StringData[2].forEach(s => {

            $("#searchResp").append("<a href='" + s.html_url + "'>" + s.name + "</a>")

            let item = "<div class='item'>"
                + "<span class='repName'>" + s.name + "</span>"
                + "<p class='repDesc'>" + s.description + "</p>"
                + "<a target='_blank' href='" + s.html_url + "'> Go to <i class='fas fa-external-link-alt'></i> </a>"
                + "</div>";

            $("#repContent").append(item)
        });
    })
        .fail(() => {
            getAllRepos(user)
        })

}
$(document).ready(() => {
  $("header").load("/includes/header.html");
  $("footer").load("/includes/footer.html");

  var email = $.urlParam("email");
  $("#UnsubscribeEmail").html(email);

  $("#Unsubscribe").on("click", () => {
    $.get("/php/unsubscribe.php?email=" + email, (data) => {
      data = JSON.parse(data);

      console.log(data);
      if (data['status'] == 'success') {
        $("#UnsubscribeEmail").html("Voce foi desinscrito com sucesso!");
        setTimeout(() => {
          window.location.href = "/";
        }, 1000);
      } else {
        alert("Something went wrong");
      }
    })
  });
})





$.urlParam = function (name) {
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  if (results == null) {
    return null;
  }
  else {
    return results[1] || 0;
  }
}
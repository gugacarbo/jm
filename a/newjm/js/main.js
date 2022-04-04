const myslide = document.querySelectorAll('.myslide'),
  dot = document.querySelectorAll('.dot');
let counter = 1;
slidefun(counter);

let timer = setInterval(autoSlide, 8000);
function autoSlide() {
  counter += 1;
  slidefun(counter);
}
function plusSlides(n) {
  counter += n;
  slidefun(counter);
  resetTimer();
}
function currentSlide(n) {
  counter = n;
  slidefun(counter);
  resetTimer();
}
function resetTimer() {
  clearInterval(timer);
  timer = setInterval(autoSlide, 8000);
}

function slidefun(n) {

  let i;
  for (i = 0; i < myslide.length; i++) {
    myslide[i].style.display = "none";
  }
  for (i = 0; i < dot.length; i++) {
    dot[i].className = dot[i].className.replace(' active', '');
  }
  if (n > myslide.length) {
    counter = 1;
  }
  if (n < 1) {
    counter = myslide.length;
  }
  var slblk = myslide[counter - 1];
  slblk.style.display = "block";
  dot[counter - 1].className += " active";
}

// Select categoria

var x, i, j, l, ll, selElmnt, a, b, c;

x = document.getElementsByClassName("custom-select");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < ll; j++) {
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function (e) {
      var y, i, k, s, h, sl, yl;
      s = this.parentNode.parentNode.getElementsByTagName("select")[0];
      sl = s.length;
      h = this.parentNode.previousSibling;
      for (i = 0; i < sl; i++) {
        if (s.options[i].innerHTML == this.innerHTML) {
          s.selectedIndex = i;
          h.innerHTML = this.innerHTML;
          y = this.parentNode.getElementsByClassName("same-as-selected");
          yl = y.length;
          for (k = 0; k < yl; k++) {
            y[k].removeAttribute("class");
          }
          this.setAttribute("class", "same-as-selected");
          break;
        }
      }
      h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function (e) {
    e.stopPropagation();
    closeAllSelect(this);
    this.nextSibling.classList.toggle("select-hide");
    this.classList.toggle("select-arrow-active");
  });
}

function closeAllSelect(elmnt) {
  var x, y, i, xl, yl, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  xl = x.length;
  yl = y.length;
  for (i = 0; i < yl; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < xl; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}

document.addEventListener("click", closeAllSelect);


$('.product-item-second img').click(function () {

  var cover = $('.product-item-main img');
  var thumb = $(this).attr('scr');

  cover.fadeTo('200', '0', function () {
    cover.attr('scr', thumb);
    cover.dafeTo('150', '1');
  });

});




/*
    Newsletter
*/

$("#btn-asign").on('click', function () {
  const ver = (($("#verification")).val());
  if (ver == '4') {

    const email = $("#email-nl").val();
    const nome = $("#name-nl").val();

    const params = {
      'nome': nome,
      'email' : email
    }
    if (validaEmail(email)) {

      $.get("php/cadNewsLetter.php", params, function(data){
        if(data === "email Cadastrado Com Sucesso!"){
          alert("email Cadastrado Com Sucesso!");
          $("#email-nl").val('');
          $("#name-nl").val('');
          $("#verification").val('');
          
        }
        if(data === "email Já Cadastrado!"){
          alert("email Já Cadastrado!");
          $("#email-nl").val('');
          $("#name-nl").val('');
          $("#verification").val('');
          
        }

      })

    } else {
      $("#email-nl").val('');
      $("#email-nl").attr("placeholder", "Email Inválido!");
      alert("Email Inválido!")
    }

  } else {
    $("#verification").val('');
    $("#verification").attr("placeholder", "Teste Incorreto!");

      $("#verification").attr("placeholder", "2 + 2 =");

    alert("Teste Incorreto!")
  }

  
});

function validaEmail(email) {
  var regex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
  return regex.test(email);
}
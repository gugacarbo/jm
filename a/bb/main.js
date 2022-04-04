const textTime = 2800;

$(document).ready(function () {
    $("#btn1").click(function () {
        

        $("#btn1").html("<i class='fas fa-heart heart' style='color: #ff0062;  opacity: 0;'></i>");
        $(".heart").fadeTo(800, 1, ()=>{
            $(".heart").fadeTo(800, 0.6, ()=>{
                $(".heart").fadeTo(800, 1, ()=>{
                    $("#btn1").fadeTo(1000, 0, "linear", function () {
                        $("#btn1").css("display", "none");
            
                        setTimeout(() => {
            
                            $(".backg").css("display", "flex");
            
                            $(".backg").fadeTo(600, 0.6, () => {
                                $(".backg").css("width", "100vw");
                                $(".backg").css("height", "100vh");
                                $(".backg").css("border-radius", "0");
                                $(".backg").fadeTo(300, 1, function () {
                                    $(".content").css("display", "flex")
                                    setTimeout(() => {
                                        $(".content").fadeTo(700, 1)
                                    }, 1000);
            
                                    setTimeout(() => {
                                        $("#text1").fadeTo(800, 0, () => {
                                            $("#text1").css("display", "none")
                                            $("#text2").css("display", "block")
                                            $("#text2").fadeTo(500, 1)
            
                                            setTimeout(() => {
                                                $("#text2").fadeTo(500, 0, () => {
                                                    $("#text2").css("display", "none")
                                                    $("#frase").css("display", "flex");
                                                    $("#frase").fadeTo(500, 1, function () {
            
                                                    })
                                                })
                                            }, textTime);
                                        })
                                    }, textTime);
                                })
                            });
                        }, 300);
                    });
                });
            });
        });

    })

    $("#test").click(()=>{
        $.get("test.php", function(data){
            console.log(data)
        })
        console.log("click")
    })
});


function Like() {
    $(".fa-heart").css("color", "#ff0062")
}



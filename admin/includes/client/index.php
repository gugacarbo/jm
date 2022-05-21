<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link rel="stylesheet" href="client.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
		<script src="jquery.table2excel.js"></script>


</head>

<body>
    <table class="container" id="mailList">

    </table>
    <button id="Export">Export</button>
    <script>
        $(document).ready(function() {
            getNewsletter();
            $("#Export").click(function(e) {
                var table = $("#mailList");
                if (table && table.length) {
                    var preserveColors =
                        (table.hasClass('colorClass') ? true : false);

                    $(table).table2excel({

                        // This class's content is excluded from getting exported
                        exclude: ".noExl",
                        name: "Output excel file ",
                        filename: "outputFile.xls",

                        fileext: ".xls", //File extension type
                        exclude_img: true,
                        exclude_links: true,
                        exclude_inputs: true,
                        preserveColors: preserveColors
                    });
                }

            });
        });



        function getNewsletter() {
            $.get("getClient.php", function(data) {
                $("#mailList").empty();
                data = JSON.parse(data);
                $.each(data, function(i, item) {
                    console.log(item)
                    $("#mailList").append("<tr class='client'><td>" + item["name"] + " " + item["lastName"] + "</td><td>" + item["email"] + "</td><td>" + item["phone"] + "</td> <td>" + item["cpf"] + "</td><td>" + item["bornDate"] + " <td>" + item["date"] + "</td> </td>  </tr>");
                });
            });
        }
    </script>
</body>

</html>
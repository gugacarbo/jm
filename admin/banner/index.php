
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link rel="stylesheet" href="banner.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <script src="/jquery.mask.js"></script>
</head>

<body>

    <div class="container">
        <form method="post" action="" enctype="multipart/form-data" id="myform">
            <div class="bannerContainer">
                <span>Main Banner</span>
                <div class='preview'>
                    <input type="file" id="Banner1File1" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner1Image1">
                </div>

                <div class='preview'>
                    <input type="file" id="Banner1File2" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner1Image2">
                </div>

                <div class='preview'>
                    <input type="file" id="Banner1File3" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner1Image3">
                </div>
            </div>
            <div class="bannerContainer">
            <span>Products Banner</span>

                <div class='preview'>
                    <input type="file" id="Banner2File1" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner2Image1">
                </div>

                <div class='preview'>
                    <input type="file" id="Banner2File2" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner2Image2">
                </div>

                <div class='preview'>
                    <input type="file" id="Banner2File3" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner2Image3">
                </div>
            </div>
            <div class="bannerContainer">
            <span>About Banner</span>

                <div class='preview'>
                    <input type="file" id="Banner3File1" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner3Image1">
                </div>

                <div class='preview'>
                    <input type="file" id="Banner3File2" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner3Image2">
                </div>

                <div class='preview'>
                    <input type="file" id="Banner3File3" name="file" />
                    <img src="">
                    <i class="fa-solid fa-trash"></i>
                    <input type="hidden" id="Banner3Image3">
                </div>
            </div>
        </form>
        <input type="button" class="button" value="Salvar" id="but_upload">
    </div>

    <script src="banner.js"></script>
</body>

</html>
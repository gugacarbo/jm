<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">


    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../admin.css">
</head>
<body>
    <div class="adminBannerContainer adminContainer">
        <span class="adminTitle">Banners</span>
        <p class="adminDescricao">
            Nesta aba você pode inserir, alterar e excluir os full Banners contirnos so site. Caso nao queira usar algum banner, basta deixar os seus respectivos campos de imagem vazios.
        </p>
        <button id="but_uploadBanners">Salvar</button>
        <div class="con">

        <div class="preview">
            <section>
                <span>Banner da Tela Inicial</span>
                <div class="prev">
                    <div class="images" id="imgMain">
                        <img src="noImage.png" class="prev1Img">
                        <img src="noImage.png" class="prev2Img">
                        <img src="noImage.png" class="prev13mg" >
                    </div>
                    <div class="simu">
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                    </div>
                </div>
            </section>
            <section>
                <span>Banner da Tela Produtos</span>
                <div class="prev">
                    <div class="images" id="imgProd">
                        <img src="noImage.png" class="prev1Img">
                        <img src="noImage.png" class="prev2Img">
                        <img src="noImage.png" class="prev3Img">
                    </div>
                    <div class="simu">
                        <b></b>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>
                        <p></p>

                    </div>
                </div>
            </section>
            <section>
                <span>Banner da Tela Sobre</span>
                <div class="prev">
                    <div class="images" id="imgAbout">
                        <img src="noImage.png" class="prev1Img">
                        <img src="noImage.png" class="prev2Img">
                        <img src="noImage.png" class="prev3Img">
                    </div>
                    <div class="simu">


                        <b></b>
                        <b></b>
                        <b></b>
                        <b></b>
                        <b></b>
                        <b></b>
                        <b></b>
                    </div>
                </div>
            </section>
        </div>
        <div class="bannersContent">
            <form method="post" action="" enctype="multipart/form-data" id="myform">
                <div class="bannerContainer">
                    <p>
                        Selecione abaixo as imagens que irão compor o banner da tela inicial. <br>(tamanho da imagem: 1100x500)
                    </p>
                    <div class='preview'>
                        <input type="file" id="Banner1File1" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner1Image1">
                    </div>

                    <div class='preview'>
                        <input type="file" id="Banner1File2" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner1Image2">
                    </div>

                    <div class='preview'>
                        <input type="file" id="Banner1File3" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner1Image3">
                    </div>
                </div>
                <div class="bannerContainer">
                    <p>
                        Selecione abaixo as imagens que irão compor o banner da tela de produtos. <br> (tamanho da imagem: 1100x500)
                    </p>

                    <div class='preview'>
                        <input type="file" id="Banner2File1" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner2Image1">
                    </div>

                    <div class='preview'>
                        <input type="file" id="Banner2File2" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner2Image2">
                    </div>

                    <div class='preview'>
                        <input type="file" id="Banner2File3" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner2Image3">
                    </div>
                </div>
                <div class="bannerContainer">
                    <p>
                        Selecione abaixo as imagens que irão compor o banner da tela sobre.<br> (tamanho da imagem: 1100x500)
                    </p>

                    <div class='preview'>
                        <input type="file" id="Banner3File1" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner3Image1">
                    </div>

                    <div class='preview'>
                        <input type="file" id="Banner3File2" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner3Image2">
                    </div>

                    <div class='preview'>
                        <input type="file" id="Banner3File3" name="file" />
                        <img src="noImage.png">
                        <i class="fa-solid fa-trash"></i>
                        <input type="hidden" id="Banner3Image3">
                    </div>
                </div>
            </form>
        </div>
        </div>



    </div>

    <script src="banner.js"></script>
</body>

</html>
<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '';
    // die($content);
}

?>

<div class="adminBannerContainer adminContainer">
    <span class="adminTitle">Banners</span>
    <p class="adminDescricao">
        Nesta aba você pode inserir, alterar e excluir os full Banners contirnos so site. Caso nao queira usar algum banner, basta deixar os seus respectivos campos de imagem vazios.
    </p>
    <button id="but_uploadBanners">Salvar</button>

    <div class="bannerShow">
        <section>
            <span>Banner da Tela Inicial</span>
            <div class="prev">
                <div class="images" id="imgMain">
                    <img src="img/noImage.png" class="prev1Img">
                    <img src="img/noImage.png" class="prev2Img">
                    <img src="img/noImage.png" class="prev13mg">
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
        <div class="bannerContainer">
            <p>
                Selecione abaixo as imagens que irão compor o banner da tela inicial. <br>(tamanho da imagem: 1100x500)
            </p>
            <div class="preview">
                <input type="file" id="Banner1File1" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner1Image1">
            </div>

            <div class="preview">
                <input type="file" id="Banner1File2" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner1Image2">
            </div>

            <div class="preview">
                <input type="file" id="Banner1File3" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner1Image3">
            </div>
        </div>
    </div>
    <div class="bannerShow">

        <section>
            <span>Banner da Tela Produtos</span>
            <div class="prev">
                <div class="images" id="imgProd">
                    <img src="img/noImage.png" class="prev1Img">
                    <img src="img/noImage.png" class="prev2Img">
                    <img src="img/noImage.png" class="prev3Img">
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
        <div class="bannerContainer">
            <p>
                Selecione abaixo as imagens que irão compor o banner da tela de produtos. <br> (tamanho da imagem: 1100x500)
            </p>

            <div class="preview">
                <input type="file" id="Banner2File1" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner2Image1">
            </div>

            <div class="preview">
                <input type="file" id="Banner2File2" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner2Image2">
            </div>

            <div class="preview">
                <input type="file" id="Banner2File3" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner2Image3">
            </div>
        </div>

    </div>
    <div class="bannerShow">
        <section>
            <span>Banner da Tela Sobre</span>
            <div class="prev">
                <div class="images" id="imgAbout">
                    <img src="img/noImage.png" class="prev1Img">
                    <img src="img/noImage.png" class="prev2Img">
                    <img src="img/noImage.png" class="prev3Img">
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
        <div class="bannerContainer">
            <p>
                Selecione abaixo as imagens que irão compor o banner da tela sobre.<br> (tamanho da imagem: 1100x500)
            </p>

            <div class="preview">
                <input type="file" id="Banner3File1" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner3Image1">
            </div>

            <div class="preview">
                <input type="file" id="Banner3File2" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner3Image2">
            </div>

            <div class="preview">
                <input type="file" id="Banner3File3" name="file" />
                <img src="img/noImage.png">
                <i class="fa-solid fa-trash"></i>
                <input type="hidden" id="Banner3Image3">
            </div>
        </div>
    </div>

</div>
</div>
<script src="includes/banner/banner.js"></script>
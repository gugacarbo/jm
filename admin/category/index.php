<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JM - Admin</title>
    <link rel="icon" href="/img/Jm_Logo_Branco.png">

    <link rel="stylesheet" href="category.css">

    <link href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css" rel="stylesheet">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    <script src="https://kit.fontawesome.com/dd47628d23.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>

<body>
    <div class="container">
        <span class="title">Categorias</span>
        <p class="description">
            Nesta aba voce pode inserir, alterar e excluir categorias. Elas serão importantes pois estarão presentes nos filtros de pesquisa de produtors bem como estarão presentes na criação de Carroséis.
        </p>
        <div class="content" id="CatList">

        </div>
        <div class="chart">
            <span>Produtos Por Categoria</span>
            <canvas id="myChart" width="100%" height="100%"></canvas>
        </div>
    </div>

    <script src="category.js"></script>
</body>

</html>
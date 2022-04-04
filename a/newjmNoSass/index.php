<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta name="description" content="Semijóias com qualidade e preço, acesse nosso site e conheça nossos produtos. | Segurança e praticidade para fazer suas compras!" />
	<meta name="keywords" content="acessórios, acessórios de luxo, brinco, brincos,semijoias,semijóas, comprar Semijóias, comprar joias, loja de jóias, loja de semijóias, jóias, joias, jm acessórios, jm, joias e semijoias, brinco, pulseira, aneis, presente, dia das maes, dia dos namorados, comprar presente">
	<meta name="author" content="Andrei Jean, Gustavo Carbonera">
	<meta name="creator" content="inCube Dev">
	<meta name="publisher" content="inCube Dev">
	<meta name="google" content="nopagereadaloud" />
	<meta name="robots" content="notranslate">
	<meta property="og:title" content="Acesse nosso link e fique por dentro das nossas promoções e novidades" />
	<meta property="og:type" content="product" />
	<meta property="og:description" content="Receba no e-mail nossas promoções e novidades, acesse nossa loja no Instagram e conheça nossos produtos, Segurança e praticidade na hora da compra, acessórios de luxo na palma da sua mão!!!" />
	<meta property="og:image" content="img/og_logo.png" />
	<meta property="og:url" content="landing.incubedev.com.br" />
	<meta property="og:site_name" content="JM Acessórios de Luxo" />

	<link href="css/all.css" rel="stylesheet">
	<link href="css/header-footer.css" rel="stylesheet">
	<link href="css/contact.css" rel="stylesheet">
	<link href="css/home.css" rel="stylesheet">
	<link href="css/banner.css" rel="stylesheet">
	<link href="css/products.css" rel="stylesheet">
	<link href="css/about.css" rel="stylesheet">
	<link href="css/product.css" rel="stylesheet">
	<link href="css/product-view.css" rel="stylesheet">
	<link href="css/cart.css" rel="stylesheet">
	<link href="css/info.css" rel="stylesheet">

	<link href="lib/fontawesome/css/all.css" rel="stylesheet">

	<link rel="preconnect" ref="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Aldrich&display=swap" rel="stylesheet">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>



</script>
	
	<title>JM Acessórios de Luxo</title>
</head>

<body>
	<div class="Container">

	<header>
			
			<div class="header-content">
			<a href="index.php"><img src="img/logo_pc.png" class="logo-img"></a>
			<div class="nav">
				<nav class="menu">
					<ul>
						<li><a href="index.php">Home</a></li>
						<li><a href="index.php?pg=about">Sobre</a></li>
						<li><a href="index.php?pg=products">Produtos</a></li>
						<li><a href="index.php?pg=home#contact">Contato</a></li>
					</ul>
				</nav>
			</div>
			
			<span class="ig-link"><a href="https://www.instagram.com/jomartelloacessorios/" target="blank"><i class="fab fa-instagram" aria-hidden="true"></i></a></span>
			<span class="wpp-link"><a href="https://api.whatsapp.com/send?phone=5549999604384" target="blank"><i class="fab fa-whatsapp" aria-hidden="true"></i></a></span>
			<input type="text" name="search" class="search-bar" placeholder="Pesquise por produtos">
			<button id="btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
			<span class="shopping-cart"><a href="index.php?pg=cart"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></span>
			
			
		</div>
	</header>
	
	
	<?php
	if (!isset($_GET['pg']) or empty($_GET['pg'])) {
		include("home.php");
	} else {
		$link = "{$_GET['pg']}.php";
		if (file_exists($link)) {
			include($link);
		} else {
			include("erro.php");
		}
	}
	?>
	<footer class="footer-style">
		<div class="footer-content">
			<nav class="nav-footer">
				<ul>
					<li><a href="index.php">Home</a></li>
					<li><a href="index.php?pg=about">Sobre</a></li>
					<li><a href="index.php?pg=products">Produtos</a></li>
					<li><a href="index.php?id=contact">Contato</a></li>
				</ul>
			</nav>
			
			<img src="img/logo_pc.png" class="img-footer">
			
			<center>
				<img src="img/incube.png" class="incube-img">
			</center>
		</div>
	</footer>
	
	
	<script src="js/jquery.js"></script>
	<script src="js/main.js"></script>
	
	
</div>
</body>

</html>
<?php
include("php/db_connect.php");

include("php/banner.php");

setBanner("Main", $conn);
?>


<section class="category-nav">
	<a href="index.php?pg=products&categoria=colar">
		<article class="category-item">
			<img src="img/necklace-hover-off.png">
			<span>Colares</span>
		</article>
	</a>
	<a href="index.php?pg=products&categoria=brinco">
	<article class="category-item">
		<img src="img/earing-hover-off.png">
		<span>Brincos</span>
	</article>
	</a>
	<a href="index.php?pg=products&categoria=anel">
	<article class="category-item">
		<img src="img/ring-hover-off.png">
		<span>Anéis</span>
	</article>
	</a>
</section>





<section class="items">
		
<?php
 
		
		$sql = "SELECT * FROM produtos";

		$stmt = $conn->prepare($sql);

		$stmt->execute();

		$result = $stmt->get_result();

		while ($row = $result->fetch_assoc()) {
?>
			<article class="item-style">
				<a <?php echo "href='index.php?pg=product&ref=" . $row['referencia'] . "'"; ?>>
					<div class="item-image">
						<?php
						if ($row['promo']) {
							$promo = round((1 - ($row['preco'] / $row['preco_promo'])) * 100);
							echo "<span class='item-sale'>" . $promo . "% OFF</span>";
						}

						$img_array = (json_decode($row['fotos']));
						?>
						<img <?php echo "src='img/" . $img_array->{'1'} . "'"; ?> >
					</div>
				</a>
				<span class="item-title"><a href="index.php?pg=product"><?php echo $row['nome']; ?></a></span>
				<span class="item-price"><a href="index.php?pg=product">R$ <?php echo number_format($row['preco'], 2, ',', ' '); ?></a></span>
				<span class="item-credit"><a href="index.php?pg=product">ou 3x R$<?php echo number_format(($row['preco']*1.2/3), 2, ',', ' '); ?></a></span>
				<span class="item-add-cart"><a <?php echo "href='index.php?pg=product&ref=" . $row['referencia'] . "'"; ?>><i class="fa fa-cart-plus" aria-hidden="true"></i></a></span>
			</article>

<?php
		}
?>




</section>

<section class="newsletter">
	<div class="content-newsletter">
		<h2 class="nl-title">Assine nossa Newsletter</h2>
		E receba no seu e-mail nossas promoções e novidades!

		<div id="frm-newsletter">
			<input type="text" id="name-nl" class="nl-input" placeholder="Nome" required>
			<input type="email" id="email-nl" class="nl-input" placeholder="email@dominio.com" required>
			<input type="text" id="verification" class="nl-verification" placeholder="2 + 2 =">
			<button id="btn-asign" >Assinar a Newsletter</button>
	</div>

	</div>
</section>





<section class="contact">
	<div class="contact-content">
		<h2 class="contact-title">Contato</h2>
		<span>Entre em contato conosco e nos envie sua mensagem.</span>
		<form id="frm-contact" method="post">
			<input type="text" name="name-contact" placeholder="Nome">
			<input type="email" name="email-contact" placeholder="email@dominio.com.br">
			<textarea placeholder="Digite sua mensagem"></textarea>
			<button id="btn-enviar" >Enviar</button>
		</form>
	</div>
</section>
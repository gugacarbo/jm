<br>


<script>


	function searchForm(){
		const nome = $("#form_nome").val();
		const precoMin = ($("#form_precoMin").val());
		const precoMax = parseFloat($("#form_precoMax").val());

		let data = {
			"nome" : nome,
			"precoMin" : precoMin,
			"precoMax" : precoMax,
		}

		console.log(data);
	}


</script>


<section class="filter-container">

		<span class="spn-frm-filter">Preço min.</span>
		<input type="text" name="precoMin" id="form_precoMin" class="input-price" placeholder="0,00" <?php

																					if (isset($_GET['precoMin'])) {
																						echo ("value='" . $_GET['precoMin'] . "'");
																					} ?>>
		<span class="spn-frm-filter">Preço máx.</span>
		<input type="text" name="precoMax" id="form_precoMax" class="input-price" placeholder="0,00" <?php
																					if (isset($_GET['precoMax'])) {
																						echo ("value='" . $_GET['precoMax'] . "'");
																					}
																					?>>
		<span class="spn-frm-filter">Categorias</span>
		<div class="custom-select" style="width: 200px">
			<select name="categoria">
				<option value="anel">Anéis</option>
				<option value="colar">Colares</option>
				<option value="brinco">Brincos</option>
				<option value="bracelete">Braceletes</option>
			</select>
		</div>
		<span class="spn-frm-filter">Filtrar por</span>
		<div class="custom-select" style="width: 120px;">
			<select>
				<option value="0">Menor Valor</option>
				<option value="1">Maior Valor</option>
				<option value="2">Mais Recentes</option>
			</select>
		</div>
		<input type="text" name="nome" class="search-products-bar" placeholder="Pesquise por produtos" id="form_nome">
		<button id="btn-filter" onclick="searchForm()"> Buscar</button>

</section>

<section class="items">

	<?php

	include("php/db_connect.php");


	$vowels = array("=", "/", "=", ":", ";", "|");

	$mainQuery = "SELECT * FROM produtos WHERE";

	$query = "";

	$filter = [];
	$filter_val;

	if (isset($_GET['nome']) && $_GET['nome'] != "") {
		$nome = str_replace($vowels, "", $_GET['nome']);
		$filter['nome'] = "nome = '" . $nome . "'";
	}

	if (isset($_GET['categoria']) && $_GET['categoria'] != "") {
		$cat_name = str_replace($vowels, "", $_GET['categoria']);
		$sql = "SELECT id FROM  categorias WHERE nome = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $cat_name);
		$stmt->execute();
		$result = $stmt->get_result();
		$res = $result->fetch_assoc();
		$filter_id = $res['id'];

		//echo $filter_id;
		$filter['categoria'] = "categoria =" . $filter_id;
	}

	if (isset($_GET['precoMin']) && isset($_GET['precoMax']) && $_GET['precoMin'] != "" && $_GET['precoMax'] != "") {
		$precoMin = str_replace($vowels, "", $_GET['precoMin']);
		$precoMax = str_replace($vowels, "", $_GET['precoMax']);

		$filter['preco'] = "preco >= " . $precoMin . " AND preco <= " . $precoMax;
	} else if (isset($_GET['precoMin']) && $_GET['precoMin'] != "") {
		$precoMin = str_replace($vowels, "", $_GET['precoMin']);
		$filter['preco'] = "preco >= " . $precoMin;
	} else if (isset($_GET['precoMax']) && $_GET['precoMax'] != "") {
		$precoMax = str_replace($vowels, "", $_GET['precoMax']);
		$filter['preco'] = "preco <= " . $precoMax;
	}


	$filterNum = count($filter);

	if ($filterNum > 0) {
		$query = "";
		$filterCount = 0;

		foreach ($filter as $filter) {
			$Lquery = $query;
			$query = $Lquery . " " . $filter;

			$filterCount++;

			if ($filterCount < $filterNum) {
				$query = $query . " AND ";
			}
		}
	} else {
		$query = "1";
	}

	$mainQuery .= $query;



	$result = $conn->query($mainQuery);









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
					<img <?php echo "src='img/" . $img_array->{'1'} . "'"; ?> style="height: 175px;">
				</div>
			</a>
			<span class="item-title"><a href="index.php?pg=product"><?php echo $row['nome']; ?></a></span>
			<span class="item-price"><a href="index.php?pg=product">R$ <?php echo number_format($row['preco'], 2, ',', ' '); ?></a></span>
			<span class="item-credit"><a href="index.php?pg=product">ou 3x R$<?php echo number_format(($row['preco'] * 1.2 / 3), 2, ',', ' '); ?></a></span>
			<span class="item-add-cart"><a <?php echo "href='index.php?pg=product&ref=" . $row['referencia'] . "'"; ?>><i class="fa fa-cart-plus" aria-hidden="true"></i></a></span>
		</article>

	<?php
	}
	?>



</section>
<div class="products-nav">
	<a href="">
		<div class="product-next"><i class="fa fa-angle-right" aria-hidden="true"></i></div>
	</a>
	<p class="page-act">1</p>
	<a href="">
		<div class="product-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></div>
	</a>
</div>
<br>

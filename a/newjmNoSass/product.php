
<script>

	var get = <?php echo json_encode($_GET['ref']) ?>;
	var ref = get;
	ref = parseInt(ref);
	
	$.get("php/getItemByReference.php", {ref}, function(props){
		
		try{
			const data = (JSON.parse(props));

			data['fotos'] = JSON.parse(data['fotos']);
			$(".product-item-title").text(data['nome']);
			$(".product-item-sale").text("De R$"+data['preco_promo']);
			$(".product-item-price").text(""+data['preco']);
			$(".description-content").text("");
			$(".description-content").append(""+data['descricao']);
			$(".reference").text(""+data['referencia']);
			$(".product-material").text(""+data['material']);
			
			//<!--<img src="img/brinco.png" style="width: 450px;" id="main_img">-->
			$(".product-item-main").append("<img src='img/"+data['fotos'][1]+"' style='width: 450px;' id='main_img'>");


			for(var i in data['fotos']){
				//console.log(data['fotos'][i]);
				$(".product-item-second").append("<img src='img/"+data['fotos'][i]+"' style='width: 80px;' onclick='changeImage("+i+")' id='img"+i+"'>");
			}
		}catch{
			window.location.href = "index.php";
		}
		
		
		
	});

	function changeImage(i){
		const img_id = "img"+i;
		$("#main_img").attr('src', ($("#"+img_id).attr('src')));
	}

	 
</script>

<br><br><br><br>

<section class="product-container">

	<div class="product-item-gallery">
		<div class="product-item-main">
			<!--<img src="img/brinco.png" style="width: 450px;" id="main_img">-->
		</div>

		<div class="product-item-second">
		</div>
	</div>



	<div class="product-item-description">
		<span class="product-item-title">Prduct_Name</span><Br>
		<span class="product-item-sale">Product_price_promo</span><br>
		<span class="RS">R$</span> 
		<span class="product-item-price"> Product_Price</span>
		
		<img src="img/pagseguro.png" class="pagseguro"><br>

		<form class="check-postal-code">
			<input type="text" name="" class="ipt-postal-code" placeholder="00-000-000">
			<button class="btn-postal-code">Calcular Frete</button>
		</form>

		<div class="btn-buy-box">
			<button class="btn-add-cart">Adicionar ao carrinho</button>
			<button class="btn-buy">Comprar</button>
		</div>

		<div class="item-description">
			<p class="description-content">Product_Desctiption</p>
			<Br><Br>
			<span><b>Material: </b><b class="product-material"> lorem ipsum dolor</b><br><br>
			<b>Referencia: </b><b class="reference"> 000000</b></span>
		</div>
	</div>

</section>
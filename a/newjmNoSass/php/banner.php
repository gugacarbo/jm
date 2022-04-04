<?php
	include("php/db_connect.php");



	function setBanner($n, $conn){
		$nome = $n;

		$sql = "SELECT * FROM banner WHERE Nome = ?";

		$stmt = $conn->prepare($sql);

		$stmt->bind_param("s", $nome);
		
		$stmt->execute();

		$result = $stmt->get_result();

		$img_num = 0;
?>

<br>
<div class="slider">

	<?php
	while ($row = $result->fetch_assoc()) {
			$img = json_decode($row['Imagens']);
			foreach ($img as $i) {
				$img_num ++;
	?>			
	<div class="myslide fade">
		<img <?php echo ("src='img/" . $i . "'"); ?> style="width: 100%; height: 100%; display: block;">
	</div>
	<?php
			}
	}
	?>
	<div class="Control_Img">
	<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
  	<a class="next" onclick="plusSlides(1)">&#10095;</a>

	<div class="dotsbox" >
		<?php
			for($i = 0; $i < $img_num; $i ++){
		?>
		<span class="dot" <?php echo("onclick='currentSlide(".($i+1).")'"); ?> ></span>
		<?php 
			}
		?>
		
	</div>
	</div>
</div>
<br>

<?php
	}
?>
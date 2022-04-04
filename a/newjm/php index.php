	
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
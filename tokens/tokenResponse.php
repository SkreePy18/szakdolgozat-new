<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="../assets/css/tokenResponse.css">
	</head>
	<body>
		
		<img class="icon" src="./<?php echo($_GET['icon']); ?>.png" height="256"/>
		<h1 class='messageText'>
			<?php 
				if(isset($_GET['message'])) {
					echo($_GET['message']);
				}
			?>
		</h1>
	</body>
</head>
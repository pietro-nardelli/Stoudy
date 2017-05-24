<?php echo'<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
head>
<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("index.css");
	</style>

</head>
<body>
	<div id="topper">
		<div>
		<a href="index.html">
		<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logo.png" style="width: 100%;"/>
			</a>
		</div>
	</div>
	<h1>Registrazione andata a buon fine!
		<div style="font-size: 75%; font-weight: normal;">Tra 3 secondi verrai reindirizzato...</div>
	</h1>
<?php 
header("refresh: 3; url=login.php");
?>
</body>
</html>
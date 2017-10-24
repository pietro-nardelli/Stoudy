<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("css/home-studente.css");
	</style>	
</head>
<body>
<?php
include('default-code/info-studente.php');
?>
<div id="main">
	<?php
	for ($k=0; $k < $materie->length; $k++) {	
		$materia = $materie->item($k);
		if (strcasecmp($_GET['nomeMateria'] , $nomeMateriaText[$k]) == 0) {
			$trovato = true;
			$materia = $studente->getElementsByTagName('materia')->item($k);
			$materia->parentNode->removeChild($materia); //Serve perchè altrimenti da errore!
			$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
			$doc->save($path); //Sovrascriviamolo 
			?>
			<div id='message'>
				<img src="images/iconMessage.png">
				<div>
					<strong>Hai deciso di eliminare <?= $materia->firstChild->textContent ?>. I tuoi riassunti non verrano eliminati.</strong>
					<br />
					Ti stiamo reindirizzando...
				</div>
			</div>
			<?php
			header("refresh:3; url=home-studente.php");
		}
	}
	if (!$trovato) {
		header('Location: home-studente.php');
	} 
?>
</div>

</body>
</html>
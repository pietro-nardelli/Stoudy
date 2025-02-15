<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("css/riassunto.css");
	</style>	
</head>
<body>
<?php
$trovatoEsatto = -1;
$tagRicercato = $_GET['tagRicercato'];
$trovato = false;
include("default-code/info-studente.php");
include("default-code/caricamento-tags-xml.php");


for ($k=0; $k < $tags->length; $k++) {	
	//Controlla se c'è una sottostringa nel nomeTagText[$k]
	if (!empty ($_GET['tagRicercato'])) {
		if (strcasecmp($nomeTagText[$k], $_GET['tagRicercato']) == 0) { //Controlliamo se il tag cercato è ESATTAMENTE un tag
			$trovatoEsatto = $k; //Associamo alla flag l'indice del tag presente in lista così lo usiamo dopo per mostrare l'descrizione
			$break;
		}
	}
}


?>
<div id="main">
		<?php 
		if ($trovatoEsatto != -1) {
			?>
			<div id="modificaDescrizione">
				<div id="risultatoRicercaAlto">
					Modifica descrizione per il tag <a id='tagRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato="<?= $nomeTagText[$trovatoEsatto] ?>"'> <?= $nomeTagText[$trovatoEsatto] ?></a>
					<br />
				</div>
				<?php
				if (isset($_POST['submit'])) {
					if (strlen($_POST['modificaDescrizione']) > 500 || strlen($_POST['modificaDescrizione']) == 0) {
						?>
						<p style="color: red;">Non può essere più lungo di 500 caratteri oppure vuoto.</p>
						<?php
					}
					else{
						$_SESSION['modificaDescrizione'] = $_POST['modificaDescrizione'];
						$_SESSION['tagRicercato'] = $tagRicercato;
						header('Location: revisione-descrizione.php');
						exit();
					}
				}
				?>
				<form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
				<?php
					if (!empty($descrizioneTagText[$trovatoEsatto])){ 
						?>
						<textarea rows="5" name="modificaDescrizione"><?php if (!isset($_POST['submit'])) { echo $descrizioneTagText[$trovatoEsatto]; } else if (isset($_POST['submit'])){ echo $_POST['modificaDescrizione']; } else if (isset($_SESSION['modificaDescrizione'])){ echo $_SESSION['modificaDescrizione']; } ?></textarea>
						<br /><br />
					<?php 
					}
					else {
						?>
						<textarea rows="5" name="modificaDescrizione" placeholder=" Inserisci un descrizione (descrizione) al tag selezionato. Non può essere più lunga di 500 caratteri."><?php if (isset($_POST['modificaDescrizione'])){ echo $_POST['modificaDescrizione']; } else if (isset($_SESSION['modificaDescrizione'])){ echo $_SESSION['modificaDescrizione']; } ?></textarea>
						<br /><br />
						<?php
					}
					?>
					<input type="submit" name="submit" value="Modifica descrizione" />
				</form>
			</div>
		<?php
		}
		else {
			?>
			<div id='message'>
				<img src="images/iconMessage.png">
				<div>
					<strong>Tag non trovato.</strong>
					<br />
					Ti stiamo reindirizzando...
				</div>
			</div>
			<?php
			header("refresh:3; url=home-studente.php");
		}

		?>
</div>
</body>
</html>
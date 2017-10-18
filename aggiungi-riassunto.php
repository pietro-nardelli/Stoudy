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
include 'functions/upload.php';
include("default-code/info-studente.php");
?>

<div id="main">
<?php
	if (isset($_GET['nomeMateria'])) { ?>
		<div id="aggiungiRiassunto">
			<div id="nomeMateria">
				Aggiungi un riassunto di <b><?= $_GET['nomeMateria'] ?></b>
			</div>
			<?php
			if (isset($_POST['submit'])) {
				$erroreTags = 0; //Se rimane 0 tutto ok
				$errore = 0;

				//Titolo di almeno 10 caratteri e massimo 100 caratteri
				if (strlen($_POST['titoloRiassuntoForm']) < 10 || strlen($_POST['titoloRiassuntoForm']) > 100) {
					?>
					<p style="color: red;">Titolo troppo corto / lungo.</p>
					<?php
					$errore = 1; 
				}
				//Descrizione massimo 500 caratteri
				if (strlen($_POST['descrizioneRiassuntoForm']) > 500) {
					?>
					<p style="color: red;">Descrizione troppo lunga.</p>
					<?php
					$errore = 1;
				}
				
				/* Assegnamo ogni tag all'array tagsRiassuntoNuovo */
				$tagsRiassuntoNuovo = explode(" ", $_POST['tagsRiassuntoForm']); //Divide la stringa in sottostringhe
				foreach ($tagsRiassuntoNuovo as $t => $value) { 
					$tagsRiassuntoNuovo[$t] = trim($value); 
					if (strlen($tagsRiassuntoNuovo[$t]) == 0 || is_numeric($tagsRiassuntoNuovo[$t]) || strlen($tagsRiassuntoNuovo[$t]) > 20)  {
						$erroreTags = 1;
					}
				}
				/* Controlliamo che non ci siano tag uguali */
				foreach ($tagsRiassuntoNuovo as $k => $v) {
					for ($i = $k+1; $i < sizeof($tagsRiassuntoNuovo) ; $i++ ) {
						if (strcasecmp($v, $tagsRiassuntoNuovo[$i]) == 0) {
							$erroreTags = 1;
						}
					}
				}

				//Tag di almeno 1 carattere, massimo 20 non numerico e non uguali...
				if ($erroreTags == 1) {
					?>
					<p style="color: red;">I tag non devono essere vuoti, devono essere alfanumerici, non più lunghi di 20 caratteri e non possono essercene due uguali.</p>
					<?php
					$errore = 1;
				}
				//... e massimo 5 tag
				if ($t > 4) {
					echo '<p style="color: red;">Hai inserito troppi tag. Massimo 5.</p>';
					$errore = 1;
				}
				//Riassunto pubblico o privato
				if (!isset($_POST['condivisioneRiassuntoForm'])) {
					?>
					<p style="color: red;">Scegliere se rendere il riassunto pubblico o privato.</p>
					<?php
					$errore = 1;
				}

				//Se è stato compilato correttamente il form...
				if ($errore == 0) {	
					$linkDocumento = upload (); //Proviamo a caricare il pdf
					if (is_numeric($linkDocumento)) { //Se produce un numero (zero) allora c'è errore, altrimenti restituirebbe il link al PDF caricato
						?>
						<p style="color: red;">Errore nel caricamento del PDF!</p>
						<?php
					}
					else { //Se $errore = 0 e il documento è stato caricato correttamente...allora assegna i cookie corrispondenti
						$_SESSION['nowDate'] = date("Y-m-d"); //Data odierna
						$_SESSION['nowTime'] = date("H:i:s"); //Ora odierna
						$_SESSION['titoloRiassunto'] = $_POST['titoloRiassuntoForm'];
						$_SESSION['descrizioneRiassunto'] = $_POST['descrizioneRiassuntoForm'];
						$_SESSION['linkDocumentoRiassunto'] = $linkDocumento;
						$_SESSION['condivisioneRiassunto'] = $_POST['condivisioneRiassuntoForm'];
						$_SESSION['tagsRiassuntoNuovo'] = $tagsRiassuntoNuovo; //Questa è una variabile session che gestisce l'array dei tags
						$_SESSION['anteprimaRiassunto'] = 1000;
						header("Location: anteprima-riassunto.php?nomeMateria=".$_GET['nomeMateria'].""); //Ricarichiamo la pagina
						exit();
					}
				}
			}
			//Se torniamo dall'anteprima dobbiamo mantenere i valori precedentemente inseriti nel form
			if (isset($_SESSION['anteprimaRiassunto'])) {
				?>
				<form action="<?= $_SERVER["PHP_SELF"].'?nomeMateria='.$_GET['nomeMateria'] ?>" method="POST" enctype="multipart/form-data">
					<input type="text" name="titoloRiassuntoForm" placeholder=" Inserisci un titolo" value="<?= $_SESSION['titoloRiassunto']; ?>" /><br /><br />
					<textarea rows="2" name="descrizioneRiassuntoForm" placeholder=" Inserisci una descrizione (optional)"><?= $_SESSION['descrizioneRiassunto']; ?></textarea><br /><br />
					<label><img src="images/iconCaricaPdf.png" style="width: 16px;"> carica un PDF... 
					<input type="file" name="fileToUpload" />
					</label>
					<br /><br />
					<input type="text" name="tagsRiassuntoForm" placeholder =" Inserisci tag (max 5) divisi da virgole" value="<?php for($k=0;$k<sizeof($_SESSION['tagsRiassuntoNuovo'])-1;$k++) { echo $_SESSION['tagsRiassuntoNuovo'][$k]." "; }  echo $_SESSION['tagsRiassuntoNuovo'][$k];?>"/><br /><br />
					<?php
					if (strcasecmp($_SESSION['condivisioneRiassunto'], "pubblico") ==0 ) {
						?>
						<input type="radio" name="condivisioneRiassuntoForm" value="pubblico" checked> Pubblico
						<input type="radio" name="condivisioneRiassuntoForm" value="privato"> Privato <br />
						<?php 
					}
					?>
					<?php
					if (strcasecmp($_SESSION['condivisioneRiassunto'], "privato") ==0 ) {
						?>
						<input type="radio" name="condivisioneRiassuntoForm" value="pubblico"> Pubblico
						<input type="radio" name="condivisioneRiassuntoForm" value="privato" checked> Privato <br />
						<?php 
					}
					?>
					<input type="submit" name="submit" value="Visualizza anteprima" />
				</form>
			<?php
			}
			else {
				?>
				<form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST" enctype="multipart/form-data">
					<input type="text" name="titoloRiassuntoForm" placeholder=" Inserisci un titolo" /><br /><br />
					<textarea rows="2" name="descrizioneRiassuntoForm" placeholder=" Inserisci una descrizione (optional)"></textarea><br /><br />
					<label><img src="images/iconCaricaPdf.png" style="width: 16px;"> carica un PDF... 
					<input type="file" name="fileToUpload" />
					</label>
					<br /><br />
					<input type="text" name="tagsRiassuntoForm" placeholder =" Inserisci tag (max 5) divisi da virgole" /><br /><br />
					<input type="radio" name="condivisioneRiassuntoForm" value="pubblico"> Pubblico
					<input type="radio" name="condivisioneRiassuntoForm" value="privato"> Privato <br />
					<input type="submit" name="submit" value="Visualizza anteprima" />
				</form>
				<?php
			}
			?>
		</div> 
	<?php
	} //Tutto questo è visualizzato solo se c'è nomeMateria nel GET
	else {
		echo "Impossibile aggiungere riassunto senza una materia!";
	}
	?>
</div>
</body>
</html>

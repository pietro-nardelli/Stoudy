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
include 'functions/giorniDisponibili.php'; 
include("default-code/info-studente.php");
?>
<div id="main">
	<?php 
	/* ABBIAMO PREMUTO "AGGIUNGI MATERIA" */
	if (empty($_SESSION['status'])) {
		?>
		<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post" id="aggiungiMateria">
			<div>
			Aggiungi una materia
			</div>
			<p>
			Puoi decidere se pianificare una materia sviluppando un <b>piano di studi</b> per arrivare all'esame preparato.
			<br />
			Oppure puoi organizzarti autonomamente, ma in entrambi casi hai la possibilità di scrivere dei <b>riassunti</b>.
			</p>
			<?php
			$flagMateriaEsistente = false; //Se questo flag è true allora esiste già una materia con lo stesso nome
			if (isset($_POST['submit'])) {	
				if (!empty($_POST['nomeMateria']) && !empty($_POST['status']) ) {
					foreach ($nomeMateriaText as $k=>$v) { //Scorriamo le materie
						//Se esiste una materia con quel nome restituisci errore
						if ( !strcasecmp($_POST['nomeMateria'], $nomeMateriaText[$k]) ) {	
							?>
							<p style="color: red;">Materia già presente.</p>
							<?php
							$flagMateriaEsistente = true;
						}
					}
					//Se la materia è nuova, prosegui e aggiorna.
					if (!$flagMateriaEsistente) {
						$_SESSION['nomeMateria']= $_POST['nomeMateria'];
						$_SESSION['status']= $_POST['status'];

						header("Location: aggiungi-materia.php");
					}
				}
				if (empty($_POST['nomeMateria']) || empty($_POST['status']) ) {
					?>
					<p style="color: red;">Compilare tutti i campi.</p>
					<?php
				}
			}
			?>

			<input type="text" name="nomeMateria" placeholder=" Nome della materia" <?php if (isset($_POST['nomeMateria'])){ echo 'value="'.$_POST['nomeMateria'].'"'; } ?> />
			<input type="radio" name="status" value="planned" <?php if (isset($_POST['status']) && $_POST['status'] == 'planned'){ echo 'checked'; } ?> /> <label>Pianifica</label>
			<input type="radio" name="status" value="unplanned" <?php if (isset($_POST['status']) && $_POST['status'] == 'unplanned'){ echo 'checked'; } ?> /> <label>Non pianificarla</label><br />
			<input type="submit" name="submit" value="Continua" />
		</form>
	<?php 
	}

	/* ABBIAMO SELEZIONATO "NON PIANIFICARE" */
	else if (!empty($_SESSION['status']) && strcasecmp($_SESSION['status'], "unplanned") == 0 ) {
		$newMateria = $doc->createElement("materia");
		$newNomeMateria = $doc->createElement("nomeMateria", $_SESSION['nomeMateria']);
						
		/*Attacchiamo all'elemento principale tutti i suoi figli e i corrispettivi "nipoti"*/
		$newMateria->appendChild($newNomeMateria);			
		$newMateria->setAttribute("status", $_SESSION['status']);
		
		
		/*Inseriamo il nodo che abbiamo creato, prima del primo elemento della lista di elementi dopo root*/
		$materieElement->insertBefore($newMateria);
		$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
		$doc->save($path); //Sovrascriviamolo

		unset($_SESSION['nomeMateria']);
		unset($_SESSION['status']);
		header("Location: home-studente.php");
		exit();

	}

	/* ABBIAMO SELEZIONATO "PIANIFICA" */
	else if (!empty($_SESSION['status']) && empty($_SESSION['oggettoStudio']) && 
			strcasecmp($_SESSION['status'], "unplanned") != 0 ) { ?>
		<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="aggiungiMateria">
			<div>
			Aggiungi una materia
			</div>
			<p>
			Inserisci cosa si vuole monitorare. 
			<br />
			Ad esempio, se si deve studiare un libro di 300 pagine inserire "pagine" nel form sottostante.
			</p>
			<?php
			if (isset($_POST['submit'])) {	
				if ($_POST['oggettoStudio']) { //Se oggetto studio è stato inserito, prosegui e aggiorna.
					$_SESSION['oggettoStudio']= $_POST['oggettoStudio'];
					
					header("Location: aggiungi-materia.php");
				}
				else { //Se qualche campo non è stato compilato...
					?>
					<p style="color: red;">Compilare tutti i campi.</p>
					<?php
				}
			}
			if (isset($_POST['back'])) { //Se abbiamo premuto back, torna al primissimo caso
				unset($_SESSION['status']);

				header("Location: aggiungi-materia.php");
			}
			?>

			<input type="text" name="oggettoStudio" placeholder="Cosa vuoi monitorare?"/> <br />	

			<input type="submit" name="back" value="Indietro" />	
			<input type="submit" name="submit" value="Continua" />
		</form>
	<?php
	}

	/* ABBIAMO INSERITO UN OGGETTO DI STUDIO: ULTIMO PASSAGGIO */
	else {
		?>
		<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post" id="aggiungiMateria">
			<div>
			Aggiungi una materia
			</div>
			<p>
			Ancora un ultimo passaggio e abbiamo finito!
			</p>
			<?php

			if (isset($_POST['submit'])) {	
				if (!empty($_POST['valoreDaStudiare'])) { 
					if (is_numeric ($_POST['valoreDaStudiare']) && $_POST['valoreDaStudiare'] > 0 ) {
						$_SESSION['valoreDaStudiare']= $_POST['valoreDaStudiare'];
					}
					else {
						unset($_SESSION['valoreDaStudiare']);
						?>
						<p style="color: red;">Valore da studiare deve essere un n° maggiore di 0.</p>
						<?php
					}
				}
				if ($_POST['dataScadenza']) {
					$dateTime = date_create_from_format('Y-m-d', $_POST['dataScadenza']);
					$errors = date_get_last_errors();
					$oggi = date('Y-m-d'); //Questo serve per la funzione giorniDisponibili()
					

					if (empty($errors['warning_count']) && $dateTime != false) {
						$_SESSION['dataScadenza'] = $_POST['dataScadenza'];
						//Trasformiamo dataScadenza nel formato corretto per la funzione giorniDisponibili()										
						$dataScadenza = $dateTime->format('Y-m-d'); 
					}
					else {
						unset($_SESSION['dataScadenza']);
						?>
						<p style="color: red;">La data non è stata compilata correttamente.</p>
						<?php
					}
				}
				
				if ($_POST['nGiorniRipasso']) {
					if (is_numeric ($_POST['nGiorniRipasso']) && $_POST['nGiorniRipasso'] >= 0) {
						$_SESSION['nGiorniRipasso'] = $_POST['nGiorniRipasso'];
					}
					else {
						unset($_SESSION['nGiorniRipasso']);
						?>
						<p style="color: red;">Giorni di ripasso deve essere un n >= 0.</p>
						<?php
					}
				}
				else { //Se non è stato inserito nulla, allora il valore è 0
					$_SESSION['nGiorniRipasso'] = "0";
				}

				if ($_POST['valoreStudiato']) {
					if (is_numeric ($_POST['valoreStudiato']) && $_POST['valoreStudiato'] >= 0) {
						$_SESSION['valoreStudiato'] = $_POST['valoreStudiato'];
					}
					else {
						unset($_SESSION['valoreStudiato']);
						?>
						<p style="color: red;">Valore studiato deve essere un n° maggiore di 0.</p>
						<?php
					}
				}
				else { //Se non è stato inserito nulla, allora il valore è 0
					$_SESSION['valoreStudiato'] = "0";
				}

				//Questo if serve per far apparire solo una volta l'errore corrispondente
				//N.B. Per DATE inserendo isset c'è errore.
				if (!isset($_POST['valoreDaStudiare']) || empty($_POST['dataScadenza']) ) { 
					?>
					<p style="color: red;">Compilare tutti i campi obbligatori.</p>
					<?php
				}
				else { //Se tutte le variabili sono presenti...
					//Se non ci sono abbastanza giorni disponibili, compresi i giorni di ripasso...
					if ($dateTime != false) {
						if (giorniDisponibili($oggi,$dataScadenza, $_POST['nGiorniRipasso']) <= 0) {
							unset($_SESSION['valoreDaStudiare']);
							unset($_SESSION['dataScadenza']);
							unset($_SESSION['nGiorniRipasso']);
							unset($_SESSION['valoreStudiato']);
							?>
							<p style="color: red;">Ops, non hai abbastanza giorni per studiare.</p>
							<?php
						}
					}

					//Inoltre non si può inserire un valoreStudiato > valoreDaStudiare
					if ($_POST['valoreStudiato'] > $_POST['valoreDaStudiare'] ) {
						unset($_SESSION['valoreDaStudiare']);
						unset($_SESSION['dataScadenza']);
						unset($_SESSION['nGiorniRipasso']);
						unset($_SESSION['valoreStudiato']);
						?>
						<p style="color: red;">Il valore studiato non può essere maggiore del totale da studiare!</p>
						<?php
					}
				}
				
				//Se tutte le variabili sono state creato (anche quelle = 0), aggiorniamo il DOM
				if (isset($_SESSION['valoreDaStudiare']) &&
				isset($_SESSION['dataScadenza']) &&
				isset($_SESSION['nGiorniRipasso']) &&
				isset($_SESSION['valoreStudiato'])) {
					$newMateria = $doc->createElement("materia");
					$newNomeMateria = $doc->createElement("nomeMateria", $_SESSION['nomeMateria']);
									
					$newOggettoStudio = $doc->createElement("oggettoStudio", $_SESSION['oggettoStudio']);
					$newValoreDaStudiare = $doc->createElement("valoreDaStudiare", $_SESSION['valoreDaStudiare']);
					$newDataScadenza = $doc->createElement("dataScadenza", $_SESSION['dataScadenza']);
					$newNGiorniRipasso = $doc->createElement("nGiorniRipasso", $_SESSION['nGiorniRipasso']);
					$newValoreStudiato = $doc->createElement("valoreStudiato", $_SESSION['valoreStudiato']);
					$newValoreStudiatoOggi = $doc->createElement("valoreStudiatoOggi","0");
					$newDataStudiatoOggi = $doc->createElement("dataStudiatoOggi",date("Y-m-d"));
					
					//Attacchiamo all'elemento principale tutti i suoi figli e i corrispettivi "nipoti"
					$newMateria->appendChild($newNomeMateria);
					$newMateria->appendChild($newValoreDaStudiare);
					$newMateria->appendChild($newOggettoStudio);
					$newMateria->appendChild($newDataScadenza);
					$newMateria->appendChild($newNGiorniRipasso);
					$newMateria->appendChild($newValoreStudiatoOggi);
					$newMateria->appendChild($newDataStudiatoOggi);
					$newMateria->appendChild($newValoreStudiato);
					
					$newMateria->setAttribute("status", $_SESSION['status']);
					
					
					//Inseriamo il nodo che abbiamo creato, prima del primo elemento della lista di elementi dopo root
					$materieElement->insertBefore($newMateria);
					$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
					$doc->save($path); //Sovrascriviamolo

					unset($_SESSION['nomeMateria']);
					unset($_SESSION['status']);
					unset($_SESSION['oggettoStudio']);
					unset($_SESSION['valoreDaStudiare']);
					unset($_SESSION['dataScadenza']);
					unset($_SESSION['nGiorniRipasso']);
					unset($_SESSION['valoreStudiato']);
					header("Location: home-studente.php");
					exit();
				}
			}


			//Se abbiamo premuto back, torna al secondo caso
			if (isset($_POST['back'])) {
				unset($_SESSION['oggettoStudio']);
				header("Location: aggiungi-materia.php");
			}
			?>

			<input type="text" name="valoreDaStudiare" placeholder="Quante/i <?= $_SESSION['oggettoStudio'] ?> devi studiare?" <?php if (isset($_POST['valoreDaStudiare'])){ echo 'value="'.$_POST['valoreDaStudiare'].'"'; } ?> /> <br />				
			<input type="text" name="dataScadenza" placeholder="Inserire la data dell'esame (yyyy-mm-dd)" <?php if (isset($_POST['dataScadenza'])){ echo 'value="'.$_POST['dataScadenza'].'"'; } ?>/> <br />		
			<input type="text" name="nGiorniRipasso" placeholder="Quanti giorni si intende ripetere? (optional)" <?php if (isset($_POST['nGiorniRipasso'])){ echo 'value="'.$_POST['nGiorniRipasso'].'"'; } ?>/> <br />	
			<input type="text" name="valoreStudiato" placeholder="Inserisci quante/i <?= $_SESSION['oggettoStudio'] ?> hai già fatto (optional) " <?php if (isset($_POST['valoreStudiato'])){ echo 'value="'.$_POST['valoreStudiato'].'"'; } ?>/> <br />
			
			<input type="submit" name="back" value="Indietro" />
			<input type="submit" name="submit" value="Continua" />
		</form>
	<?php
	}
	?>
</div>

</body>
</html>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("home-studente.css");
	</style>	
</head>
<body>
<?php

session_start();
if (!isset($_SESSION['accessoPermesso'])) {
    header('Location: login.php');
}

/***Procedura standard per inizializzare il file XML***/
$xmlString = ""; //Inizializziamo la variabile xmlString
foreach (file("xml-schema/studenti.xml") as $node) { //Per ogni riga del file xml...
	$xmlString .= trim($node); //Aggiungi alla stringa $xmlString la riga $node
}
$doc = new DOMDocument(); //Creiamo l'oggetto documento DOM e lo assegnamo a $doc
$doc->loadXML($xmlString); //$doc essendo un oggetto DOMDocument possiede il parser XML (metodo nella classe DOMDocument) che lo facciamo girare sulla stringa $xmlString
$root = $doc->documentElement; //la chiamata a DocumentElement restituisce la radice del documento DOM
$studenti = $root->childNodes; //$studenti contiene i nodi figli di root 
/***/

/*Scorri tutti gli studenti del file XML*/
for ($i=0; $i < $studenti->length; $i++) {
	/*Se l'email derivante dal login coincide con uno studente presente nel file XML, carica tutti i dati relativi*/
	if ($_SESSION['email'] == $studenti->item($i)->firstChild->nextSibling->nextSibling->textContent){
		$studente = $studenti->item($i); //questo sarà uno degli studenti e sarà dotato di altri figli.
		$nome = $studente->firstChild; //ora data contiene il primo sottoelemento (studente) di $studente[$i]
		$nomeText = $nome->textContent;

		$cognome = $nome->nextSibling;
		$cognomeText = $cognome->textContent;

		$email = $cognome->nextSibling;
		$emailText = $email->textContent;

		$materie = $email->nextSibling;
		$materie = $materie->childNodes;
		/*Bisogna creare un array per ogni valore presente in materia, affinchè si possa successivamente
		 *elencare ed aggiornare le materie presenti nella lista. Se si creasse un array per i soli valori
		 *testuali sarebbe impossibile aggiornarli. Ogni valore $k deve appartenere ad una materia
		 *($k=>materia k-esima).
		 */
		$nomeMateriaText = array();
		for ($k=0; $k < $materie->length; $k++) {	
			$materia = $materie->item($k);

			$statusText[$k] = $materia->getAttribute('status');
			$nomeMateria[$k] = $materia->firstChild;
			$nomeMateriaText[$k] = $nomeMateria[$k]->textContent;

			//Solo per la materia planned possiamo inserire tutti i dati inerenti al piano di studi, altrimenti errore
			if ($statusText[$k] == 'planned') {
				$valoreDaStudiare[$k] = $nomeMateria[$k]->nextSibling;
				$valoreDaStudiareText[$k] = $valoreDaStudiare[$k]->textContent;

				$oggettoStudio[$k] = $valoreDaStudiare[$k]->nextSibling;
				$oggettoStudioText[$k] = $oggettoStudio[$k]->textContent;

				$dataScadenza[$k] = $oggettoStudio[$k]->nextSibling;
				$dataScadenzaText[$k] = $dataScadenza[$k]->textContent;

				$nGiorniRipasso[$k] = $dataScadenza[$k]->nextSibling;
				$nGiorniRipassoText[$k] = $nGiorniRipasso[$k]->textContent;			

				$valoreStudiatoOggi[$k] = $nGiorniRipasso[$k]->nextSibling;			
				$valoreStudiatoOggiText[$k] = $valoreStudiatoOggi[$k]->textContent;

				$dataStudiatoOggi[$k] = $valoreStudiatoOggi[$k]->nextSibling;
				$dataStudiatoOggiText[$k] = $dataStudiatoOggi[$k]->textContent;
				
				$valoreStudiato[$k] = $dataStudiatoOggi[$k]->nextSibling;
				$valoreStudiatoText[$k] = $valoreStudiato[$k]->textContent;
			}
		} 

		$riassunti = $email->nextSibling->nextSibling;
		$reputation = $riassunti->nextSibling;
		$reputationText = $reputation->textContent;

		$coins = $reputation->nextSibling;
		$coinsText = $coins->textContent;	
	}
}


?>
	<div id="lateralHomeStudente">
		<div id="logoHomeStudente">
			<a href="#">
				<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logoHome.png" style="width: 100%;"/>
			</a>
		</div>
		<div id="navigation">
			<a href="aggiungi-materia.php"><img src="images/iconAggiungiMateria.png">Nuova materia</a>
			<a href="#"><img src="images/iconArchivioMaterie.png">Archivio materie</a>
			<a href="#"><img src="images/iconRiassuntiCreati.png">Riassunti creati</a>
			<a href="#"><img src="images/iconRiassuntiVisualizzati.png">Riassunti visualizzati</a>
			<a href="#"><img src="images/iconRiassuntiPreferiti.png">Riassunti preferiti</a>
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="cercaRiassunti">		
				<input type="text" name="nome" placeholder=" Cerca riassunti" />
				<input type="image" src="images/iconCercaRiassunti.png" alt="Submit Form" />
			</form>
		</div>
		<div id="navigationUser">
			<div id="user">
				<img src="images/iconUtente.png"><?php echo $nomeText." ".$cognomeText; ?>
			</div>
			<div id="reputation">
				<img src="images/iconReputation.png" title="Reputation"><?php echo $reputationText; ?>
			</div>
			<div id="coins">
				<img src="images/iconCoins.png" title="Coins"><?php echo $coinsText; ?>
			</div>
		</div>
		<!-- Il link del logout si comporta come i precedenti ma si trova in un punto differente quindi bisogna assegnargli
			uno stile interno particolare -->
		<div id="navigation" style="top: 75px; height: 40px;">
			<a href="logout.php"><img src="images/iconLogout.png">Logout</a>
		</div>
	</div>
	<div id="main">
		<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="aggiungiMateria">
		<?php
		if (empty($_SESSION['status'])) {
		?>
			<div>
			Aggiungi una materia
			</div>
			<p>
			Puoi decidere se pianificare una materia sviluppando un <b>piano di studi</b> per arrivare all'esame preparato. 
			<br />
			Oppure puoi organizzarti autonomamente, ma in entrambi casi hai la possibilità di scrivere dei <b>riassunti</b>
			che ti permetteranno di memorizzare in maniera più efficace la materia.
			</p>
			<?php
			if (isset($_POST['submit'])) {	
				if ($_POST['nomeMateria'] && !empty($_POST['status']) ) {
					for ($k=0; $k < $materie->length; $k++) {
						if ( $_POST['nomeMateria'] == $nomeMateriaText[$k] ) {	
							echo '<p style="color: red;">Materia già presente.</p>';
						}
					}
					$_SESSION['nomeMateria']= $_POST['nomeMateria'];
					$_SESSION['status']= $_POST['status'];
				}
				else {
					echo '<p style="color: red;">Compilare tutti i campi.</p>';
				}
			}
			?>

			<input type="text" name="nomeMateria" placeholder=" Nome della materia" />
			<input type="radio" name="status" value="planned" /> <label>Pianifica</label>
			<input type="radio" name="status" value="unplanned" /> <label>Non pianificarla</label>
			<input type="submit" name="submit" value="Continua" />
		<?php 
		}
		else if (!empty($_SESSION['status']) && empty($_SESSION['oggettoStudio'])) {
		?>
			<div>
			Aggiungi una materia
			</div>
			<p>
			Inserisci cosa si vuole monitorare. 
			<br />
			Ad esempio, se si deve studiare un libro di 300 pagine inserire "pagine" nel form sottostante.
			</p>
			<?php
			if (isset($_POST['submit2'])) {	
				if ($_POST['oggettoStudio']) {
					$_SESSION['oggettoStudio']= $_POST['oggettoStudio'];
				}
				else {
					echo '<p style="color: red;">Compilare tutti i campi.</p>';
				}
			}
			?>

			<input type="text" name="oggettoStudio" placeholder="Pagine? Capitoli? Ore?"/> <br />		
			<!--<input type="text" name="valoreDaStudiare" placeholder="Inserire un valore corrispondente a quanto si deve studiare" /> <br />				
			<input type="text" name="dataScadenza" placeholder="Inserire la data dell'esame nel formato yyyy-mm-dd" /> <br />		
			<input type="text" name="nGiorniRipasso" placeholder="Quanti giorni si intende ripetere?" /> <br />	
			<input type="text" name="valoreStudiato" placeholder="Se hai iniziato già a studiare, inserisci " /> <br />
			-->
			<input type="submit" name="submit2" value="Continua" />
		<?php
		}
		else {
			?>
				<div>
				Aggiungi una materia
				</div>
				<p>
				Blablabla
				</p>
				<?php
				if (isset($_POST['submit3'])) {	
					if ($_POST['valoreDaStudiare']) {
						if (is_numeric ($_POST['valoreDaStudiare']) && $_POST['valoreDaStudiare'] > 0 ) {
							$_SESSION['valoreDaStudiare']= $_POST['valoreDaStudiare'];
						}
						else {
							echo '<p style="color: red;">Valore da studiare deve essere un n° maggiore di 0.</p>';
						}
					}
					else {
						echo '<p style="color: red;">Compilare tutti i campi.</p>';
					}
					//https://stackoverflow.com/questions/10120643/php-datetime-createfromformat-functionality/10120725#10120725
					if ($_POST['dataScadenza']) {
						$dateTime = DateTime::createFromFormat('Y-m-d', $_POST['dataScadenza']);
						$errors = DateTime::getLastErrors();
						if (empty($errors['warning_count'])) {
							$_SESSION['dataScadenza'] = $_POST['dataScadenza'];
						}
						else {
							echo '<p style="color: red;">La data non è stata compilata correttamente.</p>';
						}
					}
					else {
						echo '<p style="color: red;">Compilare tutti i campi.</p>';
					}
					//Inserire da qui in poi ngiorniRipasso e valoreStudiato. Poi inserire anche il pulsante per annullare
				}
				?>
	
				<input type="text" name="valoreDaStudiare" placeholder="Quante/i <?php echo $_SESSION['oggettoStudio'] ?> devi studiare?" /> <br />				
				<input type="text" name="dataScadenza" placeholder="Inserire la data dell'esame nel formato yyyy-mm-dd" /> <br />		
				<input type="text" name="nGiorniRipasso" placeholder="Quanti giorni si intende ripetere?" /> <br />	
				<input type="text" name="valoreStudiato" placeholder="Inserisci quante/i <?php echo $_SESSION['oggettoStudio'] ?> hai già fatto " /> <br />
	
				<input type="submit" name="submit3" value="Continua" />
		<?php
		}
		?>
		</form>	
	</div>

</body>
</html>
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
		$nome = $studente->firstChild; 
		$nomeText = $nome->textContent;

		$cognome = $nome->nextSibling;
		$cognomeText = $cognome->textContent;

		$email = $cognome->nextSibling;
		$emailText = $email->textContent;

		//Questa parte è necessaria per aggiornare efficacemente il dom con $materieElement->insertBefore($newNode);
		$materieElement = $email->nextSibling; //Questo rappresenta l'elemento "materie"
		$materie = $materieElement->childNodes;  //Quest'altro invece la lista di materie
		/*Bisogna creare un array per ogni valore presente in materia, affinchè si possa successivamente
		 *elencare ed aggiornare le materie presenti nella lista. Se si creasse un array per i soli valori
		 *testuali sarebbe impossibile aggiornarli. Ogni valore $k deve appartenere ad una materia
		 *($k=>materia k-esima).
		 */
		
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
			<a href="home-studente.php">
				<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logoHome.png" style="width: 100%;"/>
			</a>
		</div>
		<div id="navigation">
			<a href="aggiungi-materia.php"><img src="images/iconAggiungiMateria.png">Nuova materia</a>
			<a href="#"><img src="images/iconRiassuntiCreati.png">Riassunti creati</a>
			<a href="#"><img src="images/iconRiassuntiVisualizzati.png">Riassunti visualizzati</a>
			<a href="#"><img src="images/iconRiassuntiPreferiti.png">Riassunti preferiti</a>
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="get" id="cercaRiassunti">		
				<input type="text" name="tagRicercato" placeholder=" Cerca riassunti" />
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
		<?php 
		/* ABBIAMO PREMUTO "AGGIUNGI MATERIA" */
		if (empty($_SESSION['status'])) {
			?>
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="aggiungiMateria">
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
					$errore = 0; //Se questo flag è 1 allora esiste già una materia con lo stesso nome
					if ($_POST['nomeMateria'] && !empty($_POST['status']) ) {
						for ($k=0; $k < $materie->length; $k++) { //Scorriamo le materie
							//Se esiste una materia con quel nome restituisci errore
							if ( strcasecmp($_POST['nomeMateria'], $nomeMateriaText[$k]) == 0 ) {	
								echo '<p style="color: red;">Materia già presente.</p>';
								$errore = 1;
							}
						}
						//Se la materia è nuova, prosegui e aggiorna.
						if ($errore == 0) {
							$_SESSION['nomeMateria']= $_POST['nomeMateria'];
							$_SESSION['status']= $_POST['status'];

							header("Location: aggiungi-materia.php");
						}
					}
					//Se qualche campo non è stato compilato...
					else {
						echo '<p style="color: red;">Compilare tutti i campi.</p>';
					}
				}
				?>

				<input type="text" name="nomeMateria" placeholder=" Nome della materia" />
				<input type="radio" name="status" value="planned" /> <label>Pianifica</label>
				<input type="radio" name="status" value="unplanned" /> <label>Non pianificarla</label><br />
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
						echo '<p style="color: red;">Compilare tutti i campi.</p>';
					}
				}
				if (isset($_POST['back'])) { //Se abbiamo premuto back, torna al primissimo caso
					unset($_SESSION['status']);

					header("Location: aggiungi-materia.php");
				}
				?>

				<input type="text" name="oggettoStudio" placeholder="Pagine? Capitoli? Paragrafi?"/> <br />	

				<input type="submit" name="back" value="Indietro" />	
				<input type="submit" name="submit" value="Continua" />
			</form>
		<?php
		}

		/* ABBIAMO INSERITO UN OGGETTO DI STUDIO: ULTIMO PASSAGGIO */
		else {
			?>
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="aggiungiMateria">
				<div>
				Aggiungi una materia
				</div>
				<p>
				Ancora un ultimo passaggio e abbiamo finito!
				</p>
				<?php
				if (isset($_POST['submit'])) {	
					if ($_POST['valoreDaStudiare']) { 
						if (is_numeric ($_POST['valoreDaStudiare']) && $_POST['valoreDaStudiare'] > 0 ) {
							$_SESSION['valoreDaStudiare']= $_POST['valoreDaStudiare'];
						}
						else {
							unset($_SESSION['valoreDaStudiare']);
							echo '<p style="color: red;">Valore da studiare deve essere un n° maggiore di 0.</p>';
						}
					}
					if ($_POST['dataScadenza']) {
						//Re-check
						$dateTime = DateTime::createFromFormat('Y-m-d', $_POST['dataScadenza']);
						$errors = DateTime::getLastErrors();
						$now = new DateTime();

						if (empty($errors['warning_count'])) {
							$_SESSION['dataScadenza'] = $_POST['dataScadenza'];
						}
						else {
							unset($_SESSION['dataScadenza']);
							echo '<p style="color: red;">La data non è stata compilata correttamente.</p>';
						}
					}
					if ($_POST['nGiorniRipasso']) {
						if (is_numeric ($_POST['nGiorniRipasso']) && $_POST['nGiorniRipasso'] >= 0) {
							$_SESSION['nGiorniRipasso'] = $_POST['nGiorniRipasso'];
						}
						else {
							unset($_SESSION['nGiorniRipasso']);
							echo '<p style="color: red;">Giorni di ripasso deve essere un n >= 0.</p>';
						}
					}
					else { //Se non è stato inserito nulla, allora il valore è 0
						$_SESSION['nGiorniRipasso'] = "0";
					}
					if ($_POST['valoreStudiato']) {
						if (is_numeric ($_POST['nGiorniRipasso']) && $_POST['valoreStudiato'] >= 0) {
							$_SESSION['valoreStudiato'] = $_POST['valoreStudiato'];
						}
						else {
							unset($_SESSION['valoreStudiato']);
							echo '<p style="color: red;">Valore studiato deve essere un n° maggiore di 0.</p>';
						}
					}
					else { //Se non è stato inserito nulla, allora il valore è 0
						$_SESSION['valoreStudiato'] = "0";
					}

					//Questo if serve per far apparire solo una volta l'errore corrispondente
					//N.B. Per DATE inserendo isset c'è errore.
					if (!isset($_POST['valoreDaStudiare']) || empty($_POST['dataScadenza']) ) { 
						echo '<p style="color: red;">Compilare tutti i campi obbligatori.</p>';
					}
					else { //Se tutte le variabili sono presenti...
						$dataScadenza = $dateTime->format('Y-m-d'); //Trasformiamo dataScadenza nel formato corretto per la funzione

						//Se non ci sono abbastanza giorni disponibili, compresi i giorni di ripasso...
						if (giorniDisponibili($dataScadenza, $_POST['nGiorniRipasso']) <= 0) {
							unset($_SESSION['valoreDaStudiare']);
							unset($_SESSION['dataScadenza']);
							unset($_SESSION['nGiorniRipasso']);
							unset($_SESSION['valoreStudiato']);
							echo '<p style="color: red;">Ops, non hai abbastanza giorni per studiare.</p>';
						}

						//Inoltre non si può inserire un valoreStudiato > valoreDaStudiare
						if ($_POST['valoreStudiato'] > $_POST['valoreDaStudiare'] ) {
							unset($_SESSION['valoreDaStudiare']);
							unset($_SESSION['dataScadenza']);
							unset($_SESSION['nGiorniRipasso']);
							unset($_SESSION['valoreStudiato']);
							echo '<p style="color: red;">Il valore studiato non può essere maggiore del totale da studiare!</p>';
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
						
						/*Attacchiamo all'elemento principale tutti i suoi figli e i corrispettivi "nipoti"*/
						$newMateria->appendChild($newNomeMateria);
						$newMateria->appendChild($newValoreDaStudiare);
						$newMateria->appendChild($newOggettoStudio);
						$newMateria->appendChild($newDataScadenza);
						$newMateria->appendChild($newNGiorniRipasso);
						$newMateria->appendChild($newValoreStudiatoOggi);
						$newMateria->appendChild($newDataStudiatoOggi);
						$newMateria->appendChild($newValoreStudiato);
						
						$newMateria->setAttribute("status", $_SESSION['status']);
						
						
						/*Inseriamo il nodo che abbiamo creato, prima del primo elemento della lista di elementi dopo root*/
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
	
				<input type="text" name="valoreDaStudiare" placeholder="Quante/i <?php echo $_SESSION['oggettoStudio'] ?> devi studiare?" /> <br />				
				<input type="text" name="dataScadenza" placeholder="Inserire la data dell'esame (yyyy-mm-dd)" /> <br />		
				<input type="text" name="nGiorniRipasso" placeholder="Quanti giorni si intende ripetere? (optional)" /> <br />	
				<input type="text" name="valoreStudiato" placeholder="Inserisci quante/i <?php echo $_SESSION['oggettoStudio'] ?> hai già fatto (optional) " /> <br />
				
				<input type="submit" name="back" value="Indietro" />
				<input type="submit" name="submit" value="Continua" />
			</form>
		<?php
		}
		?>
	</div>

</body>
</html>
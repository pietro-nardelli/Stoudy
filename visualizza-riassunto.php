<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("riassunto.css");
	</style>	
</head>
<body>
<?php
				
				/*
				echo nl2br($_POST['testoRiassuntoForm'])."<br />"; //Stampa correttamente con i <br />
				*/

session_start();

if (!isset($_SESSION['accessoPermesso'])) {
    header('Location: login.php');
}
$IDGet = $_GET['IDRiassunto'];
$visualizzato = false;
$preferito = 0;
$riassuntoProprio = false;

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

		$materieElement = $email->nextSibling; //Questo rappresenta l'elemento "materie"
		$materie = $materieElement->childNodes; //Quest'altro invece la lista di materie
		/*Bisogna creare un array per ogni valore presente in materia, affinchè si possa successivamente
		 *elencare ed aggiornare le materie presenti nella lista. Se si creasse un array per i soli valori
		 *testuali sarebbe impossibile aggiornarli. Ogni valore $k deve appartenere ad una materia
		 *($k=>materia k-esima).
		 */
		
		for ($k=0; $k < $materie->length; $k++) {	
			$materia = $materie->item($k); //Materia k-esima appartenente alla lista precedentemente definita

			$statusText[$k] = $materia->getAttribute('status'); //Serve per capire se la materia è planned-unplanned-archived
			//L'unica cosa in comune tra gli status è che possiamo inserire in ogni caso creare l'array nomeMateria
			if ($statusText[$k] == 'unplanned' || $statusText[$k] == 'planned') {
				$nomeMateria[$k] = $materia->firstChild;
				$nomeMateriaText[$k] = $nomeMateria[$k]->textContent;
			}
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

		$riassuntiStudente = $email->nextSibling->nextSibling;

		$riassuntiVisualizzatiElement = $riassuntiStudente->firstChild->nextSibling;
		$riassuntiVisualizzati = $riassuntiVisualizzatiElement->childNodes;
		for ($k=0; $k < $riassuntiVisualizzati->length; $k++) {	
			$riassuntoIDVisualizzato[$k] = $riassuntiVisualizzati->item ($k);
			$riassuntoIDVisualizzatoText[$k] = $riassuntoIDVisualizzato[$k]->textContent;
			if ($IDGet == $riassuntoIDVisualizzatoText[$k]) {
				$visualizzato = true;
			}
		}
		$riassuntiPreferitiElement = $riassuntiVisualizzatiElement->nextSibling;
		$riassuntiPreferiti = $riassuntiPreferitiElement->childNodes;
		for ($k=0; $k < $riassuntiPreferiti->length; $k++) {	
			$riassuntoIDPreferito[$k] = $riassuntiPreferiti->item ($k);
			$riassuntoIDPreferitoText[$k] = $riassuntoIDPreferito[$k]->textContent;
			if ($IDGet == $riassuntoIDPreferitoText[$k]) {
				$indicePreferito = $k;
				$preferito = 1;
			}
		}


		$reputation = $riassuntiStudente->nextSibling;
		$reputationText = $reputation->textContent;

		$coins = $reputation->nextSibling;
		$coinsText = $coins->textContent;	
	}
}

/*Inizializziamo il file riassunti.xml*/
$xmlString3 = ""; 
foreach (file("xml-schema/riassunti.xml") as $node3) { 
	$xmlString3 .= trim($node3); 
}
$doc3 = new DOMDocument(); 
$doc3->loadXML($xmlString3); 
$root3 = $doc3->documentElement; 
$riassunti = $root3->childNodes; 
for ($id=0; $id < $riassunti->length; $id++) { //id è l'id del riassunto che aumenta ad ogni riassunto aggiunto
	$riassunto = $riassunti->item($id); 
	$condivisioneRiassuntoText[$id] = $riassunto->getAttribute('condivisione');
	$IDRiassunto[$id] = $riassunto->firstChild; 
	$IDRiassuntoText[$id] = $IDRiassunto[$id]->textContent;

	$titoloRiassunto[$id] = $IDRiassunto[$id]->nextSibling;
	$titoloRiassuntoText[$id] = $titoloRiassunto[$id]->textContent;

	$emailStudenteRiassunto[$id] = $titoloRiassunto[$id]->nextSibling;
	$emailStudenteRiassuntoText[$id] = $emailStudenteRiassunto[$id]->textContent;

	$dataRiassunto[$id] = $emailStudenteRiassunto[$id]->nextSibling;
	$dataRiassuntoText[$id] = $dataRiassunto[$id]->textContent;

	$orarioRiassunto[$id] = $dataRiassunto[$id]->nextSibling;
	$orarioriassuntoText[$id] = $orarioRiassunto[$id]->textContent;

	$testoRiassunto[$id] = $orarioRiassunto[$id]->nextSibling;
	$testoRiassuntoText[$id] = $testoRiassunto[$id]->textContent;

	$visualizzazioniRiassunto[$id] = $testoRiassunto[$id]->nextSibling;
	$visualizzazioniRiassuntoText[$id] = $visualizzazioniRiassunto[$id]->textContent;

	$tagsRiassuntoElement[$id] = $visualizzazioniRiassunto[$id]->nextSibling;
	$tagsRiassunto[$id] = $tagsRiassuntoElement[$id]->childNodes;
	for ($k=0; $k < $tagsRiassunto[$id]->length; $k++) { 	
		$nomeTagRiassunto = $tagsRiassunto[$id]->item($k);
		$nomeTagRiassuntoText[$k] = $nomeTagRiassunto->textContent;
	}

	$preferitiRiassuntoElement[$id] = $tagsRiassuntoElement[$id]->nextSibling;
	$preferitiRiassunto[$id] = $preferitiRiassuntoElement[$id]->childNodes;
	for ($k=0; $k < $preferitiRiassunto[$id]->length; $k++) {	
		$emailPreferitiRiassunto = $preferitiRiassunto[$id]->item($k);
		$emailPreferitiRiassuntoText[$k] = $emailPreferitiRiassunto->textContent;
	}
}
/***/
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
	<?php if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
		if (strcasecmp($_SESSION['email'], $emailStudenteRiassuntoText[$IDGet]) == 0) {
			$riassuntoProprio = true;
		}
		if (!$riassuntoProprio && (strcasecmp($condivisioneRiassuntoText[$IDGet], "privato") == 0) ) {
			echo "Questo riassunto è privato e non può essere visualizzato.";
			//REDIRECT DA FARE QUI
			exit();
		}
		$nowTime = date("H:i:s"); //Ora odierna
		$nowDate = date("Y-m-d"); //Data odierna
		$nowTimeTotal = strtotime($nowTime." ".$nowDate);
		$timeTotal = strtotime($dataRiassuntoText[$IDGet]." ".$orarioriassuntoText[$IDGet]);
		$diffHours = ($nowTimeTotal - $timeTotal)/3600;

		if (!$riassuntoProprio) {
			if (!$visualizzato) {
				if ($coinsText == 0) {
					if ($diffHours > 24) {
						echo "Questo riassunto non può essere visualizzato. Non hai abbastanza coin.";
						exit();
					}
					else { //Se il riassunto è stato inserito entro 24 ore
						
						$newRiassuntoIDVisualizzato = $doc->createElement("riassuntoIDVisualizzato", $IDGet);
						$riassuntiVisualizzatiElement->insertBefore($newRiassuntoIDVisualizzato);		
						$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
						$doc->save($path);
						
						$visualizzazioniRiassunto[$IDGet]->textContent= $visualizzazioniRiassuntoText[$IDGet]+1;
						$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; 
						$doc3->save($path3);
						header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);
					}
				}
				else { //Se si hanno abbastanza coin per visualizzarlo...
					$newRiassuntoIDVisualizzato = $doc->createElement("riassuntoIDVisualizzato", $IDGet);
					$riassuntiVisualizzatiElement->insertBefore($newRiassuntoIDVisualizzato);		
					$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
					$doc->save($path);

					$visualizzazioniRiassunto[$IDGet]->textContent= $visualizzazioniRiassuntoText[$IDGet]+1;
					$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; 
					$doc3->save($path3);
					header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);

				}
			}
		}

		if (isset($_GET['preferito'])) {
			if ($_GET['preferito'] == 1 && !$preferito) { //Aggiungiamo il riassunto ai preferiti
				$newRiassuntoIDPreferito = $doc->createElement("riassuntoIDPreferito", $IDGet);
				
				$riassuntiPreferitiElement->insertBefore($newRiassuntoIDPreferito);		
							
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
				$doc->save($path);
				header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);
			}
			else if ($_GET['preferito'] == 0 && $preferito) { //Togliamo il riassunto dai preferiti
				$riassuntoPreferito = $studente->getElementsByTagName('riassuntoIDPreferito')->item($indicePreferito);
				$riassuntoPreferito->parentNode->removeChild($riassuntoPreferito); //Serve perchè altrimenti da errore!
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc->save($path); //Sovrascriviamolo 
				header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);
			}
		}
		?>
        <div id="visualizzaRiassunto">
            <div id="nomeMateria">
                <?php echo "<b>".$titoloRiassuntoText[$IDGet]."</b>"; ?>
			</div>
			<div>
				<?php 
				echo "<br />";
				echo nl2br($testoRiassuntoText[$IDGet])."<br /><hr style='width: 95%;'/><hr id='lista' />";
				echo "<b>Autore</b>: ".$emailStudenteRiassuntoText[$IDGet]."<br /><hr id='lista' />";
				echo "<b>Data </b>: ".$dataRiassuntoText[$IDGet]." <b>Ora </b>: ".$orarioriassuntoText[$IDGet]."<br /> <hr id='lista' />";
				echo "<b>Tags</b>: ";
				foreach ($tagsRiassunto[$IDGet] as $key=>$value) { 
					echo $nomeTagRiassuntoText[$key]." | ";
				}
				echo "<br /> <hr id='lista' />";
				echo "<b>Visualizzazioni</b>: ".$visualizzazioniRiassuntoText[$IDGet]." <br />";
				echo "<b>Preferiti</b>: ";
				$numeroPreferiti = 0;
				foreach ($preferitiRiassunto[$IDGet] as $key=>$value) { 
					$numeroPreferiti = $numeroPreferiti++;
				}
				echo $numeroPreferiti." <hr id='lista' />";
				?>
				<div id="opzioniRiassunto">
				<?php
				//Se la variabile preferiti è empty || se è = a 0 allora visualizza questo.
				//Altrimenti visualizza la stellina che diventa da gialla scura a chiara e la scritta toglilo dai preferiti
				//In entrambi i casi bisogna procedere all'eliminazione o all'aggiunta tramite DOM
				if (!$preferito) { 
					?>
					<a id="aggiungiAiPreferiti" href="visualizza-riassunto.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&preferito=".urlencode(1).""; ?>">Aggiungilo ai preferiti</a>
					<?php
				}
				else {
					?>
					<a id="togliloDaiPreferiti" href="visualizza-riassunto.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&preferito=".urlencode(0).""; ?>">Toglilo dai preferiti</a>
					<?php
				}
				?>
					<a id="segnalaRiassunto" href="#">Segnala riassunto </a>
				</div>
				
        </div>
		<?php } 
		else {
			echo "Impossibile visualizzare un riassunto se non viene fornito un ID valido";
		}?>
    </div>
</body>
</html>
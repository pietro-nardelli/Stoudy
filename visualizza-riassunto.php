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
				$preferito = 1; //Se uguale a 1 allora la flag indica che è già tra i preferiti, viceversa non lo è.
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
//Questo ciclo è necessario per assegnare all'IDRIassuntoLista l'ID di ogni riassunto
for ($cRiass=0; $cRiass < $riassunti->length; $cRiass++) {
	$riassunto = $riassunti->item($cRiass); 
	$IDRiassuntoLista[$cRiass] = $riassunto->firstChild->textContent;
}

/*A questo punto possiamo scorrere l'array precedentemente inizializzato tenendo conto che il suo valore $id 
 *è l'indice degli array che andremo ad inizializzare per ogni oggetto nel dom di ogni riassunto.
 *Se non lo facessimo quando andremo cercare per ID per operare su quel determinato oggetto
 *non lo troveremo. 
 */
if ($riassunti->length) { //Altrimenti restituisce errore se non ci sono riassunti nel file xml
	foreach ($IDRiassuntoLista as $count => $id) {
		$riassunto = $riassunti->item($count); 
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
		$orarioRiassuntoText[$id] = $orarioRiassunto[$id]->textContent;

		$descrizioneRiassunto[$id] = $orarioRiassunto[$id]->nextSibling;
		$descrizioneRiassuntoText[$id] = $descrizioneRiassunto[$id]->textContent;

		$linkDocumentoRiassunto[$id] = $descrizioneRiassunto[$id]->nextSibling;
		$linkDocumentoRiassuntoText[$id] = $linkDocumentoRiassunto[$id]->textContent;

		$visualizzazioniRiassunto[$id] = $linkDocumentoRiassunto[$id]->nextSibling;
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
			if (strcasecmp($_SESSION['email'], $emailPreferitiRiassuntoText[$k]) == 0) {
				$indiceEmailPreferito = $k;
			}
		}
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
			<form action="cerca-riassunti.php" method="get" id="cercaRiassunti">		
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
	//Se id è valido e se abbiamo usato il "motore" di ricerca...
	if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
		//Controlliamo se il riassunto è nostro oppure no
		if (strcasecmp($_SESSION['email'], $emailStudenteRiassuntoText[$IDGet]) == 0) {
			$riassuntoProprio = true;
		}
		//Se non è nostro ed è privato
		if (!$riassuntoProprio && (strcasecmp($condivisioneRiassuntoText[$IDGet], "privato") == 0) ) {
			echo "Questo riassunto è privato e non può essere visualizzato.";
			//REDIRECT DA FARE QUI
			exit();
		}
		$nowTime = date("H:i:s"); //Ora odierna
		$nowDate = date("Y-m-d"); //Data odierna
		$nowTimeTotal = strtotime($nowTime." ".$nowDate); //Va fatto per ottenere la differenza di orari
		$timeTotal = strtotime($dataRiassuntoText[$IDGet]." ".$orarioRiassuntoText[$IDGet]); //Idem sopra
		$diffHours = ($nowTimeTotal - $timeTotal)/3600; //Questa è la differenza in ore tra la data del riassunto e la data attuale

		if (!$riassuntoProprio) {
			if (!$visualizzato) {
				if ($coinsText == 0) {
					//Se il riassunto è stato inserito da più di 24 ore, non si hanno coin e non lo abbiamo già visualizzato nè è nostro...
					if ($diffHours > 24) {
						echo "Questo riassunto non può essere visualizzato. Non hai abbastanza coin.";
						exit();
					}
					else { //Se il riassunto è stato inserito entro 24 ore: visualizziamolo e aggiungiamolo ai riassunti visti
						$newRiassuntoIDVisualizzato = $doc->createElement("riassuntoIDVisualizzato", $IDGet);
						$riassuntiVisualizzatiElement->insertBefore($newRiassuntoIDVisualizzato);		
						$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
						$doc->save($path);
						
						$visualizzazioniRiassunto[$IDGet]->textContent= $visualizzazioniRiassuntoText[$IDGet]+1;
						$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; 
						$doc3->save($path3); 
						header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet); //Aggiorniamo per visualizzarlo correttamente con il DOM aggiornato
					}
				}
				else { //Se si hanno abbastanza coin per visualizzarlo, visualizziamolo e aggiungiamolo ai riassunti visti.
					//In seguito dovremo togliere un coin dall'account
					$newRiassuntoIDVisualizzato = $doc->createElement("riassuntoIDVisualizzato", $IDGet);
					$riassuntiVisualizzatiElement->insertBefore($newRiassuntoIDVisualizzato);		
					$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
					$doc->save($path);

					$visualizzazioniRiassunto[$IDGet]->textContent= $visualizzazioniRiassuntoText[$IDGet]+1;
					
					$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; 
					$doc3->save($path3);
					header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet); //Aggiorniamo per visualizzarlo correttamente con il DOM aggiornato

				}
			}
		} //In tutti gli altri casi possiamo visualizzare il riassunto! E' nostro o è stato visualizzato
		
		//Se abbiamo premuto il pulsante preferito
		if (isset($_GET['preferito'])) {
			//Il pulsante rimanda 1 quando non è ancora tra i preferiti
			if ($_GET['preferito'] == 1 && !$preferito) { //Aggiungiamo il riassunto ai preferiti
				$newRiassuntoIDPreferito = $doc->createElement("riassuntoIDPreferito", $IDGet);
				$riassuntiPreferitiElement->insertBefore($newRiassuntoIDPreferito);					
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
				$doc->save($path);

				$newEmailPreferiti = $doc3->createElement("emailPreferiti", $_SESSION['email']);
				$preferitiRiassuntoElement[$IDGet]->insertBefore($newEmailPreferiti);
				$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; 
				$doc3->save($path3);

				header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);
			}
			//Il pulsante rimanda 0 quando lo abbiamo tra i preferiti
			else if ($_GET['preferito'] == 0 && $preferito) { //Togliamo il riassunto dai preferiti
				$riassuntoPreferito = $studente->getElementsByTagName('riassuntoIDPreferito')->item($indicePreferito);
				$riassuntoPreferito->parentNode->removeChild($riassuntoPreferito); //Serve perchè altrimenti da errore!
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc->save($path); //Sovrascriviamolo 

				$emailPreferito = $root3->getElementsByTagName('emailPreferiti')->item($indiceEmailPreferito);
				$emailPreferito->parentNode->removeChild($emailPreferito);
				$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc3->save($path3); //Sovrascriviamolo 

				header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);
			}
		}
		//Da qui in poi visualizziamo il riassunto
		?>
        <div id="visualizzaRiassunto">
            <div id="nomeMateria">
                <?php echo "<b>".$titoloRiassuntoText[$IDGet]."</b>"; ?>
			</div>
			<div>
				<?php 
				echo "<br />";
				echo nl2br($descrizioneRiassuntoText[$IDGet])."<br />";?>
				<br />
				<embed src="<?php echo $linkDocumentoRiassuntoText[$IDGet]; ?>" width="100%" height="500" type='application/pdf'>
				<br /><br />
				<hr style='width: 95%;'/><hr id='lista' />
				<?php
				echo "<b>Autore</b>: ".$emailStudenteRiassuntoText[$IDGet]."<br /><hr id='lista' />";
				echo "<b>Data </b>: ".$dataRiassuntoText[$IDGet]." <b>Ora </b>: ".$orarioRiassuntoText[$IDGet]."<br /> <hr id='lista' />";
				echo "<b>Tags</b>: ";
				foreach ($tagsRiassunto[$IDGet] as $key=>$value) { 
					echo $nomeTagRiassuntoText[$key]." | ";
				}
				echo "<br /> <hr id='lista' />";
				echo "<b>Visualizzazioni</b>: ".$visualizzazioniRiassuntoText[$IDGet]." <br />";
				echo "<b>Preferiti</b>: ";
				$numeroPreferiti = $preferitiRiassunto[$IDGet] ->length;
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
					<a id="segnalaRiassunto" href="segnala-riassunto.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&emailStudente=".urlencode($_SESSION['email']).""; ?>">Segnala riassunto </a>
				</div>
				
        </div>
		<?php } 
		else {
			echo "Impossibile visualizzare un riassunto se non viene fornito un ID valido";
		}?>
    </div>
</body>
</html>
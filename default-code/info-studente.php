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
		foreach ($materie as $k=>$v) {	
			$materia = $materie->item($k); //Materia k-esima appartenente alla lista precedentemente definita

			$statusText[$k] = $materia->getAttribute('status'); //Serve per capire se la materia è planned-unplanned-archived

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

		$riassuntiStudente = $email->nextSibling->nextSibling;

		$riassuntiCreatiElement = $riassuntiStudente->firstChild;
		$riassuntiCreati = $riassuntiCreatiElement->childNodes;
		for ($k=0; $k < $riassuntiCreati->length; $k++) {	
			$riassuntoIDCreato[$k] = $riassuntiCreati->item ($k);
			$riassuntoIDCreatoText[$k] = $riassuntoIDCreato[$k]->textContent;
		}

		$riassuntiVisualizzatiElement = $riassuntiCreatiElement->nextSibling;
		$riassuntiVisualizzati = $riassuntiVisualizzatiElement->childNodes;
		for ($k=0; $k < $riassuntiVisualizzati->length; $k++) {	
			$riassuntoIDVisualizzato[$k] = $riassuntiVisualizzati->item ($k);
			$riassuntoIDVisualizzatoText[$k] = $riassuntoIDVisualizzato[$k]->textContent;
		}
		
		$riassuntiPreferitiElement = $riassuntiVisualizzatiElement->nextSibling;
		$riassuntiPreferiti = $riassuntiPreferitiElement->childNodes;
		for ($k=0; $k < $riassuntiPreferiti->length; $k++) {	
			$riassuntoIDPreferito[$k] = $riassuntiPreferiti->item ($k);
			$riassuntoIDPreferitoText[$k] = $riassuntoIDPreferito[$k]->textContent;
		}

		$reputation = $riassuntiStudente->nextSibling;
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
		<a href="riassunti-creati.php"><img src="images/iconRiassuntiCreati.png">Riassunti creati</a>
		<a href="riassunti-visualizzati.php"><img src="images/iconRiassuntiVisualizzati.png">Riassunti visualizzati</a>
		<a href="riassunti-preferiti.php"><img src="images/iconRiassuntiPreferiti.png">Riassunti preferiti</a>
		<form action="cerca-riassunti.php" method="get" id="cercaRiassunti">		
			<input type="text" name="tagRicercato" placeholder=" Cerca riassunti" />
			<input type="image" src="images/iconCercaRiassunti.png" alt="Submit Form" />
		</form>
	</div>
	<div id="navigationUser">
		<div id="user">
			<img src="images/iconUtente.png"><?= $nomeText." ".$cognomeText; ?>
		</div>
		<div id="reputation">
			<img src="images/iconReputation.png" title="Reputation"><?= $reputationText; ?>
		</div>
		<div id="coins">
			<img src="images/iconCoins.png" title="Coins"><?= $coinsText; ?>
		</div>
	</div>
	<!-- Il link del logout si comporta come i precedenti ma si trova in un punto differente quindi bisogna assegnargli
			uno stile interno particolare -->
	<div id="navigation" style="top: 75px; height: 40px;">
		<a href="logout.php"><img src="images/iconLogout.png">Logout</a>
	</div>
</div>
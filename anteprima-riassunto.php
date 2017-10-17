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
include 'functions/upload.php';

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
    if (isset($_GET['nomeMateria'])) { 
		//Dopo aver caricato il pdf, compilato correttamente il form e controllato i tag che siano corretti, possiamo aggiornare il DOM
		if (isset($_SESSION['anteprimaRiassunto'])) {
			if (!isset($_GET['conferma'])) {
			?>
			<div id="visualizzaRiassunto">
				<div id="nomeMateria">
					<?php echo "<b>".$_SESSION['titoloRiassunto']."</b>"; ?>
				</div>
				<div>
					<?php 
					echo "<br />";
					echo nl2br($_SESSION['descrizioneRiassunto'])."<br />";?>
					<br />
					<embed src="<?php echo $_SESSION['linkDocumentoRiassunto']; ?>" width="100%" height="500" type='application/pdf'>
					<br /><br />
					<hr style='width: 95%;'/>
					<table id="tabellaTagEstratti">
					<th><th colspan="2">Controllo tag</th></th>
					<?php
					/*Carichiamo il file tags.xml per mostrare gli estratti!*/
					$xmlString2 = ""; 
					foreach (file("xml-schema/tags.xml") as $node2) { 
						$xmlString2 .= trim($node2); 
					}
					$doc2 = new DOMDocument();
					$doc2->loadXML($xmlString2); 
					$root2 = $doc2->documentElement; 
					$tags = $root2->childNodes; 
					foreach ($_SESSION['tagsRiassuntoNuovo'] as $l => $value) {
						for ($k=0; $k < $tags->length; $k++) {	
							$tag = $tags->item($k); 
							$nomeTag[$k] = $tag->firstChild; 
							$nomeTagText[$k] = $nomeTag[$k]->textContent;
							$estrattoTag[$k] = $nomeTag[$k]->nextSibling;
							$estrattoTagText[$k] = $estrattoTag[$k]->textContent;
							//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml
							if (strcasecmp ($value, $nomeTagText[$k]) == 0 && !empty($estrattoTagText[$k])) {
								$indiceTrovato[$l] = -1;
								?>
								<?php
								echo "<tr><td><a id='tagAnteprima' href='#'>".$value."</a></td><td >".$estrattoTagText[$k]."</td></tr>";
								break; //Usiamo il break perchè tanto abbiamo trovato ciò che cercavamo nel nostro for annidato
							}
						}
					}
					//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml: caso in cui tag non è presente.
					foreach ($_SESSION['tagsRiassuntoNuovo'] as $p => $value) {
						for ($k=0; $k < $tags->length; $k++) {	
							$estrattoTagText[$k] = $estrattoTag[$k]->textContent;
							//Dobbiamo allora semplicemente creare un tag nuovo
							if (empty($indiceTrovato[$p]) ||  $indiceTrovato[$p] != -1) {
								?>
								<?php
								echo "<tr><td><a id='tagAnteprima' href='#'>".$value."</a></td><td ><i>estratto mancante...</i></td></tr>";
								break; //Usiamo il break perchè tanto abbiamo trovato ciò che cercavamo nel nostro for annidato
							}
						}
					}
					?>
					</table>
				</div>
				<div id="pulsantiAnteprima">
					<a href="anteprima-riassunto.php?nomeMateria=<?php echo $_GET['nomeMateria']."&conferma=0"; ?> " id="tornaAdAggiungiRiassunto">Indietro</a>
					<a href="anteprima-riassunto.php?nomeMateria=<?php echo $_GET['nomeMateria']."&conferma=1"; ?>" id="confermaRiassunto">Conferma riassunto</a>	
				<div>	
			</div>
		<?php
			}
			else if ($_GET['conferma'] == 1) {
				//DA QUI IN AVANTI SI AGGIORNA IL DOM
								
				/* AGGIORNIAMO IL FILE RIASSUNTI.XML */
				$xmlString3 = ""; 
				foreach (file("xml-schema/riassunti.xml") as $node3) { 
					$xmlString3 .= trim($node3); 
				}
				$doc3 = new DOMDocument(); 
				$doc3->loadXML($xmlString3); 
				$root3 = $doc3->documentElement; 
				$riassunti = $root3->childNodes; 
				/*Il contatore non è l'ID del riassunto in quanto un riassunto potrebbe essere cancellato, 
				*e ciò che ne rimane è un posto vuoto!! 
				* Se non facessimo così l'ID non sarebbe univoco.
				*/
				for ($cRiass=0; $cRiass < $riassunti->length; $cRiass++) {
					$riassunto = $riassunti->item($cRiass); 
					$condivisioneRiassuntoText[$cRiass] = $riassunto->getAttribute('condivisione');
					$IDRiassunto[$cRiass] = $riassunto->firstChild; 
					$IDRiassuntoText[$cRiass] = $IDRiassunto[$cRiass]->textContent;

					$titoloRiassunto[$cRiass] = $IDRiassunto[$cRiass]->nextSibling;
					$titoloRiassuntoText[$cRiass] = $titoloRiassunto[$cRiass]->textContent;

					$emailStudenteRiassunto[$cRiass] = $titoloRiassunto[$cRiass]->nextSibling;
					$emailStudenteRiassuntoText[$cRiass] = $emailStudenteRiassunto[$cRiass]->textContent;

					$dataRiassunto[$cRiass] = $emailStudenteRiassunto[$cRiass]->nextSibling;
					$dataRiassuntoText[$cRiass] = $dataRiassunto[$cRiass]->textContent;

					$orarioRiassunto[$cRiass] = $dataRiassunto[$cRiass]->nextSibling;
					$orarioriassuntoText[$cRiass] = $orarioRiassunto[$cRiass]->textContent;

					$descrizioneRiassunto[$cRiass] = $orarioRiassunto[$cRiass]->nextSibling;
					$descrizioneRiassuntoText[$cRiass] = $descrizioneRiassunto[$cRiass]->textContent;

					$linkDocumentoRiassunto[$cRiass] = $descrizioneRiassunto[$cRiass]->nextSibling;
					$linkDocumentoRiassuntoText[$cRiass] = $linkDocumentoRiassunto[$cRiass]->textContent;

					$visualizzazioniRiassunto[$cRiass] = $linkDocumentoRiassunto[$cRiass]->nextSibling;
					$visualizzazioniRiassuntoText[$cRiass] = $visualizzazioniRiassunto[$cRiass]->textContent;

					$tagsRiassuntoElement[$cRiass] = $visualizzazioniRiassunto[$cRiass]->nextSibling;
					$tagsRiassunto[$cRiass] = $tagsRiassuntoElement[$cRiass]->childNodes;
					for ($k=0; $k < $tagsRiassunto[$cRiass]->length; $k++) { 	
						$nomeTagRiassunto = $tagsRiassunto[$cRiass]->item($k);
						$nomeTagRiassuntoText[$k] = $nomeTagRiassunto->textContent;
					}

					$preferitiRiassuntoElement[$cRiass] = $tagsRiassuntoElement[$cRiass]->nextSibling;
					$preferitiRiassunto[$cRiass] = $preferitiRiassuntoElement[$cRiass]->childNodes;
					for ($k=0; $k < $preferitiRiassunto[$cRiass]->length; $k++) {	
						$emailPreferitiRiassunto = $preferitiRiassunto[$cRiass]->item($k);
						$emailPreferitiRiassuntoText[$k] = $emailPreferitiRiassunto->textContent;
					}
				}

				/*Da qui $IDRiassuntoText[ $riassunti->length -1 ] +1 corrisponde all'ID che noi vogliamo inserire...
				*Non c'è bisogno di fare come cerca-riassunti e visualizza-riassunto perchè non dobbiamo operare sugli altri oggetti del DOM ma solo
				*aggiungerne uno in più all'ultimo della fila.
				*/
				$id = $IDRiassuntoText[ $riassunti->length -1 ] +1;

				$newRiassunto = $doc3->createElement("riassunto");
				$newIDRiassunto = $doc3->createElement("ID", $id );		
				$newTitoloRiassunto = $doc3->createElement("titolo", $_SESSION['titoloRiassunto']);
				$newEmailStudenteRiassunto = $doc3->createElement("emailStudente", $_SESSION['email']);
				$newDataRiassunto = $doc3->createElement("data", $_SESSION['nowDate']);
				$newOrarioRiassunto = $doc3->createElement("orario", $_SESSION['nowTime']);
				$newDescrizioneRiassunto = $doc3->createElement("descrizione", $_SESSION['descrizioneRiassunto']);
				$newLinkDocumentoRiassunto = $doc3->createElement("linkDocumento", $_SESSION['linkDocumentoRiassunto']);				
				$newVisualizzazioniRiassunto = $doc3->createElement("visualizzazioni","0");
				$newTagsRiassunto = $doc3->createElement("tags");
				$newPreferitiRiassunto = $doc3->createElement("preferiti");

				$newRiassunto->appendChild($newIDRiassunto);
				$newRiassunto->appendChild($newTitoloRiassunto);
				$newRiassunto->appendChild($newEmailStudenteRiassunto);
				$newRiassunto->appendChild($newDataRiassunto);
				$newRiassunto->appendChild($newOrarioRiassunto);
				$newRiassunto->appendChild($newDescrizioneRiassunto);
				$newRiassunto->appendChild($newLinkDocumentoRiassunto);
				$newRiassunto->appendChild($newVisualizzazioniRiassunto);
				$newRiassunto->appendChild($newTagsRiassunto);
				$newRiassunto->appendChild($newPreferitiRiassunto);
				foreach ($_SESSION['tagsRiassuntoNuovo'] as $l => $value) {
					$newNomeTagRiassunto = $doc3->createElement("nomeTag", $value);
					$newTagsRiassunto->appendChild($newNomeTagRiassunto);
				}
				$newRiassunto->setAttribute("condivisione", $_SESSION['condivisioneRiassunto']);
				
				$root3->appendChild($newRiassunto); //Va fatto con appendChild altrimenti potrebbe creare problemi...
				$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml";
				$doc3->save($path3);
				/***/


				/* AGGIORNIAMO IL FILE RIASSUNTI.XML (solamente riassunti->creati) */ 
				$riassuntiCreati = $riassuntiStudente->firstChild;
				
				$newRiassuntoIDCreato = $doc->createElement("riassuntoIDCreato", $id);
				$riassuntiCreati->appendChild($newRiassuntoIDCreato); //CHECK SE SERVE QUESTO, visto che c'è già insertBefore		
				$newRiassuntoIDCreato->setAttribute("materiaRiassunto", $_GET['nomeMateria']);
				
				$riassuntiCreati->insertBefore($newRiassuntoIDCreato);
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
				$doc->save($path);
				/***/

				/* AGGIORNIAMO IL FILE TAGS.XML */
				$xmlString2 = ""; 
				foreach (file("xml-schema/tags.xml") as $node2) { 
					$xmlString2 .= trim($node2); 
				}
				$doc2 = new DOMDocument();
				$doc2->loadXML($xmlString2); 
				$root2 = $doc2->documentElement; 
				$tags = $root2->childNodes; 
				for ($k=0; $k < $tags->length; $k++) {	
					$tag = $tags->item($k); 
					$nomeTag[$k] = $tag->firstChild; 
					$nomeTagText[$k] = $nomeTag[$k]->textContent;
					$estrattoTag[$k] = $nomeTag[$k]->nextSibling;
					$estrattoTagText[$k] = $estrattoTag[$k]->textContent;
					$riassuntoID[$k] = $estrattoTag[$k] ->nextSibling;
					
					
					/*$l è l'indice del tag, tra quelli inseriti. 
					*Se l'array indiceTrovato è pari a -1 => quel tag è già presente nel file.
					*Altrimenti => quel tag non è presente nel file e va aggiunto da zero.
					*/

					//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml: caso in cui tag già presente.
					foreach ($_SESSION['tagsRiassuntoNuovo'] as $l => $value) {
						//Dobbiamo allora semplicemente associare un riassuntoID al tag($k) corrispondente.
						if (strcasecmp ($value, $nomeTagText[$k]) == 0) {
							$indiceTrovato[$l] = -1;
							$newRiassuntoID = $doc2->createElement("riassuntoID", $id);
							$tag->appendChild($newRiassuntoID);	
						}
					}	
				}
				//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml: caso in cui tag non è presente.
				foreach ($_SESSION['tagsRiassuntoNuovo'] as $p => $value) {
					//Dobbiamo allora semplicemente creare un tag nuovo
					if (empty($indiceTrovato[$p]) ||  $indiceTrovato[$p] != -1) {
						$newTag = $doc2->createElement("tag");
						$newNome = $doc2->createElement("nome", $value);
						$newEstratto = $doc2->createElement("estratto", "");
						$newRiassuntoID = $doc2->createElement("riassuntoID", $id);
						
						$newTag->appendChild($newNome);
						$newTag->appendChild($newEstratto);
						$newTag->appendChild($newRiassuntoID);	
	
						$root2->appendChild($newTag);
					}
				}

				$path2 = dirname(__FILE__)."/xml-schema/tags.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc2->save($path2); //Sovrascriviamolo
				/***/

				
				unset($_SESSION['nowDate']);
				unset($_SESSION['nowTime']);
				unset($_SESSION['titoloRiassunto']);
				unset($_SESSION['descrizioneRiassunto']);
				unset($_SESSION['linkDocumentoRiassunto']);
				unset($_SESSION['condivisioneRiassunto']);
				unset($_SESSION['tagsRiassuntoNuovo']); //Questa è una variabile session che gestisce l'array dei tags
				unset($_SESSION['aggiungiRiassunto']); //Bisogna fare unset dopo aver aggiornato il DOM
				unset($_SESSION['aggiungiRiassunto']);
				unset($_SESSION['anteprimaRiassunto']);
				header('Location: visualizza-riassunto.php?IDRiassunto='.$id);
			}
			else { //Se conferma c'è ma è diversa da 1 (ovvero 0)
				unlink ($_SESSION['linkDocumentoRiassunto']);
				header("Location: aggiungi-riassunto.php?nomeMateria=".$_GET['nomeMateria']."");
			}
		} 
		else { //Se non c'è la sessione, c'è stato un errore e non potevamo trovarci in questa pagina...
			header("Location: aggiungi-riassunto.php?nomeMateria=".$_GET['nomeMateria']."");
		}
    }
    else { //Se non c'è il get con nomeMateria...
        echo "Impossibile aggiungere riassunto senza una materia!";
	}
?>
<?php
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

 //Quindi $id; $IDRiassuntoText[$id]; sono la stessa cosa!
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
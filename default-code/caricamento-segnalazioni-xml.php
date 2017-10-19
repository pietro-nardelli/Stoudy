<?php
/*Inizializziamo il file segnalazioni.xml*/
$xmlString4 = ""; 
foreach (file("xml-schema/segnalazioni.xml") as $node4) { 
	$xmlString4 .= trim($node4); 
}
$doc4 = new DOMDocument(); 
$doc4->loadXML($xmlString4); 
$root4 = $doc4->documentElement; 
$segnalazioni = $root4->childNodes;
for ($i=0; $i < $segnalazioni->length; $i++) {
    $segnalazione = $segnalazioni->item($i);
    $riassuntoID[$i] = $segnalazione->firstChild; 
    $riassuntoIDText[$i] = $riassuntoID[$i]->textContent;
    $emailAdmin[$i] = $riassuntoID[$i]->nextSibling;
    $emailAdminText[$i] = $emailAdmin[$i]->textContent;
    //Contiene la lista delle segnalazioni, all'indice riassuntoID
    $emailStudenteLista[$riassuntoIDText[$i]] = $segnalazione->getElementsByTagName('emailStudente'); 
}
?>
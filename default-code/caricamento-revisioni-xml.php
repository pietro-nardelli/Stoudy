<?php
//Inizializziamo il file revisioni.xml
$xmlString5 = ""; 
foreach (file("xml-schema/revisioni.xml") as $node5) { 
	$xmlString5 .= trim($node5); 
}
$doc5 = new DOMDocument(); 
$doc5->loadXML($xmlString5); 
$root5 = $doc5->documentElement; 
$revisioni = $root5->childNodes;
for ($i=0; $i < $revisioni->length; $i++) {
    $revisione = $revisioni->item ($i);
    $nomeTagRevisione[$i] = $revisione->firstChild; 
    $nomeTagRevisioneText[$i] = $nomeTagRevisione[$i]->textContent;
    
    $emailAdminRevisione[$i] = $nomeTagRevisione[$i]->nextSibling;
    $emailAdminRevisioneText[$i] = $emailAdminRevisione[$i]->textContent;
    
    $modificaDescrizione[$i] = $emailAdminRevisione[$i]->nextSibling;
    $modificaDescrizioneText[$i] = $modificaDescrizione[$i]->textContent;

    $emailStudenteRevisione[$i] = $modificaDescrizione[$i]->nextSibling;
    $emailStudenteRevisioneText[$i] = $emailStudenteRevisione[$i]->textContent;
}
/***/
?>
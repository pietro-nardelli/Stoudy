<?php
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
		$descrizioneTag[$k] = $nomeTag[$k]->nextSibling;
		$descrizioneTagText[$k] = $descrizioneTag[$k]->textContent;
		//$riassuntoIDLista = $tag->getElementsByTagName('riassuntoID');
    }
?>
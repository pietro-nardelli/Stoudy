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
$trovatoEsatto = -1;
$editImpossibile = false;
$trovato = false;

include('default-code/info-studente.php');
include('default-code/caricamento-riassunti-xml.php');

//Inizializziamo il file tags.xml
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
	//Controlla se c'è una sottostringa nel nomeTagText[$k]
	if (!empty ($_GET['tagRicercato'])) {
		if (stripos($nomeTagText[$k], $_GET['tagRicercato']) !== false) {
			if (strcasecmp($nomeTagText[$k], $_GET['tagRicercato']) == 0) { //Controlliamo se il tag cercato è ESATTAMENTE un tag
				$trovatoEsatto = $k; //Associamo alla flag l'indice del tag presente in lista così lo usiamo dopo per mostrare l'estratto
			}
			$riassuntoIDTrovatoLista = $tag->getElementsByTagName('riassuntoID');
			//Potrebbe darsi che un tag sia rimasto senza riassunti per via di eliminazioni, in questo caso rimanda false in trovato
			if ($riassuntoIDTrovatoLista->length == 0) {
				$trovato = false;
			}

			foreach ($riassuntoIDTrovatoLista as $key => $value) { //Inseriamo nell'array riassuntoIDTrovato ognuno degli ID del tag ricercato
				//Se il riassunto è public lo aggiungiamo, ovvero basta un solo riassunto public nel tag per averlo trovato.
				if (strcasecmp($condivisioneRiassuntoText[$riassuntoIDTrovatoLista->item($key)->textContent], "privato") != 0) {
					$riassuntoIDTrovato[] = $riassuntoIDTrovatoLista->item($key)->textContent;
					$trovato = true;
				}
			}
		}
	}
}	
/////////

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
    
    $modificaEstratto[$i] = $nomeTagRevisione[$i]->nextSibling;
    $modificaEstrattoText[$i] = $modificaEstratto[$i]->textContent;


    $emailAdmin[$i] = $modificaEstratto[$i]->nextSibling;
    $emailAdminText[$i] = $emailAdmin[$i]->textContent;

    $emailStudente[$i] = $emailAdmin[$i]->nextSibling;
    $emailStudenteText[$i] = $emailStudente[$i]->textContent;
    //Se il tag è già presente nelle revisioni allora non può essere revisionato nuovamente
    if ( strcasecmp ($_GET['tagRicercato'], $nomeTagRevisioneText[$i]) == 0 ) {
        $editImpossibile = true;
    }
}
/***/


?>
<div id="main">
	<?php 
	if ($trovato) {
		foreach ($riassuntoIDTrovato as $key=>$valueID) {
			$valueIDArray [] = $valueID;
			$riassuntoTrovatoTitolo[] = $titoloRiassuntoText[$valueID];
			$riassuntoTrovatoEmail[] = $emailStudenteRiassuntoText[$valueID];
			$riassuntoTrovatoData [] = $dataRiassuntoText[$valueID];
			$riassuntoTrovatoOrario [] = $orarioRiassuntoText[$valueID];
			$riassuntoTrovatoVisualizzazioni []= $visualizzazioniRiassuntoText[$valueID];
			$riassuntoTrovatoPreferiti [] =  $preferitiRiassunto[$valueID]->length;
		}		
		?>
		<div id="riassuntoTrovato">
			<?php 
			//Caso in cui il testo cercato è un tag ESATTO: mostriamo l'estratto (all'indice $trovatoEsatto) e il relativo pulsante
			if ($trovatoEsatto != -1) { ?>
				<div id="risultatoRicercaAlto">
					Risultati per aver cercato <a id="tagRiassuntoTrovato" href="#"><?php echo $_GET['tagRicercato'];?></a>
				</div>
				<div id="estratto">
					<?php echo $estrattoTagText[$trovatoEsatto];
					if (!$editImpossibile) {
						echo " <a href='modifica-estratto.php?tagRicercato=".urlencode($_GET['tagRicercato'])."'>modifica estratto</a>";
					}
					?>
				</div>
				<hr />
				<?php
			}
			//Caso in cui il testo cercato non è un tag esatto
			else { ?>
				<div id="risultatoRicercaAlto">Risultati per per aver cercato "<b><?php echo $_GET['tagRicercato'];?></b>" </div>
				<hr />
				<?php
			}
			
			include("default-code/gestione-pagine-riassunto.php");
			?>
		</div>
		<?php
	}
	else {
		echo "Tag non trovato..";
	}

	?>
</div>
</body>
</html>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("home-studente.css");
	</style>	
</head>
<body>
	<div id="topperHomeStudente">
		<div id="logoHomeStudente">
			<a href="#">
				<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logoHome.png" style="width: 100%;"/>
			</a>
		</div>
	</div>
	<div id="main">
		
	</div>
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
$elementi = $root->childNodes; //$elementi contiene i nodi figli di root 
/***/

/*Scorri tutti gli elementi del file XML*/
for ($i=0; $i < $elementi->length; $i++) {
	/*Se l'email derivante dal login coincide con un elemento presente nel file XML, carica tutti i dati relativi*/
	if ($_SESSION['email'] == $elementi->item($i)->firstChild->nextSibling->nextSibling->textContent){
		$elemento = $elementi->item($i); //questo sarà uno degli elementi e sarà dotato di altri figli.
		$nome = $elemento->firstChild; //ora data contiene il primo sottoelemento di $elemento[$i], ovvero data
		$nomeText = $nome->textContent;

		$cognome = $nome->nextSibling;
		$cognomeText = $cognome->textContent;

		$email = $cognome->nextSibling;
		$emailText = $email->textContent;

		$materie = $email->nextSibling;

		if ($materie->hasChildNodes()) { //Solo se ci sono materie bisogna inizializzarle!
			$materia = $materie->firstChild;
			$nomeMateria = $materia->firstChild;
			$nomeMateriaText = $nomeMateria->textContent;
				
			$valoreStudio = $nomeMateria->nextSibling;
			$valoreStudioText = $valoreStudio->textContent;

			$oggettoStudio = $valoreStudio->nextSibling;
			$oggettoStudioText = $oggettoStudio->textContent;

			$dataScadenza = $oggettoStudio->nextSibling;
			$dataScadenzaText = $dataScadenza->textContent;

			$nGiorniRipasso = $dataScadenza->nextSibling;
			$nGiorniRipassoText = $nGiorniRipasso->textContent;

			$valoreStudiato = $nGiorniRipasso->nextSibling;
			$valoreStudiatoText = $valoreStudiato->textContent;

			$status = $materia->getAttribute;
			$statusText = $status->textContent;
		} 

		$riassunti = $materie->nextSibling;
		$reputation = $riassunti->nextSibling;
		$reputationText = $reputation->textContent;

		$coins = $reputation->nextSibling;
		$coinsText = $coins->textContent;	
	}
}


?>
</body>
</html>


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
include("default-code/caricamento-tags-xml.php");
include("default-code/caricamento-revisioni-xml.php");
for ($k=0; $k < $tags->length; $k++) {	
	$tag = $tags->item($k); 
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

for ($i=0; $i < $revisioni->length; $i++) {
    //Se il tag è già presente nelle revisioni allora non può essere revisionato nuovamente
    if ( strcasecmp ($_GET['tagRicercato'], $nomeTagRevisioneText[$i]) == 0 ) {
        $editImpossibile = true;
    }
}


?>
<div id="main">
	<?php 
	if ($trovato) {
		//Procedura per associare all'array di riassunti non doppioni: scorrendo tutto l'array, se non c'è un doppione si aggiunge
		/*Ad esempio: riassunto 1 ha tag1; riassunto 2 ha tag1 e tag2. Cercando "tag", se non controllassimo i doppioni, 
		 *restituirebbe una lista con: riassunto 1, riassunto 2, riassunto 2: errata.
		 */
		foreach ($riassuntoIDTrovato as $key=>$valueID) {
			$doppione = false; 
			for ($i = $key+1; $i < sizeof($riassuntoIDTrovato); $i++) {
				if ($valueID == $riassuntoIDTrovato[$i]) {
					$doppione = true;
				}
			}
			if (!$doppione) {
				$valueIDArray [] = $valueID;
				$riassuntoTrovatoTitolo[] = $titoloRiassuntoText[$valueID];
				$riassuntoTrovatoEmail[] = $emailStudenteRiassuntoText[$valueID];
				$riassuntoTrovatoData [] = $dataRiassuntoText[$valueID];
				$riassuntoTrovatoOrario [] = $orarioRiassuntoText[$valueID];
				$riassuntoTrovatoVisualizzazioni []= $visualizzazioniRiassuntoText[$valueID];
				$riassuntoTrovatoPreferiti [] =  $preferitiRiassunto[$valueID]->length;
				$doppione = false;
			}
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
		echo "Nessun riassunto trovato con il tag inserito..";
	}

	?>
</div>
</body>
</html>
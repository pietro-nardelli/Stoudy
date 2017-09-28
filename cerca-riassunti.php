<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("riassunto.css");
	</style>	
</head>
<body>
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

/*Inizializziamo il file riassunti.xml*/
$xmlString3 = ""; 
foreach (file("xml-schema/riassunti.xml") as $node3) { 
	$xmlString3 .= trim($node3); 
}
$doc3 = new DOMDocument(); 
$doc3->loadXML($xmlString3); 
$root3 = $doc3->documentElement; 
$riassunti = $root3->childNodes; 
for ($id=0; $id < $riassunti->length; $id++) { //id è l'id del riassunto che aumenta ad ogni riassunto aggiunto
	$riassunto = $riassunti->item($id); 
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

	$testoRiassunto[$id] = $orarioRiassunto[$id]->nextSibling;
	$testoRiassuntoText[$id] = $testoRiassunto[$id]->textContent;

	$visualizzazioniRiassunto[$id] = $testoRiassunto[$id]->nextSibling;
	$visualizzazioniRiassuntoText[$id] = $visualizzazioniRiassunto[$id]->textContent;

	$tagsRiassuntoElement[$id] = $visualizzazioniRiassunto[$id]->nextSibling;
	$tagsRiassunto[$id] = $tagsRiassuntoElement[$id]->childNodes;

	$preferitiRiassuntoElement[$id] = $tagsRiassuntoElement[$id]->nextSibling;
	$preferitiRiassunto[$id] = $preferitiRiassuntoElement[$id]->childNodes;
}
/***/

/* Inizializziamo il file TAGS.XML */
$xmlString2 = ""; 
foreach (file("xml-schema/tags.xml") as $node2) { 
	$xmlString2 .= trim($node2); 
}
$doc2 = new DOMDocument();
$doc2->loadXML($xmlString2); 
$root2 = $doc2->documentElement; 
$tags = $root2->childNodes; 
$trovato = false;
	for ($k=0; $k < $tags->length; $k++) {	
    	$tag = $tags->item($k); 
		$nomeTag[$k] = $tag->firstChild; 
		$nomeTagText[$k] = $nomeTag[$k]->textContent;
        //Controlla se c'è una sottostringa nel nomeTagText[$k]
        if (!empty ($_GET['tagRicercato'])) {
            if (stripos($nomeTagText[$k], $_GET['tagRicercato']) !== false) {
                $trovato = true;
                $riassuntoIDTrovatoLista = $tag->getElementsByTagName('riassuntoID');
                foreach ($riassuntoIDTrovatoLista as $key => $value) { //Inseriamo nell'array riassuntoIDTrovato ognuno degli ID del tag ricercato
                    $riassuntoIDTrovato[] = $riassuntoIDTrovatoLista->item($key)->textContent;
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
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="get" id="cercaRiassunti">		
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
        if ($trovato) {
            foreach ($riassuntoIDTrovato as $key=>$valueID) {
				if (strcasecmp($condivisioneRiassuntoText[$valueID], "pubblico") == 0) {
					$valueIDArray [] = $valueID;
					$riassuntoTrovatoTitolo[] = $titoloRiassuntoText[$valueID];
					$riassuntoTrovatoEmail[] = $emailStudenteRiassuntoText[$valueID];
					$riassuntoTrovatoData [] = $dataRiassuntoText[$valueID];
					$riassuntoTrovatoOrario [] = $orarioRiassuntoText[$valueID];
					$riassuntoTrovatoVisualizzazioni []= $visualizzazioniRiassuntoText[$valueID];
					$riassuntoTrovatoPreferiti [] =  $preferitiRiassunto[$valueID]->length;
				}
			}		
			?>
			<div id="riassuntoTrovato">
				<div id="risultatoRicercaAlto">Risultati per per aver cercato il tag: <b><?php echo $_GET['tagRicercato'];?></b> </div><hr />
				<?php
				for ($key = 0; $key < sizeof($valueIDArray) ; $key++) { 
					$valueID = $valueIDArray[$key];
					echo "<a id ='titoloRiassuntoTrovato' href='visualizza-riassunto.php?IDRiassunto=".urlencode($valueID)."' >".$riassuntoTrovatoTitolo[$key]."</a>";
					echo "<span id ='visualizzazioniPreferitiRiassuntoTrovato'>".$riassuntoTrovatoVisualizzazioni[$key]." <img src='images/iconViews.png' /> ".$riassuntoTrovatoPreferiti[$key]." <img src='images/iconFavorites.png' /></span>";
					echo "<br /><span id='emailRiassuntoTrovato'><i> Creato da ".$riassuntoTrovatoEmail[$key]." il ".$riassuntoTrovatoData[$key]." alle ore ".$riassuntoTrovatoOrario[$key]."</i></span>";
					foreach ($tagsRiassunto[$valueID] as $j => $value) {
						$nomeTagRiassunto = $tagsRiassunto[$valueID]->item($j);
						$nomeTagRiassuntoText[$j] = $nomeTagRiassunto->textContent;
						echo $nomeTagRiassuntoText[$j]."<br />";
					}
					echo "<hr />";
				}
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
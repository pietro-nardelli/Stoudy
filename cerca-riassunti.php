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
			$materia = $materie->item($k); 

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

		$preferitiRiassuntoElement[$id] = $tagsRiassuntoElement[$id]->nextSibling;
		$preferitiRiassunto[$id] = $preferitiRiassuntoElement[$id]->childNodes;
	}
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
/***/

/*Inizializziamo il file revisioni.xml*/
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
				<div id="risultatoRicercaAlto">Risultati per per aver cercato "<b><?php echo $_GET['tagRicercato'];?></b>" </div><hr />
				<?php
			}
				$pageLength = 2;
				if (isset($_GET['next'])) {
					$first = $_GET['next'];
				}
				else {
					$first = 0;
				}
				if (sizeof($valueIDArray) - $first < $pageLength ) {
					$last = sizeof($valueIDArray);
				}
				else {
					$last = $first+$pageLength;
				}
				for ($key = $first; $key < $last ; $key++) { 
					$valueID = $valueIDArray[$key];
					echo "<a id ='titoloRiassuntoTrovato' href='visualizza-riassunto.php?IDRiassunto=".urlencode($valueID)."' >".$riassuntoTrovatoTitolo[$key]."</a>";
					echo "<span id ='visualizzazioniPreferitiRiassuntoTrovato'>".$riassuntoTrovatoVisualizzazioni[$key]." <img src='images/iconViews.png' /> ".$riassuntoTrovatoPreferiti[$key]." <img src='images/iconFavorites.png' /></span>";
					echo "<br /><span id='emailRiassuntoTrovato'><i> Creato da ".$riassuntoTrovatoEmail[$key]." il ".$riassuntoTrovatoData[$key]." alle ore ".$riassuntoTrovatoOrario[$key]."</i></span>";
					foreach ($tagsRiassunto[$valueID] as $j => $value) {
						$nomeTagRiassunto = $tagsRiassunto[$valueID]->item($j);
						$nomeTagRiassuntoText[$j] = $nomeTagRiassunto->textContent;
						echo "<a id='tagRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($nomeTagRiassuntoText[$j])."'>".$nomeTagRiassuntoText[$j]."</a>";
					}
					echo "<hr />";
				}
				$totPagine =  round ( (sizeof($valueIDArray) / $pageLength));
				$paginaAttuale = ($first / $pageLength)+1;

				?>
					<div id="pagineRiassuntoTrovato">
					<?php
					if ($paginaAttuale == 1 && $paginaAttuale == $totPagine) {
						echo "pagina ".$paginaAttuale." / ".$totPagine;		
					}
					else if ($paginaAttuale == 1) {
						echo "pagina ".$paginaAttuale." / ".$totPagine;												
						echo "<a id ='pagineNextRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".$last."' >successivo</a>";														

					}
					else if ($paginaAttuale < $totPagine) {
						echo "<a id ='paginePrevRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".($last-$pageLength-1)."' >precedente</a>";
						echo "pagina ".$paginaAttuale." / ".$totPagine;												
						echo "<a id ='pagineNextRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".$last."' >successivo</a>";														
					}
					else {
						echo "<a id ='paginePrevRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".($last-$pageLength-1)."' >precedente</a>";						
						echo "pagina ".$paginaAttuale." / ".$totPagine;	
					}
					?>
					</div>
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
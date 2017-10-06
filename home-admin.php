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
if (!isset($_SESSION['accessoPermessoAdmin'])) {
    header('Location: login.php');
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

/*Inizializziamo il file segnalazioni.xml*/
$xmlString4 = ""; 
foreach (file("xml-schema/segnalazioni.xml") as $node4) { 
	$xmlString4 .= trim($node4); 
}
$doc4 = new DOMDocument(); 
$doc4->loadXML($xmlString4); 
$root4 = $doc4->documentElement; 
$segnalazioni = $root4->childNodes;

$trovato = false; //Neanche un riassunto trovato

for ($i=0; $i < $segnalazioni->length; $i++) {
    $segnalazione = $segnalazioni->item($i);
    $riassuntoID[$i] = $segnalazione->firstChild; 
    $riassuntoIDText[$i] = $riassuntoID[$i]->textContent;

    $emailAdmin[$i] = $riassuntoID[$i]->nextSibling;
    $emailAdminText[$i] = $emailAdmin[$i]->textContent;
    if (strcasecmp ($emailAdminText[$i], $_SESSION['email']) == 0 ) {
        $trovato = true; //Almeno un riassunto trovato
        $riassuntoIDSegnalati [] = $riassuntoIDText[$i];
    }
    $emailStudenteLista[$i] = $segnalazione->getElementsByTagName('emailStudente');
    /*foreach ($emailStudenteLista[$i] as $key => $value) { 
    }*/
}


?>

	<div id="lateralHomeStudente">
		<div id="logoHomeStudente">
			<a href="home-studente.php">
				<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logoHome.png" style="width: 100%;"/>
			</a>
		</div>
		<!-- Il link del logout si comporta come i precedenti ma si trova in un punto differente quindi bisogna assegnargli
			 uno stile interno particolare -->
		<div id="navigation" style="top: 90%; height: 40px;">
			<a href="logout.php"><img src="images/iconLogout.png">Logout</a>
		</div>
	</div>
    <div id="main">
        <?php 
        if ($trovato) {
            foreach ($riassuntoIDSegnalati as $key=>$valueID) {
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
				<div id="risultatoRicercaAlto"><b><?php echo $_SESSION['email']; ?>: </b>dovresti analizzare <b><?php echo sizeof($riassuntoIDSegnalati);?> riassunti</b> </div><hr />
				<?php
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
						echo "<a id ='pagineNextRiassuntoTrovato' href='home-admin.php?next=".$last."' >successivo</a>";														

					}
					else if ($paginaAttuale < $totPagine) {
						echo "<a id ='paginePrevRiassuntoTrovato' href='home-admin.php?next=".($last-$pageLength-1)."' >precedente</a>";
						echo "pagina ".$paginaAttuale." / ".$totPagine;												
						echo "<a id ='pagineNextRiassuntoTrovato' href='home-admin.php?next=".$last."' >successivo</a>";														
					}
					else {
						echo "<a id ='paginePrevRiassuntoTrovato' href='home-admin.php?next=".($last-$pageLength-1)."' >precedente</a>";						
						echo "pagina ".$paginaAttuale." / ".$totPagine;	
					}
					?>
					</div>
				</div>
				<?php
        }
        else {?>
        	<div id="riassuntoTrovato">
                <div id="risultatoRicercaAlto"><b><?php echo $_SESSION['email']; ?>: </b> non ci sono riassunti da analizzare!</div>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
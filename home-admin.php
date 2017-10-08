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

    /*Procedura di eliminazione dalla lista dei riassunti segnalati, se un riassunto non è più presente in riassunti.xml*/
    if (isset($_SESSION['eliminato'])) {//Se è stato emesso un cookie con valueID
        if ($_SESSION['eliminato'] == $riassuntoIDText[$i]) { 
            //Eliminiamo la segnalazione corrispondente che possiede un ID non più valido
            $segnalazione->parentNode->removeChild($segnalazione);
            $path4 = dirname(__FILE__)."/xml-schema/segnalazioni.xml"; 
            $doc4->save($path4);
            unset($_SESSION['eliminato']);
            header('Location: home-admin.php');
            exit();
        }
    }

    $emailAdmin[$i] = $riassuntoID[$i]->nextSibling;
    $emailAdminText[$i] = $emailAdmin[$i]->textContent;
    //Se l'admin è stato assegnato ad una segnalazione...
    if (strcasecmp ($emailAdminText[$i], $_SESSION['email']) == 0 ) {
        $trovato = true; //Almeno un riassunto trovato
        $riassuntoIDSegnalato [] = $riassuntoIDText[$i]; //Inseriamo nell'array  l'ID dei riassunti ad esso assegnati
    }
    //Contiene la lista delle segnalazioni, all'indice riassuntoID
    $emailStudenteLista[$riassuntoIDText[$i]] = $segnalazione->getElementsByTagName('emailStudente'); 
}
?>

	<div id="lateralHomeStudente">
		<div id="logoHomeStudente">
			<a href="home-admin.php">
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
        if ($trovato) { //Se abbiamo trovato almeno un riassunto in quelli segnalati...
            foreach ($riassuntoIDSegnalato as $key=>$valueID) {
                if (isset ($titoloRiassuntoText[$valueID])) { //Se il riassunto segnalato non è stato cancellato...
                    $valueIDArray [] = $valueID;
                    //Trovato = Segnalato
                    $riassuntoTrovatoTitolo[] = $titoloRiassuntoText[$valueID];
                    $riassuntoTrovatoEmail[] = $emailStudenteRiassuntoText[$valueID];
                    $riassuntoTrovatoData [] = $dataRiassuntoText[$valueID];
                    $riassuntoTrovatoOrario [] = $orarioRiassuntoText[$valueID];
                    $riassuntoTrovatoVisualizzazioni []= $visualizzazioniRiassuntoText[$valueID];
                    $riassuntoTrovatoPreferiti [] =  $preferitiRiassunto[$valueID]->length;
                }
                else { //Se invece è stato cancellato bisogna eliminarlo da segnalazioni.xml
                    $_SESSION['eliminato'] = $valueID; //Emettiamo un cookie con valueID
                    header('Location: home-admin.php');
                    exit();
                }
			}		
			?>
			<div id="riassuntoTrovato">
				<div id="risultatoRicercaAlto"><b><?php echo $_SESSION['email']; ?>: </b>dovresti analizzare <b><?php echo sizeof($valueIDArray);?> riassunti</b> </div><hr />
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
					echo "<a id ='titoloRiassuntoTrovato' href='visualizza-riassunto-admin.php?IDRiassunto=".urlencode($valueID)."' >".$riassuntoTrovatoTitolo[$key]."</a>";
                    echo "<span id ='visualizzazioniPreferitiRiassuntoTrovato'>".$riassuntoTrovatoVisualizzazioni[$key]." <img src='images/iconViews.png' /> ".$riassuntoTrovatoPreferiti[$key]." <img src='images/iconFavorites.png' /></span>";
					echo "<br /><span id='emailRiassuntoTrovato'><i> Creato da ".$riassuntoTrovatoEmail[$key]." il ".$riassuntoTrovatoData[$key]." alle ore ".$riassuntoTrovatoOrario[$key]."</i></span>";
					foreach ($tagsRiassunto[$valueID] as $j => $value) {
						$nomeTagRiassunto = $tagsRiassunto[$valueID]->item($j);
						$nomeTagRiassuntoText[$j] = $nomeTagRiassunto->textContent;
						echo "<a id='tagRiassuntoTrovato' href='#'>".$nomeTagRiassuntoText[$j]."</a>";
                    }
                    echo  "(".$emailStudenteLista[$valueID]->length." segnalazioni)<br/>";
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
        else { //Se non è stato assegnato alcun riassunto...
            ?>
        	<div id="riassuntoTrovato">
                <div id="risultatoRicercaAlto"><b><?php echo $_SESSION['email']; ?>: </b> non ci sono riassunti da analizzare!</div>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
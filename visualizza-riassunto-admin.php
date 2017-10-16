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
session_start();
if (!isset($_SESSION['accessoPermessoAdmin'])) {
    header('Location: login.php');
}

$IDGet = $_GET['IDRiassunto'];

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
    //Eliminiamo il riassunto dal riassunti.xml se abbiamo premuto "elimina riassunto"
    //Bisogna eliminarlo anche da tags.xml ma non da segnalazioni.xml (a quello ci pensa già la home-admin.php)
    if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
        if (isset($_GET['elimina'])) {
            if ($IDGet == $IDRiassuntoText[$id]) { 
                $riassunto->parentNode->removeChild($riassunto);
                $path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; 
                $doc3->save($path3);

                /* Inizializziamo il file TAGS.XML */
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
                    $riassuntoIDLista = $tag->getElementsByTagName('riassuntoID');
                    foreach ($riassuntoIDLista as $key => $value) { //Inseriamo nell'array riassuntoIDTrovato ognuno degli ID del tag ricercato
                        $riassuntoIDTag = $riassuntoIDLista->item($key)->textContent;
                        if ($riassuntoIDTag == $IDGet) {
                            $riassuntoIDLista->item($key)->parentNode->removeChild($riassuntoIDLista->item($key));
                            $path2 = dirname(__FILE__)."/xml-schema/tags.xml"; 
                            $doc2->save($path2);
                        }
                    }
                }	
                /***/



                header('Location: home-admin.php');
                exit();
            }
        }
    }

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
	for ($k=0; $k < $tagsRiassunto[$id]->length; $k++) { 	
		$nomeTagRiassunto = $tagsRiassunto[$id]->item($k);
		$nomeTagRiassuntoText[$k] = $nomeTagRiassunto->textContent;
	}

	$preferitiRiassuntoElement[$id] = $tagsRiassuntoElement[$id]->nextSibling;
	$preferitiRiassunto[$id] = $preferitiRiassuntoElement[$id]->childNodes;
	for ($k=0; $k < $preferitiRiassunto[$id]->length; $k++) {	
		$emailPreferitiRiassunto = $preferitiRiassunto[$id]->item($k);
		$emailPreferitiRiassuntoText[$k] = $emailPreferitiRiassunto->textContent;
	}
}
/***/
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
	//Se il riassunto ancora esiste...
	if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
		//Se abbiamo premuto il pulsante tutto ok
		if (isset($_GET['tuttoOk'])) {
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
                /*Procedura di eliminazione dalla lista dei riassunti segnalati, se un riassunto non è più presente in riassunti.xml*/
               if ($_GET['IDRiassunto'] == $riassuntoIDText[$i]) { 
                    //Eliminiamo la segnalazione corrispondente che possiede un ID non più valido
                    $segnalazione->parentNode->removeChild($segnalazione);
                    $path4 = dirname(__FILE__)."/xml-schema/segnalazioni.xml"; 
                    $doc4->save($path4);
                    header('Location: home-admin.php');
                    exit();
                }
            }                
        }
		//Da qui in poi visualizziamo il riassunto
		?>
        <div id="visualizzaRiassunto">
            <div id="nomeMateria">
                <?php echo "<b>".$titoloRiassuntoText[$IDGet]."</b>"; ?>
			</div>
			<div>
				<?php 
				echo "<br />";
                echo nl2br($descrizioneRiassuntoText[$IDGet])."<br />";?>
				<br />
				<embed src="<?php echo $linkDocumentoRiassuntoText[$IDGet]; ?>" width="100%" height="500" type='application/pdf'>
				<br /><br />
				<hr style='width: 95%;'/><hr id='lista' />
				<?php
				echo "<b>Autore</b>: ".$emailStudenteRiassuntoText[$IDGet]."<br /><hr id='lista' />";
				echo "<b>Data </b>: ".$dataRiassuntoText[$IDGet]." <b>Ora </b>: ".$orarioRiassuntoText[$IDGet]."<br /> <hr id='lista' />";
				echo "<b>Tags</b>: ";
				foreach ($tagsRiassunto[$IDGet] as $key=>$value) { 
					echo $nomeTagRiassuntoText[$key]." | ";
				}
				echo "<br /> <hr id='lista' />";
				echo "<b>Visualizzazioni</b>: ".$visualizzazioniRiassuntoText[$IDGet]." <br />";
				echo "<b>Preferiti</b>: ";
				$numeroPreferiti = $preferitiRiassunto[$IDGet] ->length;
				echo $numeroPreferiti." <hr id='lista' />";
				?>
				<div id="opzioniRiassunto">
					<a id="tuttoOk" href="visualizza-riassunto-admin.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&tuttoOk=".urlencode(1).""; ?>">Tutto ok</a>
					<a id="segnalaRiassunto" href="visualizza-riassunto-admin.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&elimina=".urlencode(1).""; ?>">Elimina riassunto </a>
				</div>
				
        </div>
		<?php } 
		else {
			echo "Impossibile visualizzare un riassunto se non viene fornito un ID valido";
		}?>
    </div>
</body>
</html>
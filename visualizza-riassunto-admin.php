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
include("default-code/caricamento-riassunti-xml.php");
include("default-code/caricamento-tags-xml.php");
include("default-code/caricamento-segnalazioni-xml.php");

foreach ($IDRiassuntoLista as $count => $id) {
	$riassunto = $riassunti->item($count); 
    //Eliminiamo il riassunto dal riassunti.xml se abbiamo premuto "elimina riassunto"
    //Bisogna eliminarlo anche da tags.xml ma non da segnalazioni.xml (a quello ci pensa già la home-admin.php)
    if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
        if (isset($_GET['elimina'])) {
            if ($IDGet == $IDRiassuntoText[$id]) {
                $numeroPreferiti = $preferitiRiassunto[$id]->length; //Questo serve per togliere la reputation dall'utente

                unlink ($linkDocumentoRiassuntoText[$id]);
                $riassunto->parentNode->removeChild($riassunto);
                $path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; 
                $doc3->save($path3);

                //Togliamo 1 alla e la quantità di preferiti dalla reputation dell'autore
                $xmlString = "";
                foreach (file("xml-schema/studenti.xml") as $node) { 
                    $xmlString .= trim($node); 
                }
                $doc = new DOMDocument(); 
                $doc->loadXML($xmlString); 
                $root = $doc->documentElement; 
                $studenti = $root->childNodes;

                $reputationDaModificare = -1-$numeroPreferiti;
                $emailStudente = $emailStudenteRiassuntoText[$IDGet];
                include ('default-code/modificaReputation.php');
                $path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
                $doc->save($path);

                
                for ($k=0; $k < $tags->length; $k++) {	
                    $tag = $tags->item($k); 
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
                header('Location: home-admin.php');
                exit();
            }
        }
    }
}

include("default-code/info-admin.php");
?>
<div id="main">
<?php 
//Se il riassunto ancora esiste...
if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
    //Se abbiamo premuto il pulsante tutto ok
    if (isset($_GET['tuttoOk'])) {
        for ($i=0; $i < $segnalazioni->length; $i++) {
            $segnalazione = $segnalazioni->item($i);
            //Procedura di eliminazione dalla lista dei riassunti segnalati, se un riassunto non è più presente in riassunti.xml
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
        <?php
        $numeroPreferiti = $preferitiRiassunto[$IDGet] ->length; //Va messa perchè non abbiamo caricato di default il file riassunti.xml
        include("default-code/core-visualizza-riassunto-admin.php")
        ?>
        <div id="opzioniRiassunto">
            <a id="tuttoOk" href="visualizza-riassunto-admin.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&tuttoOk=".urlencode(1).""; ?>">Tutto ok</a>
            <a id="segnalaRiassunto" href="visualizza-riassunto-admin.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&elimina=".urlencode(1).""; ?>">Elimina riassunto </a>
        </div>
    </div>
    <?php } 
    else {
        ?>
        <div id='message'>
            <img src="images/iconMessage.png">
            <div>
                <strong>Impossibile visualizzare un riassunto se non viene fornito un ID valido.</strong>
                <br />
                Ti stiamo reindirizzando...
            </div>
        </div>
        <?php
        header("refresh:3; url=home-admin.php");
    }
    ?>
</div>
</body>
</html>
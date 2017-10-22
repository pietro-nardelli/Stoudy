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
include('default-code/info-studente.php');
include('default-code/caricamento-riassunti-xml.php');
include("default-code/caricamento-tags-xml.php");


?>
<div id="main">
    <?php

    //Creiamo gli array per generare la lista dei riassunti visualizzati
    for ($k=0; $k < $riassuntiVisualizzati->length; $k++) {
        $valueID = $riassuntoIDVisualizzatoText[$k];
        $riassuntoVisualizzato = $riassuntiVisualizzati ->item($k);

        if (isset($_SESSION['eliminato'])) {//Se è stato emesso un cookie con valueID

            if ($_SESSION['eliminato'] == $valueID) { 
                //Eliminiamo il riassunto corrispondente che possiede un ID non più valido
                $riassuntoVisualizzato->parentNode->removeChild($riassuntoVisualizzato);
                $path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
                $doc->save($path);
                unset($_SESSION['eliminato']);
                header('Location: riassunti-preferiti.php');
                exit();
            }

        }

        if (isset ($titoloRiassuntoText[$valueID])) { //Se il riassunto non è stato cancellato...
            $valueIDArray [] = $valueID;
            $riassuntoTrovatoTitolo[] = $titoloRiassuntoText[$valueID];
            $riassuntoTrovatoEmail[] = $emailStudenteRiassuntoText[$valueID];
            $riassuntoTrovatoData [] = $dataRiassuntoText[$valueID];
            $riassuntoTrovatoOrario [] = $orarioRiassuntoText[$valueID];
            $riassuntoTrovatoVisualizzazioni []= $visualizzazioniRiassuntoText[$valueID];
            $riassuntoTrovatoPreferiti [] =  $preferitiRiassunto[$valueID]->length;
        }
        else { //Se invece è stato cancellato bisogna eliminarlo dalla lista dei visualizzati
				$_SESSION['eliminato'] = $valueID; //Emettiamo un cookie con valueID
				header('Location: riassunti-visualizzati.php');
				exit();
        }
        
    }
    ?>
    <?php
    if ($riassuntiVisualizzati->length) { ?>
        <div id="riassuntoTrovato">
            <div id="risultatoRicercaAlto">
                I tuoi riassunti visualizzati</a>
            </div>
            <hr />
            <?php include("default-code/gestione-pagine-riassunto.php"); ?>
        </div>

        <?php
    }
    else {
        echo "Non hai visualizzato alcun riassunto...";
    }
    ?>

</div>
</body>
</html>
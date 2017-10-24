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

      //Creiamo gli array per generare la lista dei riassunti creati
      for ($k=0; $k < $riassuntiCreati->length; $k++) {
        $valueID = $riassuntoIDCreatoText[$k];
        $riassuntoCreato = $riassuntiCreati ->item($k);

        if (isset($_SESSION['eliminato'])) {//Se è stato emesso un cookie con valueID

            if ($_SESSION['eliminato'] == $valueID) { 
                //Eliminiamo il riassunto corrispondente che possiede un ID non più valido
                $riassuntoCreato->parentNode->removeChild($riassuntoCreato);
                $path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
                $doc->save($path);
                unset($_SESSION['eliminato']);
                header('Location: riassunti-creati.php');
                exit();
            }

        }

        if (isset ($titoloRiassuntoText[$valueID])) { //Se il riassunto non è stato eliminato 
            $valueIDArray [] = $valueID;
            $riassuntoTrovatoTitolo[] = $titoloRiassuntoText[$valueID];
            $riassuntoTrovatoEmail[] = $emailStudenteRiassuntoText[$valueID];
            $riassuntoTrovatoData [] = $dataRiassuntoText[$valueID];
            $riassuntoTrovatoOrario [] = $orarioRiassuntoText[$valueID];
            $riassuntoTrovatoVisualizzazioni []= $visualizzazioniRiassuntoText[$valueID];
            $riassuntoTrovatoPreferiti [] =  $preferitiRiassunto[$valueID]->length;
        }
        else { //Se invece è stato cancellato bisogna eliminarlo dalla lista dei creati
			$_SESSION['eliminato'] = $valueID; //Emettiamo un cookie con valueID
			header('Location: riassunti-creati.php');
			exit();
        }
        
    }
    ?>
   
    <?php
    if ($riassuntiCreati->length) { ?>
        <div id="riassuntoTrovato">
            <div id="risultatoRicercaAlto">
                I tuoi riassunti creati</a>
            </div>
            <hr />
            <?php include("default-code/gestione-pagine-riassunto.php"); ?>
        </div>
    <?php
    }
    else {
        ?>
		<div id='message'>
			<img src="images/iconMoleskine.png">
			<div>
                <strong>Non hai ancora creato alcun riassunto.</strong>
                <br />
				Puoi crearne uno premendo l'apposito pulsante nel riquadro della materia da te creata.
			</div>
		</div>
		<?php
    }
    ?>
</div>

</body>
</html>
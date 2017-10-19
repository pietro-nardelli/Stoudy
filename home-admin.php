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

$trovato = false; //Neanche un riassunto trovato
include("default-code/caricamento-riassunti-xml.php");
include("default-code/caricamento-segnalazioni-xml.php");
for ($i=0; $i < $segnalazioni->length; $i++) {
	$segnalazione = $segnalazioni->item($i);
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
    if (strcasecmp ($emailAdminText[$i], $_SESSION['email']) == 0 ) {
        $trovato = true; //Almeno un riassunto trovato
        $riassuntoIDSegnalato [] = $riassuntoIDText[$i]; //Inseriamo nell'array  l'ID dei riassunti ad esso assegnati
    }
}

include("default-code/info-admin.php");
include("default-code/caricamento-tags-xml.php");
include("default-code/caricamento-revisioni-xml.php");
?>

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
			<div id="risultatoRicercaAlto">
				<b><?= $_SESSION['email'] ?>: </b>dovresti analizzare <b><?= sizeof($valueIDArray) ?> riassunti</b> 
			</div>
			<hr />
			<?php
			include("default-code/gestione-pagine-riassunto-admin.php");
			?>
		</div>
		<?php
	}
	else { //Se non è stato assegnato alcun riassunto...
		?>
		<div id="riassuntoTrovato">
			<div id="risultatoRicercaAlto"><b><?= $_SESSION['email'] ?>: </b> non ci sono riassunti da analizzare!</div>
		</div>
		<?php
	}
	?>
	<div id="riassuntoTrovato">
		<table id="tabellaTagEstrattiAdmin">
			<tr><th>Tag</th><th>Old estratto</th><th>New estratto</th></tr>
			<?php
			for ($i=0; $i < $revisioni->length; $i++) {
				//Se il tag è già presente nelle revisioni allora non può essere revisionato nuovamente
				foreach($nomeTagText as $j=>$value) {
					if ( strcasecmp ($_SESSION['email'], $emailAdminText[$i]) == 0 ) {
						if (strcasecmp($value, $nomeTagRevisioneText[$i]) == 0) {
							echo "<tr><td><a id='tagAnteprima' href='#'>".$nomeTagRevisioneText[$i]."</a></td><td>".$estrattoTagText[$j]."</td><td>".$modificaEstrattoText[$i]."</td></tr>";
						}
					}
				}
			}
		?>
		</table>
	</div>
</div>

</body>
</html>
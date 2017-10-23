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
/////////////////////////////////// GESTIONE RIASSUNTI /////////////////////////////////
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


	/////////////////////////////////// GESTIONE REVISIONI /////////////////////////////////


	//Abbiamo premuto modifica, andiamo ad eliminarlo da revisioni...
	if (isset($_POST['modificaEstrattoAdmin'])) {
		for ($i=0; $i < $revisioni->length; $i++) {
			$revisione= $revisioni->item($i);
			if (!strcasecmp($_POST['nomeTag'], $nomeTagRevisioneText[$i])) {				
				$revisione->parentNode->removeChild($revisione);
				$path5 = dirname(__FILE__)."/xml-schema/revisioni.xml"; 
				$doc5->save($path5);
			}
		}
		//... e a modificarlo da tags
		for ($j= 0; $j < $tags->length; $j++) {
			$tag= $tags->item($j);
			if (!strcasecmp ($_POST['nomeTag'], $nomeTagText[$j])) {
				echo "ciao";
				$estrattoTag[$j]->nodeValue = $_POST['modificaEstratto'];
				$path2 = dirname(__FILE__)."/xml-schema/tags.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc2->save($path2); //Sovrascriviamolo
			}
		}
		header('Location: home-admin.php');
		exit();
	}
	//Abbiamo premuto annulla, andiamo ad eliminarlo da revisioni.
	if (isset($_POST['annullaEstrattoAdmin'])) {
		for ($i=0; $i < $revisioni->length; $i++) {
			$revisione= $revisioni->item($i);
			if (!strcasecmp($_POST['nomeTag'], $nomeTagRevisioneText[$i])) {
				$revisione->parentNode->removeChild($revisione);
				$path5 = dirname(__FILE__)."/xml-schema/revisioni.xml"; 
				$doc5->save($path5);
				header('Location: home-admin.php');
				exit();
			}
		}
	}

	?>
	<div id="riassuntoTrovato">
		<table id="tabellaTagEstrattiAdmin">
			<tr>
				<th>Tag</th>
				<th>Old estratto</th>
				<th>New estratto</th>
				<th></th>
			</tr>
			<?php
			for ($i=0; $i < $revisioni->length; $i++) {
				//Se il tag è già presente nelle revisioni allora non può essere revisionato nuovamente
				foreach($nomeTagText as $j=>$value) {
					if ( strcasecmp ($_SESSION['email'], $emailAdminRevisioneText[$i]) == 0 ) {
						if (strcasecmp($value, $nomeTagRevisioneText[$i]) == 0) {
							?>
							<form action="home-admin.php" method="POST">
								<tr>
									<td><a id='tagAnteprima' href='#'><?= $nomeTagRevisioneText[$i] ?></a></td>
									<td><?= $estrattoTagText[$j] ?></td>
									<td>
										<textarea rows="2" name="modificaEstratto"><?= $modificaEstrattoText[$i] ?></textarea>
										<input type="hidden" name="nomeTag" value="<?= $nomeTagRevisioneText[$i] ?>">
									</td>
									<td style="width: 120px;">
										<input type="submit" name="modificaEstrattoAdmin" value="Modifica" />
										<input type="submit" name="annullaEstrattoAdmin" value="Annulla" />
									</td>				
								</tr>
							</form>
							<?php
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
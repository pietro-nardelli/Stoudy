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
			}
			else { //Se invece è stato cancellato bisogna eliminarlo da segnalazioni.xml
				$_SESSION['eliminato'] = $valueID; //Emettiamo un cookie con valueID
				header('Location: home-admin.php');
				exit();
			}
		}		
		?>
		<div id="riassuntoTrovato">
			<?php
			include("default-code/gestione-pagine-riassunto-admin.php");
			?>
		</div>
		<?php
	}
	else { //Se non è stato assegnato alcun riassunto...
		?>
		<div id="riassuntoTrovato">
			<div id="risultatoRicercaAlto">Non ci sono riassunti da analizzare.</div>
		</div>
		<?php
	}


	/////////////////////////////////// GESTIONE REVISIONI /////////////////////////////////


	//Abbiamo premuto modifica, andiamo ad eliminarlo da revisioni...
	if (isset($_POST['modificaDescrizioneAdmin'])) {
		for ($i=0; $i < $revisioni->length; $i++) {
			$revisione= $revisioni->item($i);
			if (!strcasecmp($_POST['nomeTag'], $nomeTagRevisioneText[$i])) {

				//Aggiungiamo 2 ai coin presenti
				$xmlString = "";
                foreach (file("xml-schema/studenti.xml") as $node) { 
                    $xmlString .= trim($node); 
                }
                $doc = new DOMDocument(); 
                $doc->loadXML($xmlString); 
                $root = $doc->documentElement; 
				$studenti = $root->childNodes;
				
				$reputationDaModificare = 2;
				$emailStudente = $emailStudenteRevisioneText[$i];
				include ('default-code/modifica-reputation.php');
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
				$doc->save($path);


				$revisione->parentNode->removeChild($revisione);
				$path5 = dirname(__FILE__)."/xml-schema/revisioni.xml"; 
				$doc5->save($path5);
			}
		}
		//... e a modificarlo da tags
		for ($j= 0; $j < $tags->length; $j++) {
			$tag= $tags->item($j);
			if (!strcasecmp ($_POST['nomeTag'], $nomeTagText[$j])) {
				$descrizioneTag[$j]->nodeValue = $_POST['modificaDescrizione'];
				$path2 = dirname(__FILE__)."/xml-schema/tags.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc2->save($path2); //Sovrascriviamolo
			}
		}

		header('Location: home-admin.php');
		exit();
	}
	//Abbiamo premuto annulla, andiamo ad eliminarlo da revisioni.
	if (isset($_POST['annullaDescrizioneAdmin'])) {
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
			<?php
			$countRevisioni = 0; //serve per sapere se ci sono revisioni o meno.
			for ($i=0; $i < $revisioni->length; $i++) {
				//Se il tag è già presente nelle revisioni allora non può essere revisionato nuovamente
				foreach($nomeTagText as $j=>$value) {
					if ( strcasecmp ($_SESSION['email'], $emailAdminRevisioneText[$i]) == 0 ) {
						if (strcasecmp($value, $nomeTagRevisioneText[$i]) == 0) {
							$countRevisioni++; 
							?>
							<form action="home-admin.php" method="POST">
								<table id="tabellaTagDescrizioniAdmin">
									<tr>
										<td style="width: 100%; text-align: center; padding: 10px;" colspan="3"><a id='tagAnteprima' href='#'><?= $nomeTagRevisioneText[$i] ?></a></td>
									</tr>
									<tr>
										<td style="width: 45%"><?= $descrizioneTagText[$j] ?></td>
										<td style="width: 45%">
											<textarea name="modificaDescrizione"><?= $modificaDescrizioneText[$i] ?></textarea>
											<input type="hidden" name="nomeTag" value="<?= $nomeTagRevisioneText[$i] ?>">
										</td>
										<td>
											<input type="submit" name="modificaDescrizioneAdmin" value="Conferma modifica" />
											<input type="submit" name="annullaDescrizioneAdmin" value="Annulla" />
										</td>				
									</tr>
								</table>
							</form>
							<?php
						}
					}
				}
			}
			if ($countRevisioni == 0) {
				?>
				<div id="risultatoRicercaAlto">
					Non ci sono descrizioni da revisionare. 
				</div>
				<?php
			}
		?>
		
	</div>
</div>

</body>
</html>
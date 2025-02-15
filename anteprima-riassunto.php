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
include("default-code/info-studente.php");
include("default-code/caricamento-tags-xml.php");
?>

<div id="main">
<?php
	if (isset($_GET['nomeMateria'])) { 
		//Dopo aver caricato il pdf, compilato correttamente il form e controllato i tag che siano corretti, possiamo aggiornare il DOM
		if (isset($_SESSION['anteprimaRiassunto'])) {
			if (!isset($_POST['conferma']) && !isset($_GET['conferma'])) {
			?>
			<div id="visualizzaRiassunto">
				<div id="nomeMateria">
					<b><?= $_SESSION['titoloRiassunto'] ?></b>
				</div>
				<form action="anteprima-riassunto.php?nomeMateria=<?php echo $_GET['nomeMateria'] ?>" method="POST">
					<div>
						<?php
						if (!empty ($_SESSION['descrizioneRiassunto'])) {
							?>
							<div style='margin-left: 5px;'><?= nl2br($_SESSION['descrizioneRiassunto']) ?></div> 
							<br />
							<?php
						}
						?>
						<embed src="<?=$_SESSION['linkDocumentoRiassunto']; ?>" width="100%" height="600px" type='application/pdf'>
						<br /><br />
						<table id="tabellaTagDescrizioni">
							<tr>
								<th colspan="2">Controllo tag</th>
							</tr>
							<?php
							foreach ($_SESSION['tagsRiassuntoNuovo'] as $l => $value) {
								for ($k=0; $k < $tags->length; $k++) {	
									//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml
									if (strcasecmp ($value, $nomeTagText[$k]) == 0 && !empty($descrizioneTagText[$k])) {
										$indiceTrovato[$l] = -1;
										?>
										<tr><td><a id='tagAnteprima' href='#'><?= $value ?></a></td><td><?= $descrizioneTagText[$k] ?></td></tr>
										<?php
										break; //Usiamo il break perchè tanto abbiamo trovato ciò che cercavamo nel nostro for annidato
									}
								}
							}
							//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml: caso in cui tag non è presente.
							foreach ($_SESSION['tagsRiassuntoNuovo'] as $p => $value) {
								for ($k=0; $k < $tags->length; $k++) {	
									$descrizioneTagText[$k] = $descrizioneTag[$k]->textContent;
									//Dobbiamo allora semplicemente creare un tag nuovo
									if (empty($indiceTrovato[$p]) ||  $indiceTrovato[$p] != -1) {
										?>
										<tr>
											<td>
												<a id='tagAnteprima' href='#'><?= $value ?></a>
											</td>
											<td>
												<textarea name="aggiungiDescrizione[<?= $p ?>]" placeholder="Aggiungere una descrizione al tag che si sta creando (max 500 caratteri)."><?php if (isset($_SESSION['aggiungiDescrizione'])) { echo $_SESSION['aggiungiDescrizione'][$p]; } ?></textarea>
											</td>
										</tr>
										<?php
										break; //Usiamo il break perchè tanto abbiamo trovato ciò che cercavamo nel nostro for annidato
									}
								}
								if (!$tags->length) { //Caso in cui non esistono ancora tags
									?>
									<tr>
										<td>
											<a id='tagAnteprima' href='#'><?= $value ?>
										</a>
									</td>
									<td>
										<i>descrizione mancante...</i>
									</td>
								</tr>
									<?php
								}
							}
							?>
						</table>
					</div>
					<div id="pulsantiAnteprima">
						<a href="anteprima-riassunto.php?nomeMateria=<?php echo $_GET['nomeMateria']."&conferma=0"; ?> " id="tornaAdAggiungiRiassunto">Indietro</a>
						<input type="submit" name="conferma" value="Conferma riassunto" id="confermaRiassunto"/>	
					<div>
				</form>	
			</div>
			<?php
			}
			else if (isset($_POST['conferma'])) {
				//Carichiamo in un'array di sessione tutti i valori precedentemente immessi, 
				//casomai per errore tornassimo alla pagina precedente
				foreach ($_POST['aggiungiDescrizione'] as $key => $value) {
					$_SESSION['aggiungiDescrizione'][$key] = $value;
				}

				$errore = false;
				foreach ($_POST['aggiungiDescrizione'] as $value) {
					if (strlen($value) > 500 || strlen($value) == 0) {
						$errore = true;
						break;
					}
				}
				if ($errore) {
					?>
					<div id='message'>
						<img src="images/iconMessage.png">
						<div>
							<strong>La descrizione di un tag non può essere lunga più di 500 caratteri oppure vuota.</strong>
							<br />
							Ti stiamo reindirizzando...
						</div>
					</div>
					<?php
					header("refresh:3; url=anteprima-riassunto.php?nomeMateria=".$_GET['nomeMateria']."");
					exit();
				}
				
				//DA QUI IN AVANTI SI AGGIORNA IL DOM
								
				include("default-code/caricamento-riassunti-xml.php"); //Prima carichiamo tutti i riassunti
				// AGGIORNIAMO IL FILE RIASSUNTI.XML
				//$lastIDRiassunto proviene da caricamento-riassunti-xml.php ed è l'ultimo IDRiassunto presente (o che lo è stato)
				//(visto che abbiamo sempre aggiunto e non è possibile che non siano in ordine di grandezza)...
				$newID = $lastIDRiassunto +1;

				$newRiassunto = $doc3->createElement("riassunto");
				$newIDRiassunto = $doc3->createElement("ID", $newID );		
				$newTitoloRiassunto = $doc3->createElement("titolo", $_SESSION['titoloRiassunto']);
				$newEmailStudenteRiassunto = $doc3->createElement("emailStudente", $_SESSION['email']);
				$newDataRiassunto = $doc3->createElement("data", $_SESSION['nowDate']);
				$newOrarioRiassunto = $doc3->createElement("orario", $_SESSION['nowTime']);
				$newDescrizioneRiassunto = $doc3->createElement("descrizione", $_SESSION['descrizioneRiassunto']);
				$newLinkDocumentoRiassunto = $doc3->createElement("linkDocumento", $_SESSION['linkDocumentoRiassunto']);				
				$newVisualizzazioniRiassunto = $doc3->createElement("visualizzazioni","0");
				$newTagsRiassunto = $doc3->createElement("tags");
				$newPreferitiRiassunto = $doc3->createElement("preferiti");

				$newRiassunto->appendChild($newIDRiassunto);
				$newRiassunto->appendChild($newTitoloRiassunto);
				$newRiassunto->appendChild($newEmailStudenteRiassunto);
				$newRiassunto->appendChild($newDataRiassunto);
				$newRiassunto->appendChild($newOrarioRiassunto);
				$newRiassunto->appendChild($newDescrizioneRiassunto);
				$newRiassunto->appendChild($newLinkDocumentoRiassunto);
				$newRiassunto->appendChild($newVisualizzazioniRiassunto);
				$newRiassunto->appendChild($newTagsRiassunto);
				$newRiassunto->appendChild($newPreferitiRiassunto);
				foreach ($_SESSION['tagsRiassuntoNuovo'] as $l => $value) {
					$newNomeTagRiassunto = $doc3->createElement("nomeTag", $value);
					$newTagsRiassunto->appendChild($newNomeTagRiassunto);
				}
				$newRiassunto->setAttribute("condivisione", $_SESSION['condivisioneRiassunto']);
				$root3->setAttribute("lastID", $newID);
				
				$root3->appendChild($newRiassunto); //Va fatto con appendChild altrimenti potrebbe creare problemi...
				$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml";
				$doc3->save($path3);
				//////////////


				//AGGIORNIAMO IL FILE STUDENTI.XML (solamente riassunti->creati e coins)
				
				//Aggiungiamo 1 ai coin presenti solo se il riassunto è pubblico
				if (!strcasecmp ($_SESSION['condivisioneRiassunto'], "pubblico")) {
					$reputationDaModificare = 1;
					$emailStudente = $_SESSION['email'];
					include ('default-code/modifica-reputation.php');
				}
				

				$riassuntiCreati = $riassuntiStudente->firstChild;
				
				$newRiassuntoIDCreato = $doc->createElement("riassuntoIDCreato", $newID);
				$riassuntiCreati->appendChild($newRiassuntoIDCreato); //CHECK SE SERVE QUESTO, visto che c'è già insertBefore		
				$newRiassuntoIDCreato->setAttribute("materiaRiassunto", $_GET['nomeMateria']);
				
				$riassuntiCreati->insertBefore($newRiassuntoIDCreato);
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; 
				$doc->save($path);
				////////////////

				//AGGIORNIAMO IL FILE TAGS.XML
				for ($k=0; $k < $tags->length; $k++) {
					$tag = $tags->item($k);						
					//$l è l'indice del tag, tra quelli inseriti. 
					//Se l'array indiceTrovato è pari a -1 => quel tag è già presente nel file.
					//Altrimenti => quel tag non è presente nel file e va aggiunto da zero.
					
					//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml: caso in cui tag già presente.
					foreach ($_SESSION['tagsRiassuntoNuovo'] as $l => $value) {
						//Dobbiamo allora semplicemente associare un riassuntoID al tag($k) corrispondente.
						if (strcasecmp ($value, $nomeTagText[$k]) == 0) {
							$indiceTrovato[$l] = -1;
							$newRiassuntoID = $doc2->createElement("riassuntoID", $newID);
							$tag->appendChild($newRiassuntoID);	
						}
					}	
				}
				//Confrontiamo ogni tag inserito con quelli già presenti in tags.xml: caso in cui tag non è presente.
				foreach ($_SESSION['tagsRiassuntoNuovo'] as $p => $value) {
					//Dobbiamo allora semplicemente creare un tag nuovo
					if (empty($indiceTrovato[$p]) ||  $indiceTrovato[$p] != -1) {
						$newTag = $doc2->createElement("tag");
						$newNome = $doc2->createElement("nome", $value);
						$newDescrizione = $doc2->createElement("descrizione", $_SESSION['aggiungiDescrizione'][$p]);
						$newRiassuntoID = $doc2->createElement("riassuntoID", $newID);
						
						$newTag->appendChild($newNome);
						$newTag->appendChild($newDescrizione);
						$newTag->appendChild($newRiassuntoID);	

						$root2->appendChild($newTag);
					}
				}

				$path2 = dirname(__FILE__)."/xml-schema/tags.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc2->save($path2); //Sovrascriviamolo
				///////////////////

				
				unset($_SESSION['nowDate']);
				unset($_SESSION['nowTime']);
				unset($_SESSION['titoloRiassunto']);
				unset($_SESSION['descrizioneRiassunto']);
				unset($_SESSION['linkDocumentoRiassunto']);
				unset($_SESSION['condivisioneRiassunto']);
				unset($_SESSION['tagsRiassuntoNuovo']); //Questa è una variabile session che gestisce l'array dei tags
				unset($_SESSION['aggiungiRiassunto']); //Bisogna fare unset dopo aver aggiornato il DOM
				unset($_SESSION['aggiungiDescrizione']);
				unset($_SESSION['anteprimaRiassunto']);
				header('Location: visualizza-riassunto.php?IDRiassunto='.$newID);
				exit();
			} 
			else if ($_GET['conferma'] == 0) { //Se conferma c'è ma è diversa da 1 (ovvero 0)
				unlink ($_SESSION['linkDocumentoRiassunto']);
				unset($_SESSION['aggiungiDescrizione']);
				header("Location: aggiungi-riassunto.php?nomeMateria=".$_GET['nomeMateria']."");
				exit();
			}
		} 
		else { //Se non c'è la sessione, c'è stato un errore e non potevamo trovarci in questa pagina...
			header("Location: aggiungi-riassunto.php?nomeMateria=".$_GET['nomeMateria']."");
			exit();
		}
	}
	else { //Se non c'è il get con nomeMateria...
		?>
		<div id='message'>
			<img src="images/iconMessage.png">
			<div>
				<strong>Impossibile aggiungere riassunto senza una materia!</strong>
				<br />
				Ti stiamo reindirizzando...
			</div>
		</div>
		<?php
		header("refresh:3; url=home-studente.php");
	}
?>
</div>
</body>
</html>
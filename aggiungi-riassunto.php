<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("aggiungi-riassunto.css");
	</style>	
</head>
<body>
<?php
				
				/*
				echo nl2br($_POST['testoRiassuntoForm'])."<br />"; //Stampa correttamente con i <br />
				*/

session_start();

if (!isset($_SESSION['accessoPermesso'])) {
    header('Location: login.php');
}
/***Procedura standard per inizializzare il file XML***/
$xmlString = ""; //Inizializziamo la variabile xmlString
foreach (file("xml-schema/studenti.xml") as $node) { //Per ogni riga del file xml...
	$xmlString .= trim($node); //Aggiungi alla stringa $xmlString la riga $node
}
$doc = new DOMDocument(); //Creiamo l'oggetto documento DOM e lo assegnamo a $doc
$doc->loadXML($xmlString); //$doc essendo un oggetto DOMDocument possiede il parser XML (metodo nella classe DOMDocument) che lo facciamo girare sulla stringa $xmlString
$root = $doc->documentElement; //la chiamata a DocumentElement restituisce la radice del documento DOM
$studenti = $root->childNodes; //$studenti contiene i nodi figli di root 
/***/

/*Scorri tutti gli studenti del file XML*/
for ($i=0; $i < $studenti->length; $i++) {
	/*Se l'email derivante dal login coincide con uno studente presente nel file XML, carica tutti i dati relativi*/
	if ($_SESSION['email'] == $studenti->item($i)->firstChild->nextSibling->nextSibling->textContent){
		$studente = $studenti->item($i); //questo sarà uno degli studenti e sarà dotato di altri figli.
		$nome = $studente->firstChild; 
		$nomeText = $nome->textContent;

		$cognome = $nome->nextSibling;
		$cognomeText = $cognome->textContent;

		$email = $cognome->nextSibling;
		$emailText = $email->textContent;

		$materieElement = $email->nextSibling; //Questo rappresenta l'elemento "materie"
		$materie = $materieElement->childNodes; //Quest'altro invece la lista di materie
		/*Bisogna creare un array per ogni valore presente in materia, affinchè si possa successivamente
		 *elencare ed aggiornare le materie presenti nella lista. Se si creasse un array per i soli valori
		 *testuali sarebbe impossibile aggiornarli. Ogni valore $k deve appartenere ad una materia
		 *($k=>materia k-esima).
		 */
		
		for ($k=0; $k < $materie->length; $k++) {	
			$materia = $materie->item($k); //Materia k-esima appartenente alla lista precedentemente definita

			$statusText[$k] = $materia->getAttribute('status'); //Serve per capire se la materia è planned-unplanned-archived
			//L'unica cosa in comune tra gli status è che possiamo inserire in ogni caso creare l'array nomeMateria
			if ($statusText[$k] == 'unplanned' || $statusText[$k] == 'planned') {
				$nomeMateria[$k] = $materia->firstChild;
				$nomeMateriaText[$k] = $nomeMateria[$k]->textContent;
			}
			//Solo per la materia planned possiamo inserire tutti i dati inerenti al piano di studi, altrimenti errore
			if ($statusText[$k] == 'planned') {
				$valoreDaStudiare[$k] = $nomeMateria[$k]->nextSibling;
				$valoreDaStudiareText[$k] = $valoreDaStudiare[$k]->textContent;

				$oggettoStudio[$k] = $valoreDaStudiare[$k]->nextSibling;
				$oggettoStudioText[$k] = $oggettoStudio[$k]->textContent;

				$dataScadenza[$k] = $oggettoStudio[$k]->nextSibling;
				$dataScadenzaText[$k] = $dataScadenza[$k]->textContent;

				$nGiorniRipasso[$k] = $dataScadenza[$k]->nextSibling;
				$nGiorniRipassoText[$k] = $nGiorniRipasso[$k]->textContent;			

				$valoreStudiatoOggi[$k] = $nGiorniRipasso[$k]->nextSibling;			
				$valoreStudiatoOggiText[$k] = $valoreStudiatoOggi[$k]->textContent;

				$dataStudiatoOggi[$k] = $valoreStudiatoOggi[$k]->nextSibling;
				$dataStudiatoOggiText[$k] = $dataStudiatoOggi[$k]->textContent;
				
				$valoreStudiato[$k] = $dataStudiatoOggi[$k]->nextSibling;
				$valoreStudiatoText[$k] = $valoreStudiato[$k]->textContent;
			}
		} 

		$riassunti = $email->nextSibling->nextSibling;
		$reputation = $riassunti->nextSibling;
		$reputationText = $reputation->textContent;

		$coins = $reputation->nextSibling;
		$coinsText = $coins->textContent;	
	}
}
?>

	<div id="lateralHomeStudente">
		<div id="logoHomeStudente">
			<a href="home-studente.php">
				<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logoHome.png" style="width: 100%;"/>
			</a>
		</div>
		<div id="navigation">
			<a href="aggiungi-materia.php"><img src="images/iconAggiungiMateria.png">Nuova materia</a>
			<a href="#"><img src="images/iconRiassuntiCreati.png">Riassunti creati</a>
			<a href="#"><img src="images/iconRiassuntiVisualizzati.png">Riassunti visualizzati</a>
			<a href="#"><img src="images/iconRiassuntiPreferiti.png">Riassunti preferiti</a>
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="cercaRiassunti">		
				<input type="text" name="nome" placeholder=" Cerca riassunti" />
				<input type="image" src="images/iconCercaRiassunti.png" alt="Submit Form" />
			</form>
		</div>
		<div id="navigationUser">
			<div id="user">
				<img src="images/iconUtente.png"><?php echo $nomeText." ".$cognomeText; ?>
			</div>
			<div id="reputation">
				<img src="images/iconReputation.png" title="Reputation"><?php echo $reputationText; ?>
			</div>
			<div id="coins">
				<img src="images/iconCoins.png" title="Coins"><?php echo $coinsText; ?>
			</div>
		</div>
		<!-- Il link del logout si comporta come i precedenti ma si trova in un punto differente quindi bisogna assegnargli
			 uno stile interno particolare -->
		<div id="navigation" style="top: 75px; height: 40px;">
			<a href="logout.php"><img src="images/iconLogout.png">Logout</a>
		</div>
	</div>
    <div id="main">
        <div id="aggiungiRiassunto">
            <div id="nomeMateria">
                <?php echo "Aggiungi un riassunto di <b>".$_GET['nomeMateria']."</b>"; ?>
			</div>
			<?php
			if (isset($_POST['submit'])) {
				$erroreTags = 0; //Se rimane 0 tutto ok
				$errore = 0;

				//Titolo di almeno 10 caratteri e massimo 100 caratteri
				if (strlen($_POST['titoloRiassuntoForm']) < 10 || strlen($_POST['titoloRiassuntoForm']) > 100) {
					echo '<p style="color: red;">Titolo troppo corto / lungo.</p>';
					$errore = 1; 
				}
				//Testo almeno 100 caratteri e massimo 1000 caratteri
				if (strlen($_POST['testoRiassuntoForm']) < 100 || strlen($_POST['titoloRiassuntoForm']) > 1000) {
					echo '<p style="color: red;">Riassunto troppo corto / lungo.</p>';
				}
				
				/* Assegnamo ogni tag all'array tagsRiassuntoNuovo */
				$tagsRiassuntoNuovo = explode(",", $_POST['tagsRiassuntoForm']); //Divide la stringa in sottostringhe
				foreach ($tagsRiassuntoNuovo as $i => $value) { 
					$tagsRiassuntoNuovo[$i] = trim($value); 
					if (strlen($tagsRiassuntoNuovo[$i]) == 0 || is_numeric($tagsRiassuntoNuovo[$i]) || strlen($tagsRiassuntoNuovo[$i]) > 20)  {
						$erroreTags = 1;
					}
				}
				/* Controlliamo che non ci siano tag uguali */
				foreach ($tagsRiassuntoNuovo as $j => $value) {
					for ($h = $j+1; $h < sizeof($tagsRiassuntoNuovo) ; $h++ ) {
						if (strcasecmp($value, $tagsRiassuntoNuovo[$h]) == 0) {
							$erroreTags = 1;
						}
					}
				}

				//Tag di almeno 1 carattere, massimo 20 non numerico e non uguali...
				if ($erroreTags == 1) {
					echo '<p style="color: red;">I tag non devono essere vuoti, devono essere alfanumerici, non più lunghi di 20 caratteri e non possono essercene due uguali.</p>';
					$errore = 1;
				}
				//... e massimo 5 tag
				if ($i > 4) {
					echo '<p style="color: red;">Hai inserito troppi tag. Massimo 5.</p>';
					$errore = 1;
				}
				//Riassunto pubblico o privato
				if (!isset($_POST['condivisioneRiassuntoForm'])) {
					echo '<p style="color: red;">Scegliere se rendere il riassunto pubblico o privato.</p>';
					$errore = 1;
				}

				//Se $errore = 0 allora assegna i cookie corrispondenti
				if ($errore == 0) {	
					$nowDate = date("Y-m-d"); //Data odierna
					$nowTime = date("H:i:s"); //Ora odierna
					$_SESSION['titoloRiassunto'] = $_POST['titoloRiassuntoForm'];
					$_SESSION['testoRiassunto'] = $_POST['testoRiassuntoForm'];
					$_SESSION['condivisioneRiassunto'] = $_POST['condivisioneRiassuntoForm'];
					$_SESSION['tagsRiassuntoNuovo'] = $tagsRiassuntoNuovo; //Questa è una variabile session che gestisce l'array dei tags
					$_SESSION['aggiungiRiassunto'] = 1000; //Bisogna fare unset dopo aver aggiornato il DOM
					header($_SERVER["PHP_SELF"]);
				}
			}

			if (isset($_SESSION['aggiungiRiassunto'])) {
				//DA QUI IN AVANTI SI AGGIORNA IL DOM
				
				// Aggiorniamo il file studenti.xml (riassunti->creati) 
				/*$riassuntiCreati = $riassunti->firstChild;
				$newRiassuntoIDCreato = $doc->createElement("riassuntoIDCreato", "1");
										
				/*Attacchiamo all'elemento principale tutti i suoi figli e i corrispettivi "nipoti"*/
				/*$riassuntiCreati->appendChild($newRiassuntoIDCreato);				
				$newRiassuntoIDCreato->setAttribute("materiaRiassunto", $_GET['nomeMateria']);
				
				
				/*Inseriamo il nodo che abbiamo creato, prima del primo elemento della lista di elementi dopo root*/
				/*$riassuntiCreati->insertBefore($newRiassuntoIDCreato);
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc->save($path); //Sovrascriviamolo
				/***/

				/* Aggiorniamo il file tags.xml */
				/*
				$xmlString2 = ""; //Inizializziamo la variabile xmlString
				foreach (file("xml-schema/tags.xml") as $node2) { //Per ogni riga del file xml...
					$xmlString2 .= trim($node2); //Aggiungi alla stringa $xmlString la riga $node
				}
				$doc2 = new DOMDocument();
				$doc2->loadXML($xmlString2); 
				$root2 = $doc2->documentElement; 
				$tags = $root2->childNodes; 


				for ($k=0; $k < $tags->length; $k++) {	
					$tag = $tags->item($k); 
		
					$nomeTag[$k] = $tag->firstChild; 
					$nomeTagText[$k] = $nomeTag[$k]->textContent;
					$riassuntoID[$k] = $nomeTag[$k] ->nextSibling;
					$riassuntoIDText[$k] = $riassuntoID[$k]->textContent;
					
					
					foreach ($_SESSION['tagsRiassuntoNuovo'] as $i => $value) {
						if (strcasecmp ($value, $nomeTagText[$k]) == 0) {
							$indiceTrovato[$i] = -1; //In questo caso il tag di indice $i è stato trovato in xml

							$newRiassuntoID = $doc2->createElement("riassuntoID", "1");
							$tag->appendChild($newRiassuntoID);	
						}
					}	
				}
				foreach ($_SESSION['tagsRiassuntoNuovo'] as $l => $value) {
					if (empty($indiceTrovato[$l]) ||  $indiceTrovato[$l] != -1) {
						$newTag = $doc2->createElement("tag");
						$newNome = $doc2->createElement("nome", $value);
						$newRiassuntoID = $doc2->createElement("riassuntoID", "1");
						
						$newTag->appendChild($newNome);
						$newTag->appendChild($newRiassuntoID);	
											
						
						$root2->appendChild($newTag);
					}
				}

				$path2 = dirname(__FILE__)."/xml-schema/tags.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc2->save($path2); //Sovrascriviamolo
				/***/

				/* Aggiorniamo il file riassunti.xml */
				$xmlString3 = ""; 
				foreach (file("xml-schema/riassunti.xml") as $node3) { 
					$xmlString3 .= trim($node3); 
				}
				$doc3 = new DOMDocument(); 
				$doc3->loadXML($xmlString3); 
				$root3 = $doc3->documentElement; 
				$riassunti = $root3->childNodes; 
				for ($i=0; $i < $riassunti->length; $i++) {
					$riassunto = $riassunti->item($i); 
					$condivisioneRiassuntoText[$i] = $riassunto->getAttribute('condivisione');
					$IDRiassunto[$i] = $riassunto->firstChild; 
					$IDRiassuntoText[$i] = $IDRiassunto[$i]->textContent;

					$titoloRiassunto[$i] = $IDRiassunto[$i]->nextSibling;
					$titoloRiassuntoText[$i] = $titoloRiassunto[$i]->textContent;

					$emailStudenteRiassunto[$i] = $titoloRiassunto[$i]->nextSibling;
					$emailStudenteRiassuntoText[$i] = $emailStudenteRiassunto[$i]->textContent;

					$dataRiassunto[$i] = $emailStudenteRiassunto[$i]->nextSibling;
					$dataRiassuntoText[$i] = $dataRiassunto[$i]->textContent;

					$orarioRiassunto[$i] = $dataRiassunto[$i]->nextSibling;
					$orarioriassuntoText[$i] = $orarioRiassunto[$i]->textContent;

					$testoRiassunto[$i] = $orarioRiassunto[$i]->nextSibling;
					$testoRiassuntoText[$i] = $testoRiassunto[$i]->textContent;

					$visualizzazioniRiassunto[$i] = $testoRiassunto[$i]->nextSibling;
					$visualizzazioniRiassuntoText[$i] = $visualizzazioniRiassunto[$i]->textContent;

					$tagsRiassuntoElement[$i] = $visualizzazioniRiassunto[$i]->nextSibling;
					$tagsRiassunto[$i] = $tagsRiassuntoElement[$i]->childNodes;
					for ($k=0; $k < $tagsRiassunto[$i]->length; $k++) {	
						$nomeTagRiassunto = $tagsRiassunto[$i]->item($k);
						$nomeTagRiassuntoText[$k] = $nomeTagRiassunto->textContent;
					}

					$preferitiRiassuntoElement[$i] = $tagsRiassuntoElement[$i]->nextSibling;
					$preferitiRiassunto[$i] = $preferitiRiassuntoElement[$i]->childNodes;
					for ($k=0; $k < $preferitiRiassunto[$i]->length; $k++) {	
						$emailPreferitiRiassunto = $preferitiRiassunto[$i]->item($k);
						$emailPreferitiRiassuntoText[$k] = $emailPreferitiRiassunto->textContent;
					}
				}



				$newRiassunto = $doc3->createElement("riassunto");
				$newIDRiassunto = $doc3->createElement("ID", $i);		
				$newTitoloRiassunto = $doc3->createElement("titolo", $_SESSION['titoloRiassunto']);
				$newEmailStudenteRiassunto = $doc3->createElement("emailStudente", $_SESSION['email']);
				$newDataRiassunto = $doc3->createElement("data",$nowDate);
				$newOrarioRiassunto = $doc3->createElement("orario", $nowTime);
				$newTestoRiassunto = $doc3->createElement("testo", $_SESSION['testoRiassunto']);
				$newVisualizzazioniRiassunto = $doc3->createElement("visualizzazioni","0");

				$newTagsRiassunto = $doc3->createElement("tags");
				$newPreferitiRiassunto = $doc3->createElement("preferiti");

				
				
				/*Attacchiamo all'elemento principale tutti i suoi figli e i corrispettivi "nipoti"*/
				$newRiassunto->appendChild($newIDRiassunto);
				$newRiassunto->appendChild($newTitoloRiassunto);
				$newRiassunto->appendChild($newEmailStudenteRiassunto);
				$newRiassunto->appendChild($newDataRiassunto);
				$newRiassunto->appendChild($newOrarioRiassunto);
				$newRiassunto->appendChild($newTestoRiassunto);
				$newRiassunto->appendChild($newVisualizzazioniRiassunto);
				foreach ($_SESSION['tagsRiassuntoNuovo'] as $i => $value) {
					$newNomeTagRiassunto = $doc3->createElement("nomeTag", $value);
					$newTagsRiassunto->appendChild($newNomeTagRiassunto);
				}
				foreach ($_SESSION['tagsRiassuntoNuovo'] as $i => $value) {
					$newEmailPreferitiRiassunto = $doc3->createElement("emailPreferiti", $value);
					$newPreferitiRiassunto->appendChild($newEmailPreferitiRiassunto);
				}
				$newRiassunto->setAttribute("condivisione", $_SESSION['condivisioneRiassunto']);
				$root3->appendChild($newRiassunto);


				$path3 = dirname(__FILE__)."/xml-schema/riassunti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc3->save($path3); //Sovrascriviamolo
				/***/
				unset($_SESSION['aggiungiRiassunto']);
				header("Refresh:200; url=home-studente.php");
			}

			?>
            <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
                <input type="text" name="titoloRiassuntoForm" placeholder=" Inserisci un titolo" /><br /><br />
                <textarea rows="4" name="testoRiassuntoForm" placeholder=" Inserisci un contenuto"></textarea><br /><br />
				<input type="text" name="tagsRiassuntoForm" placeholder =" Inserisci tag (max 5) divisi da virgole" /><br /><br />
				<input type="radio" name="condivisioneRiassuntoForm" value="pubblico"> Pubblico
				<input type="radio" name="condivisioneRiassuntoForm" value="privato"> Privato <br />
				<input type="submit" name="submit" value="Aggiungi riassunto" />
			</form>
        </div>
	</div>
</body>
</html>

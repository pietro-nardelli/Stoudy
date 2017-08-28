<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("home-studente.css");
	</style>	
</head>
<body>
<?php
include 'functions/valoreDaStudiareOggi.php';
include 'functions/percentuale.php';
include 'functions/giorniDisponibili.php';

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
$elementi = $root->childNodes; //$elementi contiene i nodi figli di root 
/***/

/*Scorri tutti gli elementi del file XML*/
for ($i=0; $i < $elementi->length; $i++) {
	/*Se l'email derivante dal login coincide con un elemento presente nel file XML, carica tutti i dati relativi*/
	if ($_SESSION['email'] == $elementi->item($i)->firstChild->nextSibling->nextSibling->textContent){
		$elemento = $elementi->item($i); //questo sarà uno degli elementi e sarà dotato di altri figli.
		$nome = $elemento->firstChild; //ora data contiene il primo sottoelemento di $elemento[$i], ovvero data
		$nomeText = $nome->textContent;

		$cognome = $nome->nextSibling;
		$cognomeText = $cognome->textContent;

		$email = $cognome->nextSibling;
		$emailText = $email->textContent;

		$materie = $email->nextSibling;

		if ($materie->hasChildNodes()) { //Solo se ci sono materie bisogna inizializzarle!
			$materia = $materie->firstChild;
			$nomeMateria = $materia->firstChild;
			$nomeMateriaText = $nomeMateria->textContent;
				
			$valoreDaStudiare = $nomeMateria->nextSibling;
			$valoreDaStudiareText = $valoreDaStudiare->textContent;

			$oggettoStudio = $valoreDaStudiare->nextSibling;
			$oggettoStudioText = $oggettoStudio->textContent;

			$dataScadenza = $oggettoStudio->nextSibling;
			$dataScadenzaText = $dataScadenza->textContent;

			$nGiorniRipasso = $dataScadenza->nextSibling;
			$nGiorniRipassoText = $nGiorniRipasso->textContent;			

			$valoreStudiatoOggi = $nGiorniRipasso->nextSibling;			
			$valoreStudiatoOggiText = $valoreStudiatoOggi->textContent;

			$dataStudiatoOggi = $valoreStudiatoOggi->nextSibling;
			$dataStudiatoOggiText = $dataStudiatoOggi->textContent;
			
			$valoreStudiato = $dataStudiatoOggi->nextSibling;
			$valoreStudiatoText = $valoreStudiato->textContent;

			$statusText = $materia->getAttribute('status');
		} 

		$riassunti = $materie->nextSibling;
		$reputation = $riassunti->nextSibling;
		$reputationText = $reputation->textContent;

		$coins = $reputation->nextSibling;
		$coinsText = $coins->textContent;	
	}
}
?>
	<div id="lateralHomeStudente">
		<div id="logoHomeStudente">
			<a href="#">
				<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logoHome.png" style="width: 100%;"/>
			</a>
		</div>
		<div id="navigation">
			<a href="#"><img src="images/iconAggiungiMateria.png">Nuova materia</a>
			<a href="#"><img src="images/iconArchivioMaterie.png">Archivio materie</a>
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
	<?php
		/*Se abbiamo premuto sul pulsante submit, aggiorniamo ciò che si trova in valoreStudiatoOggi e data
		 *e allo stesso tempo carichiamo il file xml aggiornato in tempo reale.
		 *Ovviamente solo se è un valore numerico > 0.
		 */		
		if (is_numeric($_GET['valoreStudiatoOggiForm']) && $_GET['valoreStudiatoOggiForm'] >= 0) {		 
			//Con trim() togliamo gli spazi inseriti per sbaglio nel form (alla fine e all'inizio di ogni input)
			$valoreStudiatoOggiForm = trim($_GET['valoreStudiatoOggiForm']);
			$valoreStudiatoOggi->textContent = $valoreStudiatoOggiForm;
			$valoreStudiatoOggiText = $valoreStudiatoOggi->textContent;
			$dataStudiatoOggi = $valoreStudiatoOggi->nextSibling;
			$dataStudiatoOggi->textContent = date ("Y-m-d");
			$dataStudiatoOggiText = $dataStudiatoOggi->textContent;
			$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
			$doc->save($path); //Sovrascriviamolo
		}
		/*Se la data in cui l'ultima volta è stato inserito lo studio e la data attuale sono diverse
		 *allora aggiorna il valoreStudiato (aggiornamento dopo la mezzanotte). Inoltre
		 *azzera il valoreStudiatoOggi
		 */
		if (strcmp($dataStudiatoOggi->textContent, date("Y-m-d")) != 0){
			$valoreStudiato->textContent = $valoreStudiatoText + $valoreStudiatoOggiText;
			$valoreStudiatoText = $valoreStudiato->textContent; 		//Riassegnazione della variabile
			$valoreStudiatoOggi->textContent = 0;
			$valoreStudiatoOggiText = $valoreStudiatoOggi->textContent;	//Riassegnazione della variabile
			$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
			$doc->save($path); //Sovrascriviamolo
		}
		?>
	<div id="main">
		<div id="materia">
			<?php 
			//La percentuale si modifica real-time in base a ciò che viene inserito nel valoreStudiatoOggiForm
			$percentuale = ( ($valoreStudiatoText+$valoreStudiatoOggiText)/$valoreDaStudiareText)*100;
			$percentuale = round ($percentuale, 1); //Arrotondiamo alla prima cifra dopo la virgola
			?>
			<div id="progressBar<?php percentuale(($valoreStudiatoText+$valoreStudiatoOggiText), $valoreDaStudiareText); ?>" style="background-size: <?php echo $percentuale?>% 100%; background-repeat: no-repeat;">
				<?php echo $percentuale."% (".($valoreStudiatoText+$valoreStudiatoOggiText)."/".$valoreDaStudiareText." ".$oggettoStudioText.")"; ?>
			</div>
			<?php 
			$giorniDisponibili = giorniDisponibili ($dataScadenzaText, $nGiorniRipassoText);
			$valoreDaStudiareOggi = valoreDaStudiareOggi($giorniDisponibili ,$valoreDaStudiareText, $valoreStudiatoText);

			$percentuale = ($valoreStudiatoOggiText/$valoreDaStudiareOggi)*100;
			$percentuale = round ($percentuale, 1); //Arrotondiamo alla prima cifra dopo la virgola
			?>
			<div id="nomeMateria">
				<?php echo $nomeMateriaText; ?>
			</div>
			<div>
				L'esame è il <b><?php echo $dataScadenzaText;?></b>. 
				<br />
				Hai impostato <b><?php echo $nGiorniRipassoText ?> giorni</b> di ripasso. 
				<br />
				Hai a disposizione <b><?php echo $giorniDisponibili;?> giorni</b> di studio. <br/>
			</div> 
			<form id ="aggiungiValoreStudio" action="<?php $_SERVER["PHP_SELF"] ?>" method="get">
				<input type="text" name="valoreStudiatoOggiForm" placeholder="<?php echo "Quante/i ".$oggettoStudioText." hai fatto oggi?"; ?>"/>		
				<input type="image" name="submit" src="images/iconAggiungiValoreStudio.png" alt="Submit Form" />
			</form>

			<div id="progressBar<?php percentuale($valoreStudiatoOggiText, $valoreDaStudiareOggi); ?>" style="background-size: <?php echo $percentuale?>% 100%; background-repeat: no-repeat;">
				<?php echo $percentuale."% (".$valoreStudiatoOggiText."/".$valoreDaStudiareOggi." ".$oggettoStudioText.")"; ?>
			</div>
		</div>
	</div>
</body>
</html>


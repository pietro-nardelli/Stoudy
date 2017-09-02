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
$studenti = $root->childNodes; //$studenti contiene i nodi figli di root 
/***/

/*Scorri tutti gli studenti del file XML*/
for ($i=0; $i < $studenti->length; $i++) {
	/*Se l'email derivante dal login coincide con uno studente presente nel file XML, carica tutti i dati relativi*/
	if ($_SESSION['email'] == $studenti->item($i)->firstChild->nextSibling->nextSibling->textContent){
		$studente = $studenti->item($i); //questo sarà uno degli studenti e sarà dotato di altri figli.
		$nome = $studente->firstChild; //ora data contiene il primo sottoelemento (studente) di $studente[$i]
		$nomeText = $nome->textContent;

		$cognome = $nome->nextSibling;
		$cognomeText = $cognome->textContent;

		$email = $cognome->nextSibling;
		$emailText = $email->textContent;

		$materie = $email->nextSibling;
		$materie = $materie->childNodes;
		/*Bisogna creare un array per ogni valore presente in materia, affinchè si possa successivamente
		 *elencare ed aggiornare le materie presenti nella lista. Se si creasse un array per i soli valori
		 *testuali sarebbe impossibile aggiornarli. Ogni valore $k deve appartenere ad una materia
		 *($k=>materia k-esima).
		 */
		$nomeMateriaText = array();
		for ($k=0; $k < $materie->length; $k++) {	
			$materia = $materie->item($k);
			$nomeMateria[$k] = $materia->firstChild;
			$nomeMateriaText[$k] = $nomeMateria[$k]->textContent;
				
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

			$statusText[$k] = $materia->getAttribute('status');
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
		if (isset($_GET['valoreStudiatoOggiForm']) && is_numeric($_GET['valoreStudiatoOggiForm']) && $_GET['valoreStudiatoOggiForm'] >= 0) {		 
			//Con trim() togliamo gli spazi inseriti per sbaglio nel form (alla fine e all'inizio di ogni input)
			$valoreStudiatoOggiForm = trim($_GET['valoreStudiatoOggiForm']);
			/*Ciclando le materie da visualizzare, cicliamo anche la form che possiede l'indice $k
			*affinchè si possa determinare quale pulsante di quale materia abbiamo premuto.
			*/
			$k = $_GET['indexMateria'];
			$valoreStudiatoOggi[$k]->textContent = $valoreStudiatoOggiForm;
			$valoreStudiatoOggiText[$k] = $valoreStudiatoOggi[$k]->textContent;
			$dataStudiatoOggi[$k] = $valoreStudiatoOggi[$k]->nextSibling;
			$dataStudiatoOggi[$k]->textContent = date ("Y-m-d");
			$dataStudiatoOggiText[$k] = $dataStudiatoOggi[$k]->textContent;
			$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
			$doc->save($path); //Sovrascriviamolo
		}
		/*Se la data in cui l'ultima volta è stato inserito lo studio e la data attuale sono diverse
		 *allora aggiorna il valoreStudiato (aggiornamento dopo la mezzanotte). Inoltre
		 *azzera il valoreStudiatoOggi
		 */
		for ($k=0; $k < $materie->length; $k++) {
			if (strcmp($dataStudiatoOggi[$k]->textContent, date("Y-m-d")) != 0){
				$valoreStudiato[$k]->textContent = $valoreStudiatoText[$k] + $valoreStudiatoOggiText[$k];
				$valoreStudiatoText[$k] = $valoreStudiato[$k]->textContent; 		//Riassegnazione della variabile
				$valoreStudiatoOggi[$k]->textContent = 0;
				$valoreStudiatoOggiText[$k] = $valoreStudiatoOggi[$k]->textContent;	//Riassegnazione della variabile
				$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
				$doc->save($path); //Sovrascriviamolo
			}
		}
		?>
	<div id="main">
		<?php 
		/*Dobbiamo ciclare affinchè si possano scorrere tutte le materie presente negli array creati in precedenza*/
		for ($k=0; $k < $materie->length; $k++) { ?>
		<div id="materia">
			<?php 
			//La percentuale si modifica real-time in base a ciò che viene inserito nel valoreStudiatoOggiForm
			$percentuale = ( ($valoreStudiatoText[$k]+$valoreStudiatoOggiText[$k])/$valoreDaStudiareText[$k])*100;
			$percentuale = round ($percentuale, 1); //Arrotondiamo alla prima cifra dopo la virgola
			?>
			<div id="progressBar<?php percentuale(($valoreStudiatoText[$k]+$valoreStudiatoOggiText[$k]), $valoreDaStudiareText[$k]); ?>" style="background-size: <?php echo $percentuale?>% 100%; background-repeat: no-repeat;">
				<?php echo $percentuale."% (".($valoreStudiatoText[$k]+$valoreStudiatoOggiText[$k])."/".$valoreDaStudiareText[$k]." ".$oggettoStudioText[$k].")"; ?>
			</div>
			<?php 
			$giorniDisponibili = giorniDisponibili ($dataScadenzaText[$k], $nGiorniRipassoText[$k]);
			$valoreDaStudiareOggi = valoreDaStudiareOggi($giorniDisponibili ,$valoreDaStudiareText[$k], $valoreStudiatoText[$k]);

			$percentuale = ($valoreStudiatoOggiText[$k]/$valoreDaStudiareOggi)*100;
			$percentuale = round ($percentuale, 1);
			?>
			<div id="nomeMateria">
				<b><?php echo $nomeMateriaText[$k]; ?></b>
			</div>
			<div>
				L'esame è il <b><?php echo $dataScadenzaText[$k];?></b>. 
				<br />
				Hai impostato <b><?php echo $nGiorniRipassoText[$k] ?> giorni</b> di ripasso. 
				<br />
				Hai a disposizione <b><?php echo $giorniDisponibili;?> giorni</b> di studio. <br/>
			</div> 
			<form id ="aggiungiValoreStudio" action="<?php $_SERVER["PHP_SELF"] ?>" method="get">
				<input type="text" name="valoreStudiatoOggiForm" placeholder="<?php echo "Quante/i ".$oggettoStudioText[$k]." hai fatto oggi?"; ?>"/>		
				<!-- Il valore di indexMateria è l'indice della materia che abbiamo trovato con il for su tutto l'array-->
				<input type="hidden" name="indexMateria" value="<?php echo $k;?>" />
				<input type="image" name="submit" src="images/iconAggiungiValoreStudio.png" alt="Submit Form" />
			</form>
			<div id="progressBar<?php percentuale($valoreStudiatoOggiText[$k], $valoreDaStudiareOggi); ?>" style="background-size: <?php echo $percentuale?>% 100%; background-repeat: no-repeat;">
				<?php echo $percentuale."% (".$valoreStudiatoOggiText[$k]."/".$valoreDaStudiareOggi." ".$oggettoStudioText[$k].")"; ?>
			</div>
		</div>
		<?php }?>
	</div>
</body>
</html>


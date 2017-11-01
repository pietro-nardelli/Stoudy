<?php
//Se abbiamo premuto sul pulsante submit, aggiorniamo ciò che si trova in valoreStudiatoOggi e data
//e allo stesso tempo carichiamo il file xml aggiornato in tempo reale.
//Ovviamente solo se è un valore numerico > 0.
//Dato che questo codice è stato incluso in info-studente e info-studente è un codice incluso in tutti gli altri script,
//per aggiornare lo studio bisogna controllare che ci troviamo nella home-studente
if (basename($_SERVER['PHP_SELF']) == "home-studente.php") {
	if (isset($_GET['valoreStudiatoOggiForm']) && is_numeric($_GET['valoreStudiatoOggiForm']) && $_GET['valoreStudiatoOggiForm'] >= 0) {		 
		$valoreStudiatoOggiForm = trim($_GET['valoreStudiatoOggiForm']);
		//Ciclando le materie (planned) da visualizzare, cicliamo anche la form che possiede l'indice $k
		//affinchè si possa determinare quale pulsante di quale materia abbiamo premuto.
		$k = $_GET['indexMateria'];

		$valoreStudiatoOggi[$k]->nodeValue = $valoreStudiatoOggiForm;
		$dataStudiatoOggi[$k] = $valoreStudiatoOggi[$k]->nextSibling;
		$dataStudiatoOggi[$k]->nodeValue = date ("Y-m-d");
		
		$path = dirname(__FILE__)."/../xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
		$doc->save($path); //Sovrascriviamolo
		
		header("Location: home-studente.php");
		exit();
	}
}

//Se la data in cui l'ultima volta è stato inserito lo studio e la data attuale sono diverse
//allora aggiorna il valoreStudiato (aggiornamento dopo la mezzanotte). Inoltre
//azzera il valoreStudiatoOggi
for ($k=0; $k < $materie->length; $k++) {
	if ($statusText[$k] == 'planned') { //Si deve controllare in primis se è planned altrimenti errori
		if (strcmp($dataStudiatoOggi[$k]->textContent, date("Y-m-d")) != 0){
			//I valori che andremo ad inizializzare sono da riferisci alla data in cui è stato inserito il valore di studio
			//Quindi andremo ad aggiornare correttamente se lo studente in quella data ha studiato tutto ciò che doveva studiare, o meno
			$giorniDisponibili = giorniDisponibili ($dataStudiatoOggiText[$k], $dataScadenzaText[$k], $nGiorniRipassoText[$k]);
			$valoreDaStudiarePrec = valoreDaStudiareOggi($giorniDisponibili, $valoreDaStudiareText[$k], $valoreStudiatoText[$k]);

			//Se abbiamo studiato piu del dovuto o il necessario
			if ( $valoreStudiatoOggiText[$k] >= $valoreDaStudiarePrec) { 
				$reputationDaModificare = 3;
				$emailStudente = $_SESSION['email'];
				include ('default-code/modifica-reputation.php');
			}
			//Se abbiamo studiato qualcosa ma non tutto il necessario
			else if ($valoreStudiatoOggiText[$k] != 0) {
				$reputationDaModificare = 1;
				$emailStudente = $_SESSION['email'];
				include ('default-code/modifica-reputation.php');
			}


			$valoreStudiato[$k]->nodeValue = $valoreStudiatoText[$k] + $valoreStudiatoOggiText[$k];
			$valoreStudiatoOggi[$k]->nodeValue = 0;
			$dataStudiatoOggi[$k] = $valoreStudiatoOggi[$k]->nextSibling;
			$dataStudiatoOggi[$k]->nodeValue = date ("Y-m-d");

			
			$path = dirname(__FILE__)."/../xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
			$doc->save($path); //Sovrascriviamolo
			header("Location: home-studente.php");
			exit();
		}
	}
}

?>
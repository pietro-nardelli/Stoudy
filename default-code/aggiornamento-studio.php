<?php
//Se abbiamo premuto sul pulsante submit, aggiorniamo ciò che si trova in valoreStudiatoOggi e data
//e allo stesso tempo carichiamo il file xml aggiornato in tempo reale.
//Ovviamente solo se è un valore numerico > 0.		
if (isset($_GET['valoreStudiatoOggiForm']) && is_numeric($_GET['valoreStudiatoOggiForm']) && $_GET['valoreStudiatoOggiForm'] >= 0) {		 
	$valoreStudiatoOggiForm = trim($_GET['valoreStudiatoOggiForm']);
	//Ciclando le materie (planned) da visualizzare, cicliamo anche la form che possiede l'indice $k
	//affinchè si possa determinare quale pulsante di quale materia abbiamo premuto.
	$k = $_GET['indexMateria'];
	$valoreStudiatoOggi[$k]->textContent = $valoreStudiatoOggiForm;
	$valoreStudiatoOggiText[$k] = $valoreStudiatoOggi[$k]->textContent;
	$dataStudiatoOggi[$k] = $valoreStudiatoOggi[$k]->nextSibling;
	$dataStudiatoOggi[$k]->textContent = date ("Y-m-d");
	$dataStudiatoOggiText[$k] = $dataStudiatoOggi[$k]->textContent;
	$path = dirname(__FILE__)."/../xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
	$doc->save($path); //Sovrascriviamolo
	//Va fatto un refresh con il meta perchè abbiamo già mandato l'html in output in precedenza.
	?>

	<meta http-equiv="refresh" content="0;URL='home-studente.php'">

	<?php
	exit();
}
//Se la data in cui l'ultima volta è stato inserito lo studio e la data attuale sono diverse
//allora aggiorna il valoreStudiato (aggiornamento dopo la mezzanotte). Inoltre
//azzera il valoreStudiatoOggi
for ($k=0; $k < $materie->length; $k++) {
	if ($statusText[$k] == 'planned') { //Si deve controllare in primis se è planned altrimenti errori
		if (strcmp($dataStudiatoOggi[$k]->textContent, date("Y-m-d")) != 0){
			$valoreStudiato[$k]->textContent = $valoreStudiatoText[$k] + $valoreStudiatoOggiText[$k];
			$valoreStudiatoText[$k] = $valoreStudiato[$k]->textContent; 		//Riassegnazione della variabile
			$valoreStudiatoOggi[$k]->textContent = 0;
			$valoreStudiatoOggiText[$k] = $valoreStudiatoOggi[$k]->textContent;	//Riassegnazione della variabile
			$path = dirname(__FILE__)."/../xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
			$doc->save($path); //Sovrascriviamolo
		}
	}
}
?>
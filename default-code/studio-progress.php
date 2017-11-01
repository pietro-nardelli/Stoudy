<?php
//La percentuale si modifica real-time in base a ciò che viene inserito nel valoreStudiatoOggiForm
$percentualeTotale = ( ($valoreStudiatoText[$k]+$valoreStudiatoOggiText[$k])/$valoreDaStudiareText[$k])*100;
$percentualeTotale = round ($percentualeTotale, 1); //Arrotondiamo alla prima cifra dopo la virgola
$now = date("Y-m-d"); //Data odierna
$giorniDisponibili = giorniDisponibili ($now, $dataScadenzaText[$k], $nGiorniRipassoText[$k]);
$valoreStudiatoAdOggi = $valoreStudiatoText[$k]+$valoreStudiatoOggiText[$k]; //Valore dello studio ad oggi
$colorePercentualeTotale = percentualeColori($valoreStudiatoAdOggi, $valoreDaStudiareText[$k]);
$outputBarraSup = $percentualeTotale."% (".$valoreStudiatoAdOggi."/".$valoreDaStudiareText[$k]." ".$oggettoStudioText[$k].")";

$valoreDaStudiareOggi = valoreDaStudiareOggi($giorniDisponibili ,$valoreDaStudiareText[$k], $valoreStudiatoText[$k]);
$percentualeParziale = ($valoreStudiatoOggiText[$k]/$valoreDaStudiareOggi)*100;
$percentualeParziale = round ($percentualeParziale, 1);
$colorePercentualeParziale = percentualeColori($valoreStudiatoOggiText[$k], $valoreDaStudiareOggi);
$outputBarraInf = $percentualeParziale."% (".$valoreStudiatoOggiText[$k]."/".$valoreDaStudiareOggi." ".$oggettoStudioText[$k].")";

//Se la materia pianificata ha terminato i giorni in cui è possibile studiare (dal giorno dell'esame in poi)
//dobbiamo trasformarla in una materia non pianificata.
if ($giorniDisponibili <= 0) {
    $materia->setAttribute('status','unplanned');
    $path = dirname(__FILE__)."xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
    $doc->save($path); //Sovrascriviamolo

    ?>
    <meta http-equiv="refresh" content="0;URL=home-studente.php">
    <?php
    exit();
}
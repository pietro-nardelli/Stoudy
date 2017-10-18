<?php 
/*Stampa il colore corrispondente alla percentuale assegnata*/
function percentualeColori ($valoreParziale, $valoreTotale) {
    $percentuale = ($valoreParziale/$valoreTotale)*100;
    if ($percentuale < 25) {return "Red";}
    else if ($percentuale < 50) {return "Orange";}
    else if ($percentuale < 75) {return "Yellow";}
    else if ($percentuale < 100) {return "Green";}
    else if ($percentuale >= 100) {return "Blue";}
}
?>
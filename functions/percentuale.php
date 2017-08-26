<?php 
/*Stampa il colore corrispondente alla percentuale assegnata*/
function percentuale ($valoreParziale, $valoreTotale) {
    $percentuale = ($valoreParziale/$valoreTotale)*100;
    if ($percentuale < 25) {echo "Red";}
    else if ($percentuale < 50) {echo "Orange";}
    else if ($percentuale < 75) {echo "Yellow";}
    else if ($percentuale < 100) {echo "Green";}
    else if ($percentuale >= 100) {echo "Blue";}
}
?>
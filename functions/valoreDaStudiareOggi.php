<?php
/*Calcola la quantità giornaliera di studio in base al piano di studi inserito*/
function valoreDaStudiareOggi($giorniDisponibili, $valoreDaStudiare, $valoreStudiato){                      
      $valoreDaStudiareRimanente = ($valoreDaStudiare - $valoreStudiato);
      //Ceil($x) arrotonda alla prima cifra decimale superiore, fatto per non avere zero come risultato
      return ceil($valoreDaStudiareRimanente / $giorniDisponibili); 
}
?>

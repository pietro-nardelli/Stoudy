<?php
/*Calcola la quantità giornaliera di studio in base al piano di studi inserito*/
function valoreDaStudiareOggi($giorniDisponibili, $valoreDaStudiare, $valoreStudiato){                      
      $valoreDaStudiareRimanente = ($valoreDaStudiare - $valoreStudiato);
      //Ceil($x) arrotonda alla prima cifra decimale superiore, fatto per non avere zero come risultato
      //Ovviamente su una cifra tra -1 e 0 questo rimanda 0. Per questo motivo in quel caso bisogna 
      //far si che la materia passi da pianificata a non pianificata.
      return ceil($valoreDaStudiareRimanente / $giorniDisponibili); 
}
?>

<?php
/*Calcola la quantità giornaliera di studio in base al piano di studi inserito*/
function valoreDaStudiareOggi($giorniDisponibili, $valoreDaStudiare, $valoreStudiato){                      
      $valoreDaStudiareRimanente = ($valoreDaStudiare - $valoreStudiato);
      return round($valoreDaStudiareRimanente / $giorniDisponibili);
}
?>

<?php
/*Calcola la quantità giornaliera di studio in base al piano di studi inserito*/
function valoreDaStudiareOggi($dataScadenza, $nGiorniRipasso, $valoreDaStudiare, $valoreStudiato){
      $now = date("Y-m-d");               //Data odierna
      $date1=date_create($now);           //Creiamo la data odierna, trasformandola nel formato date
      $date2=date_create($dataScadenza);  //Idem per la data di scadenza
      $diff=date_diff($date1,$date2);     //Effettuiamo la differenza tra le due date
      $giorni = $diff->format('%a');      //La variabile giorni sarà un numero intero risultante dalla differenza precedente
            
      $giorniDisponibili = $giorni - $nGiorniRipasso;                         
      $valoreDaStudiareRimanente = ($valoreDaStudiare - $valoreStudiato);
      return round($valoreDaStudiareRimanente / $giorniDisponibili);
}
?>

<?php 
function giorniDisponibili($dataScadenza, $nGiorniRipasso){
      $now = date("Y-m-d");               //Data odierna
      $date1=date_create($now);           //Creiamo la data odierna, trasformandola nel formato date
      $date2=date_create($dataScadenza);  //Idem per la data di scadenza
      $diff=date_diff($date1,$date2);     //Effettuiamo la differenza tra le due date
      $giorni = $diff->format('%R%a');    //La variabile giorni sarà un numero intero risultante dalla differenza precedente
      if (is_numeric($nGiorniRipasso)) {  //Se abbiamo inserito giorni di ripasso allora si possono sottrarre    
            $giorniDisponibili = $giorni - $nGiorniRipasso;    
      }          
      else {
            $giorniDisponibili = $giorni;  
      }
      return $giorniDisponibili;    
}
?>
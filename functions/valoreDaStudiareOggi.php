<?php
function valoreDaStudiareOggi($dataScadenza, $nGiorniRipasso, $valoreDaStudiare, $valoreStudiato){
      $now = date("Y-m-d");
      $date1=date_create($now);
      $date2=date_create($dataScadenza);
      $diff=date_diff($date1,$date2);
      $giorni = $diff->format('%a');

      $giorniDisponibili = $giorni - $nGiorniRipasso;
      $valoreDaStudiareRimanente = ($valoreDaStudiare - $valoreStudiato);
      return round($valoreDaStudiareRimanente / $giorniDisponibili);
}
?>

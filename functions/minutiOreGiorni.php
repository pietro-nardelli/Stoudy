<?php
function minutiOreGiorni($dataPrecedente) {
    $now = date('Y-m-d H:i:s');
    $differenza = strtotime($now) - strtotime($dataPrecedente);

    if ($differenza < 60) {
        return $differenza." secondi fa";
    }

    $differenzaMinuti = floor($differenza/60);
    if ($differenza >= 60 && $differenza < 3600) {
        if ($differenzaMinuti == 1) {
            return $differenzaMinuti." minuto fa";
        }
        else {
            return $differenzaMinuti." minuti fa";
        }
    }

    $differenzaOre = floor($differenza/3600);
    if ($differenza >= 3600 && $differenza < 3600*24) {
        if ($differenzaOre == 1) {
            return $differenzaOre." ora fa";
        }
        else {
            return $differenzaOre." ore fa";
        }
        
    }

    $differenzaGiorni = floor($differenza/(3600*24) );
    if ($differenza >= 3600*24) {
        if ($differenzaGiorni == 1) {
            return $differenzaGiorni." giorno fa";
        }
        else {
            return $differenzaGiorni." giorni fa";
        }
    }

    
}
?>
<?php
//Prima di stampare tutto, controlliamo che la data di accesso sia uguale a quella attuale, in caso contrario azzeriamo coins
$dataUltimoAccessoText = $studente->getAttribute('dataUltimoAccesso');
if (strcmp($dataUltimoAccessoText, date("Y-m-d")) != 0){
    
    if ($reputationText < 10 ) {
        $studente->getElementsByTagName("coins")->item(0)->nodeValue = 0;
    }
    else if ($reputationText >= 10 ) {
        $studente->getElementsByTagName("coins")->item(0)->nodeValue = 1;
    }
    else if ($reputationText >= 50 ) {
        $studente->getElementsByTagName("coins")->item(0)->nodeValue = 2;
    }
    else if ($reputationText >= 100 ) {
        $studente->getElementsByTagName("coins")->item(0)->nodeValue = 3;
    }
    else if ($reputationText >= 500 ) {
        $studente->getElementsByTagName("coins")->item(0)->nodeValue = 4;
    }
    else if ($reputationText >= 1000 ) {
        $studente->getElementsByTagName("coins")->item(0)->nodeValue = 5;
    }

    $studente->setAttribute("dataUltimoAccesso", date("Y-m-d"));
    $path = dirname(__FILE__)."/../xml-schema/studenti.xml"; 
    $doc->save($path);

    //Va fatto un refresh con il meta perchè abbiamo già mandato l'html in output in precedenza.
    ?> <meta http-equiv="refresh" content="0;URL='<?= $_SERVER['PHP_SELF'] ?>'"><?php
    exit();
}
?>

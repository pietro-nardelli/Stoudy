<?php
/*Scorri tutti gli studenti del file XML*/
for ($i=0; $i < $studenti->length; $i++) {
    if ($emailStudente == $studenti->item($i)->firstChild->nextSibling->nextSibling->textContent){
        $studente = $studenti->item($i);

        $oldReputation = $studente->getElementsByTagName("reputation")->item(0)->textContent;
        $studente->getElementsByTagName("reputation")->item(0)->nodeValue = $oldReputation + $reputationDaModificare;

    }
}

?>
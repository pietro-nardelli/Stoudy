<?php
//Controlliamo se il riassunto è nostro oppure no
if (strcasecmp($_SESSION['email'], $emailStudenteRiassuntoText[$IDGet]) == 0) {
    $riassuntoProprio = true;
}

//Se non è nostro ed è privato
if (!$riassuntoProprio && (strcasecmp($condivisioneRiassuntoText[$IDGet], "privato") == 0) ) {
    ?>
    <div id='message'>
        <img src="images/iconMessage.png">
        <div>
            <strong>Questo riassunto è privato e non può essere visualizzato.</strong>
            <br />
            Ti stiamo reindirizzando...
        </div>
    </div>
    <?php
    header("refresh:3; url=home-studente.php");
    exit();
}

$nowTime = date("H:i:s"); //Ora odierna
$nowDate = date("Y-m-d"); //Data odierna
$nowTimeTotal = strtotime($nowTime." ".$nowDate); //Va fatto per ottenere la differenza di orari
$timeTotal = strtotime($dataRiassuntoText[$IDGet]." ".$orarioRiassuntoText[$IDGet]); //Idem sopra
$diffHours = ($nowTimeTotal - $timeTotal)/3600; //Questa è la differenza in ore tra la data del riassunto e la data attuale




//Controlliamo se abbiamo visualizzato il riassunto
for ($k=0; $k < $riassuntiVisualizzati->length; $k++) {	
    $riassuntoIDVisualizzato[$k] = $riassuntiVisualizzati->item ($k);
    $riassuntoIDVisualizzatoText[$k] = $riassuntoIDVisualizzato[$k]->textContent;
    if ($IDGet == $riassuntoIDVisualizzatoText[$k]) {
        $visualizzato = true;
    }
}

//Controlliamo se il riassunto è tra i preferiti
for ($k=0; $k < $riassuntiPreferiti->length; $k++) {	
    $riassuntoIDPreferito[$k] = $riassuntiPreferiti->item ($k);
    $riassuntoIDPreferitoText[$k] = $riassuntoIDPreferito[$k]->textContent;
    if ($IDGet == $riassuntoIDPreferitoText[$k]) {
        $indicePreferito = $k;
        $preferito = 1; //Se uguale a 1 allora la flag indica che è già tra i preferiti, viceversa non lo è.
    }
}


if (!$riassuntoProprio) {
    if (!$visualizzato) {
        if ($coinsText == 0) {
            //Se il riassunto è stato inserito da più di 24 ore, non si hanno coin e non lo abbiamo già visualizzato nè è nostro...
            if ($diffHours > 24) {
                ?>
                <div id='message'>
                    <img src="images/iconMessage.png">
                    <div>
                        <strong>Questo riassunto non può essere visualizzato.</strong>
                        <br />
                        Non hai abbastanza coin.
                    </div>
                </div>
                <?php
            exit();
            }
            else { //Se il riassunto è stato inserito entro 24 ore: visualizziamolo e aggiungiamolo ai riassunti visti
                $newRiassuntoIDVisualizzato = $doc->createElement("riassuntoIDVisualizzato", $IDGet);
                $riassuntiVisualizzatiElement->insertBefore($newRiassuntoIDVisualizzato);		
                $path = dirname(__FILE__)."/../xml-schema/studenti.xml"; 
                $doc->save($path);
                
                $visualizzazioniRiassunto[$IDGet]->nodeValue= $visualizzazioniRiassuntoText[$IDGet]+1;
                $path3 = dirname(__FILE__)."/../xml-schema/riassunti.xml"; 
                $doc3->save($path3); 
                header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet); //Aggiorniamo per visualizzarlo correttamente con il DOM aggiornato
            }
        }
        else { //Se si hanno abbastanza coin per visualizzarlo, visualizziamolo e aggiungiamolo ai riassunti visti.
            $coins->nodeValue = $coins->textContent -1; //Togliamo un coin

            $newRiassuntoIDVisualizzato = $doc->createElement("riassuntoIDVisualizzato", $IDGet);
            $riassuntiVisualizzatiElement->insertBefore($newRiassuntoIDVisualizzato);		
            $path = dirname(__FILE__)."/../xml-schema/studenti.xml"; 
            $doc->save($path);

            $visualizzazioniRiassunto[$IDGet]->nodeValue = $visualizzazioniRiassuntoText[$IDGet]+1; //Aggiungiamo uno alle visualizzazioni
            
            
            $path3 = dirname(__FILE__)."/../xml-schema/riassunti.xml"; 
            $doc3->save($path3);
            header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet); //Aggiorniamo per visualizzarlo correttamente con il DOM aggiornato

        }
    }
} //In tutti gli altri casi possiamo visualizzare il riassunto! E' nostro o è stato visualizzato

if ($riassuntoProprio) {
    if (isset($_GET['elimina'])) {
        foreach ($IDRiassuntoLista as $count => $id) {
            $riassunto = $riassunti->item($count); 
            //Eliminiamo il riassunto dal riassunti.xml e da creati se abbiamo premuto "elimina riassunto"
            //Bisogna eliminarlo anche da tags.xml ma non da segnalazioni.xml (a quello ci pensa già la home-admin.php)
            if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
                if ($IDGet == $IDRiassuntoText[$id]) {
                    $numeroPreferiti = $preferitiRiassunto[$id]->length; //Questo serve per togliere la reputation dall'utente

                    //Eliminiamolo da riassunti.xml
                    unlink ($linkDocumentoRiassuntoText[$id]);
                    $riassunto->parentNode->removeChild($riassunto);
                    $path3 = dirname(__FILE__)."/../xml-schema/riassunti.xml"; 
                    $doc3->save($path3);
                    
                    //Eliminiamolo da tags
                    for ($k=0; $k < $tags->length; $k++) {	
                        $tag = $tags->item($k); 
                        //Controlla se c'è una sottostringa nel nomeTagText[$k]
                        $riassuntoIDLista = $tag->getElementsByTagName('riassuntoID');
                        foreach ($riassuntoIDLista as $key => $value) { //Inseriamo nell'array riassuntoIDTrovato ognuno degli ID del tag ricercato
                            $riassuntoIDTag = $riassuntoIDLista->item($key)->textContent;
                            if ($riassuntoIDTag == $IDGet) {
                                $riassuntoIDLista->item($key)->parentNode->removeChild($riassuntoIDLista->item($key));
                                $path2 = dirname(__FILE__)."/../xml-schema/tags.xml"; 
                                $doc2->save($path2);
                            }
                        }
                    }

                    //Eliminiamolo dai riassunti creati
                    for ($k=0; $k < $riassuntiCreati->length; $k++) {	
                        $riassuntoIDCreato[$k] = $riassuntiCreati->item($k);
                        if ($IDGet == $riassuntoIDCreatoText[$k]) {
                            $riassuntoCreato = $studente->getElementsByTagName('riassuntoIDCreato')->item($k);
                            $riassuntoCreato->parentNode->removeChild($riassuntoCreato); //Serve perchè altrimenti da errore!

                            //Aggiungiamo 1 alla reputation dell'autore
                            $reputationDaModificare = -1-$numeroPreferiti;
                            $emailStudente = $emailStudenteRiassuntoText[$IDGet];
                            include ('default-code/modificaReputation.php');


                            $path = dirname(__FILE__)."/../xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
                            $doc->save($path); //Sovrascriviamolo 
                        }
                    }
                    header('Location: home-studente.php');
                    exit();


                }
            }
        }
    }
}

//Se abbiamo premuto il pulsante preferito
if (isset($_GET['preferito']) && !$riassuntoProprio) {
    //Il pulsante rimanda 1 quando non è ancora tra i preferiti
    if ($_GET['preferito'] == 1 && !$preferito) { //Aggiungiamo il riassunto ai preferiti

        //Aggiungiamo 1 alla reputation dell'autore
        $reputationDaModificare = 1;
        $emailStudente = $emailStudenteRiassuntoText[$IDGet];
        include ('default-code/modificaReputation.php');

        $newRiassuntoIDPreferito = $doc->createElement("riassuntoIDPreferito", $IDGet);
        $riassuntiPreferitiElement->insertBefore($newRiassuntoIDPreferito);					
        $path = dirname(__FILE__)."/../xml-schema/studenti.xml"; 
        $doc->save($path);

        $newEmailPreferiti = $doc3->createElement("emailPreferiti", $_SESSION['email']);
        $preferitiRiassuntoElement[$IDGet]->insertBefore($newEmailPreferiti);
        $path3 = dirname(__FILE__)."/../xml-schema/riassunti.xml"; 
        $doc3->save($path3);

        header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);
    }
    //Il pulsante rimanda 0 quando lo abbiamo tra i preferiti
    else if ($_GET['preferito'] == 0 && $preferito) { //Togliamo il riassunto dai preferiti
        


        $riassuntoPreferito = $studente->getElementsByTagName('riassuntoIDPreferito')->item($indicePreferito);
        $riassuntoPreferito->parentNode->removeChild($riassuntoPreferito); //Serve perchè altrimenti da errore!

        //Aggiungiamo 1 alla reputation dell'autore
        $reputationDaModificare = -1;
        $emailStudente = $emailStudenteRiassuntoText[$IDGet];
        include ('default-code/modificaReputation.php');

        $path = dirname(__FILE__)."/../xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
        $doc->save($path); //Sovrascriviamolo 

        $emailPreferito = $root3->getElementsByTagName('emailPreferiti')->item($indiceEmailPreferito);
        $emailPreferito->parentNode->removeChild($emailPreferito);
        $path3 = dirname(__FILE__)."/../xml-schema/riassunti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
        $doc3->save($path3); //Sovrascriviamolo 

        header('Location: visualizza-riassunto.php?IDRiassunto='.$IDGet);
    }
}
?>
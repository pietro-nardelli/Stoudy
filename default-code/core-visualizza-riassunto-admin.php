<?php
if (!empty ($descrizioneRiassuntoText[$IDGet])) {
    ?>
    <div style='margin-left: 5px;'><?= nl2br($descrizioneRiassuntoText[$IDGet]) ?></div>
    <br />
    <?php
}
?>
<embed src="<?= $linkDocumentoRiassuntoText[$IDGet] ?>" width="100%" height="600px" type='application/pdf'>
<br /><br />
<div id="informazioniRiassunto">
    <div>
        <?php
        $xmlString = "";
        foreach (file("xml-schema/studenti.xml") as $node) { 
            $xmlString .= trim($node); 
        }
        $doc = new DOMDocument(); 
        $doc->loadXML($xmlString); 
        $root = $doc->documentElement; 
        $studenti = $root->childNodes;
        //Assegna all'email un nome e un cognome...
        for ($i=0; $i < $studenti->length; $i++) {
            if ($emailStudenteRiassuntoText[$IDGet] == $studenti->item($i)->firstChild->nextSibling->nextSibling->textContent){
                $studenteAutore = $studenti->item($i);
                
                $nome = $studenteAutore->firstChild; 
                $nomeStudenteRiassuntoText = $nome->textContent;

                $cognome = $nome->nextSibling;
                $cognomeStudenteRiassuntoText = $cognome->textContent;

            }
        }
        ?>
        Creato da <b><?= $nomeStudenteRiassuntoText ?> <?= $cognomeStudenteRiassuntoText ?></b>
        in data <b><?= $dataRiassuntoText[$IDGet] ?></b> alle ore <b><?= $orarioRiassuntoText[$IDGet] ?></b>
    </div>
    <div>
        Il riassunto ha <b><?= $visualizzazioniRiassuntoText[$IDGet] ?> visualizzazioni</b>
        e <b><?= $numeroPreferiti ?> preferiti</b>.
    </div>
    <table id="tabellaTagDescrizioni">
        <tr><th colspan="2">Tag e descrizioni</th></tr>
        <?php
        foreach ($tagsRiassunto[$IDGet] as $l => $value) {
            $nomeTagRiassunto = $tagsRiassunto[$IDGet]->item($l);
            $nomeTagRiassuntoText[$l] = $nomeTagRiassunto->textContent;
            for ($k=0; $k < $tags->length; $k++) {	
                //Confrontiamo ogni tag inserito con quelli già presenti in tags.xml
                if (strcasecmp ($nomeTagRiassuntoText[$l], $nomeTagText[$k]) == 0 && !empty($descrizioneTagText[$k])) {
                    $indiceTrovato[$l] = -1;
                    ?>
                    <tr><td><a id='tagAnteprima' href='#'><?= $nomeTagRiassuntoText[$l] ?></a></td><td><?= $descrizioneTagText[$k] ?></td></tr>
                    <?php
                    break; //Usiamo il break perchè tanto abbiamo trovato ciò che cercavamo nel nostro for annidato
                }
            }
        }
        //Confrontiamo ogni tag inserito con quelli già presenti in tags.xml: caso in cui tag non è presente.
        foreach ($tagsRiassunto[$IDGet] as $p => $value) {
            for ($k=0; $k < $tags->length; $k++) {	
                $descrizioneTagText[$k] = $descrizioneTag[$k]->textContent;
                //Dobbiamo allora semplicemente creare un tag nuovo
                if (empty($indiceTrovato[$p]) ||  $indiceTrovato[$p] != -1) {
                    ?>
                    <tr><td><a id='tagAnteprima' href='#'><?= $nomeTagRiassuntoText[$p] ?></a></td><td><i>descrizione mancante...</i></td></tr>
                    <?php
                    break; //Usiamo il break perchè tanto abbiamo trovato ciò che cercavamo nel nostro for annidato
                }
            }
        }
        ?>
    </table>    
    <br />
</div>
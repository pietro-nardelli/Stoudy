<br />
<?= nl2br($descrizioneRiassuntoText[$IDGet])."<br />";?>
<br />
<embed src="<?= $linkDocumentoRiassuntoText[$IDGet] ?>" width="100%" height="500" type='application/pdf'>
<br /><br />
<hr style='width: 95%;'/><hr id='lista' />
<?= $emailStudenteRiassuntoText[$IDGet] ?> <br />
<hr id='lista' />
<b>Data </b><?= $dataRiassuntoText[$IDGet] ?> <b>Ora </b><?= $orarioRiassuntoText[$IDGet] ?><br /> 
<hr id='lista' />
<b>Tags</b>:
<?php
foreach ($tagsRiassunto[$IDGet] as $key=>$value) { 
    echo $nomeTagRiassuntoText[$key]." | ";
}
?>
<br /> <hr id='lista' />
<b>Visualizzazioni</b>: <?= $visualizzazioniRiassuntoText[$IDGet] ?><br />
<b>Preferiti</b>: <?= $numeroPreferiti ?> <hr id='lista' />
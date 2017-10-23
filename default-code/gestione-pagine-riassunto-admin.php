<?php
$pageLength = 3;

//Qui non abbiamo la necessità di modificare l'ordine dell'array

if (isset($_GET['next'])) {
    $first = $_GET['next'];
}
else {
    $first = 0;
}
if (sizeof($valueIDArray) - $first < $pageLength ) {
    $last = sizeof($valueIDArray);
}
else {
    $last = $first+$pageLength;
}
for ($key = $first; $key < $last ; $key++) { 
    $valueID = $valueIDArray[$key];
    echo "<a id ='titoloRiassuntoTrovato' href='visualizza-riassunto-admin.php?IDRiassunto=".urlencode($valueID)."' >".$titoloRiassuntoText[$valueID]."</a>";
    echo "<span id ='visualizzazioniPreferitiRiassuntoTrovato'>".$visualizzazioniRiassuntoText[$valueID]." <img src='images/iconViews.png' /> ".$preferitiRiassunto[$valueID]->length." <img src='images/iconFavorites.png' /></span>";
    echo "<br /><span id='emailRiassuntoTrovato'><i> Creato da ".$emailStudenteRiassuntoText[$valueID]." il ".$dataRiassuntoText[$valueID]." alle ore ".$orarioRiassuntoText[$valueID]."</i></span>";
    foreach ($tagsRiassunto[$valueID] as $j => $value) {
        $nomeTagRiassunto = $tagsRiassunto[$valueID]->item($j);
        $nomeTagRiassuntoText[$j] = $nomeTagRiassunto->textContent;
        echo "<a id='tagRiassuntoTrovato' href='#'>".$nomeTagRiassuntoText[$j]."</a>";
    }
    echo  "(".$emailStudenteLista[$valueID]->length." segnalazioni)<br/>";
    echo "<hr />";
}
$totPagine =  ceil ( (sizeof($valueIDArray) / $pageLength));
$paginaAttuale = ($first / $pageLength)+1;

?>
<div id="pagineRiassuntoTrovato">
    <?php
    if ($paginaAttuale == 1 && $paginaAttuale == $totPagine) {
        echo "pagina ".$paginaAttuale." / ".$totPagine;		
    }
    else if ($paginaAttuale == 1) {
        echo "pagina ".$paginaAttuale." / ".$totPagine;												
        echo "<a id ='pagineNextRiassuntoTrovato' href='home-admin.php?next=".$last."' >successivo</a>";														

    }
    else if ($paginaAttuale < $totPagine) {
        echo "<a id ='paginePrevRiassuntoTrovato' href='home-admin.php?next=".($first-$pageLength)."' >precedente</a>";
        echo "pagina ".$paginaAttuale." / ".$totPagine;												
        echo "<a id ='pagineNextRiassuntoTrovato' href='home-admin.php?next=".$last."' >successivo</a>";														
    }
    else {
        echo "<a id ='paginePrevRiassuntoTrovato' href='home-admin.php?next=".($first-$pageLength)."' >precedente</a>";						
        echo "pagina ".$paginaAttuale." / ".$totPagine;	
    }
    ?>
</div>
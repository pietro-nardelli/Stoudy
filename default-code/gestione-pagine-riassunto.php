<?php
$pageLength = 20;
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
    echo "<a id ='titoloRiassuntoTrovato' href='visualizza-riassunto.php?IDRiassunto=".urlencode($valueID)."' >".$riassuntoTrovatoTitolo[$key]."</a>";
    echo "<span id ='visualizzazioniPreferitiRiassuntoTrovato'>".$riassuntoTrovatoVisualizzazioni[$key]." <img src='images/iconViews.png' /> ".$riassuntoTrovatoPreferiti[$key]." <img src='images/iconFavorites.png' /></span>";
    echo "<br /><span id='emailRiassuntoTrovato'><i> Creato da ".$riassuntoTrovatoEmail[$key]." il ".$riassuntoTrovatoData[$key]." alle ore ".$riassuntoTrovatoOrario[$key]."</i></span>";
    foreach ($tagsRiassunto[$valueID] as $j => $value) {
        $nomeTagRiassunto = $tagsRiassunto[$valueID]->item($j);
        $nomeTagRiassuntoText[$j] = $nomeTagRiassunto->textContent;
        echo "<a id='tagRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($nomeTagRiassuntoText[$j])."'>".$nomeTagRiassuntoText[$j]."</a>";
    }
    echo "<hr />";
}
$totPagine =  round ( (sizeof($valueIDArray) / $pageLength));
$paginaAttuale = ($first / $pageLength)+1;

?>
<div id="pagineRiassuntoTrovato">
    <?php
    if ($paginaAttuale == 1 && $paginaAttuale == $totPagine) {
        echo "pagina ".$paginaAttuale." / ".$totPagine;		
    }
    else if ($paginaAttuale == 1) {
        echo "pagina ".$paginaAttuale." / ".$totPagine;												
        echo "<a id ='pagineNextRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".$last."' >successivo</a>";														

    }
    else if ($paginaAttuale < $totPagine) {
        echo "<a id ='paginePrevRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".($last-$pageLength-1)."' >precedente</a>";
        echo "pagina ".$paginaAttuale." / ".$totPagine;												
        echo "<a id ='pagineNextRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".$last."' >successivo</a>";														
    }
    else {
        echo "<a id ='paginePrevRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato=".urlencode($_GET['tagRicercato'])."&next=".($last-$pageLength-1)."' >precedente</a>";						
        echo "pagina ".$paginaAttuale." / ".$totPagine;	
    }
    ?>
</div>
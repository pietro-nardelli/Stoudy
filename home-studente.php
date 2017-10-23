<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("css/home-studente.css");
	</style>	
</head>
<body>

<?php 
include 'functions/valoreDaStudiareOggi.php';
include 'functions/percentualeColori.php';
include 'functions/giorniDisponibili.php';
include("default-code/info-studente.php");
include("default-code/aggiornamento-studio.php");

?>
<div id="main">
	<?php 
	//Dobbiamo ciclare affinchè si possano scorrere tutte le materie presente negli array creati in precedenza
	foreach($materie as $k=>$v) { 
		//Se la materia è PLANNED, allora visualizza il piano di studi
		if ($statusText[$k] == 'planned') {
			include('default-code/studio-progress.php');
			?>
			<div id="materia">
				<div id="progressBar<?= $colorePercentualeTotale ?>" style="background-size: <?= $percentualeTotale ?>% 100%; background-repeat: no-repeat;">
					<?= $outputBarraSup ?>
				</div>
				<div id="nomeMateria">
					<b><?= $nomeMateriaText[$k] ?></b>
				</div>
				<div>
					L'esame è il <b><?php echo $dataScadenzaText[$k];?></b>. 
					<br />
					Hai impostato <b><?php echo $nGiorniRipassoText[$k] ?> giorni</b> di ripasso. 
					<br />
					Hai a disposizione <b><?php echo $giorniDisponibili;?> giorni</b> di studio. <br/>
				</div> 
				<form id ="aggiungiValoreStudio" action="<?php $_SERVER["PHP_SELF"] ?>" method="get">
					<input type="text" name="valoreStudiatoOggiForm" placeholder="<?php echo "Quante/i ".$oggettoStudioText[$k]." hai fatto oggi?"; ?>"/>		
					<!-- Il valore di indexMateria è l'indice della materia che abbiamo trovato con il for su tutto l'array-->
					<input type="hidden" name="indexMateria" value="<?php echo $k;?>" />
					<input type="image" name="submit" src="images/iconAggiungiValoreStudio.png" alt="Submit Form" />
				</form>
				<a href="aggiungi-riassunto.php?nomeMateria=<?= $nomeMateriaText[$k] ?>" id="aggiungiRiassuntoPlanned">	
					Aggiungi riassunto
				</a>
				<a href="elimina-materia.php?nomeMateria=<?= $nomeMateriaText[$k] ?>" id="eliminaMateriaPlanned">
					Elimina materia
				</a>
				<div id="progressBar<?= $colorePercentualeParziale ?>" style="background-size: <?= $percentualeParziale ?>% 100%; background-repeat: no-repeat;">
					<?= $outputBarraInf ?>
				</div>
			</div>
		<?php
		}
		//Se la materia è UNPLANNED, allora visualizza solo il pulsante 'aggiungi riassunto'
		if ($statusText[$k] == 'unplanned') {
			?>
			<div id="materia">
				<div id="nomeMateria">
					<b><?= $nomeMateriaText[$k] ?></b>
				</div>
				<div>
					Questa materia non possiede un programma di studio. 
					<br/>
					Se ti va, puoi ancora <a href="#" style="color: black; ">pianificarla</a>.
				</div>
				<a href="aggiungi-riassunto.php?nomeMateria=<?= $nomeMateriaText[$k] ?>" id="aggiungiRiassuntoUnplanned">	
					Aggiungi riassunto
				</a>
				<a href="elimina-materia.php?nomeMateria=<?= $nomeMateriaText[$k] ?>" id="eliminaMateriaUnplanned">
					Elimina materia
				</a>
				<br /><br /> <!-- Necessari per avere spazio bianco sotto il pulsante -->
			</div>
			<?php
		}
	}
	
	?>
</div>

</body>
</html>


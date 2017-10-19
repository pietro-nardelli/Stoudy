<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("css/riassunto.css");
	</style>	
</head>
<body>

<?php 
$IDGet = $_GET['IDRiassunto'];
$visualizzato = false;
$preferito = 0;
$riassuntoProprio = false;

include("default-code/info-studente.php");
include("default-code/caricamento-riassunti-xml.php");
?>

<div id="main">
<?php 
//Se id è valido e se abbiamo usato il "motore" di ricerca...
if (isset($IDGet) && !empty($IDRiassunto[$_GET['IDRiassunto']])) { 
	$numeroPreferiti = $preferitiRiassunto[$IDGet] ->length;
	include("default-code/gestione-eventi-riassunto.php")
	//Da qui in poi visualizziamo il riassunto
	?>
	<div id="visualizzaRiassunto">
		<div id="nomeMateria">
			<b><?= $titoloRiassuntoText[$IDGet] ?></b>
		</div>
		<?php
		include("default-code/core-visualizza-riassunto.php")
		?>
		<div id="opzioniRiassunto">
		<?php
		//Se la variabile preferiti è empty || se è = a 0 allora visualizza questo.
		//Altrimenti visualizza la stellina che diventa da gialla scura a chiara e la scritta toglilo dai preferiti
		//In entrambi i casi bisogna procedere all'eliminazione o all'aggiunta tramite DOM
		if (!$preferito) { 
			?>
			<a id="aggiungiAiPreferiti" href="visualizza-riassunto.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&preferito=".urlencode(1).""; ?>">Aggiungilo ai preferiti</a>
			<?php
		}
		else {
			?>
			<a id="togliloDaiPreferiti" href="visualizza-riassunto.php?<?php echo "IDRiassunto=".urlencode($IDGet)."&preferito=".urlencode(0).""; ?>">Toglilo dai preferiti</a>
			<?php
		}
		?>
			<a id="segnalaRiassunto" href="segnala-riassunto.php?<?php echo "IDRiassunto=".urlencode($IDGet).""; ?>">Segnala riassunto </a>
	</div>
	<?php } 
	else {
		?>
		Impossibile visualizzare un riassunto se non viene fornito un ID valido
		<?php
	}
	?>
</div>
</body>
</html>
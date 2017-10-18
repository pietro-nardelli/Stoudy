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
$trovatoEsatto = -1;
$tagRicercato = $_GET['tagRicercato'];
include("default-code/info-studente.php");


/* Inizializziamo il file TAGS.XML */
$xmlString2 = ""; 
foreach (file("xml-schema/tags.xml") as $node2) { 
	$xmlString2 .= trim($node2); 
}
$doc2 = new DOMDocument();
$doc2->loadXML($xmlString2); 
$root2 = $doc2->documentElement; 
$tags = $root2->childNodes; 
$trovato = false;
	for ($k=0; $k < $tags->length; $k++) {	
    	$tag = $tags->item($k); 
		$nomeTag[$k] = $tag->firstChild; 
		$nomeTagText[$k] = $nomeTag[$k]->textContent;
		$estrattoTag[$k] = $nomeTag[$k]->nextSibling;
		$estrattoTagText[$k] = $estrattoTag[$k]->textContent;
        //Controlla se c'è una sottostringa nel nomeTagText[$k]
        if (!empty ($_GET['tagRicercato'])) {
			if (strcasecmp($nomeTagText[$k], $_GET['tagRicercato']) == 0) { //Controlliamo se il tag cercato è ESATTAMENTE un tag
				$trovatoEsatto = $k; //Associamo alla flag l'indice del tag presente in lista così lo usiamo dopo per mostrare l'estratto
			}
        }
    }
/***/


?>
<div id="main">
		<?php 
		if ($trovatoEsatto != -1) {
			?>
			<div id="modificaEstratto">
				<div id="risultatoRicercaAlto">
					Modifica estratto per il tag <a id='tagRiassuntoTrovato' href='cerca-riassunti.php?tagRicercato="<?= $tagRicercato?>"'> <?= $tagRicercato ?></a>
					<br />
				</div>
				<?php
				if (isset($_POST['submit'])) {
					if (strlen($_POST['modificaEstratto']) > 500 || strlen($_POST['modificaEstratto']) == 0) {
						?>
						<p style="color: red;">Non può essere più lungo di 500 caratteri oppure vuoto.</p>
						<?php
					}
					else{
						$_SESSION['modificaEstratto'] = $_POST['modificaEstratto'];
						$_SESSION['tagRicercato'] = $tagRicercato;
						header('Location: revisione-estratto.php');
						exit();
					}
				}
				?>
				<form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
					<textarea rows="2" name="modificaEstratto"><?php echo $estrattoTagText[$trovatoEsatto]; ?></textarea><br /><br />
					<input type="submit" name="submit" value="Modifica estratto" />
				</form>
			</div>
		<?php
		}
		else {
			echo "Tag non trovato..";
		}

		?>
</div>
</body>
</html>
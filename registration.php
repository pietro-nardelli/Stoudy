<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("css/index.css");
	</style>
</head>
<body>
	<div id="topper">
		<div>
			<a href="index.html">
		<!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
				<img src="images/logo.png" style="width: 100%;"/>
			</a>
		</div>
	</div>
	<?php 
	error_reporting(E_ALL);
	$db_name = "lweb7";
	$table_name = "studenti";
	include("default-code/connection.php");

	session_start();

	//Se non si connette al server, usciamo subito
	if (mysqli_connect_errno()) { 
		?>
		<div id='message'>
			<img src="images/iconMessage.png">
			<div>
				<strong>Impossibile connettersi al server. Per favore riprovare più tardi.</strong>
			</div>
		</div>
		<?php
		exit();
		}

	//Se non viene selezionato alcun database, allora forniamo un errore. 
	if (!mysqli_select_db ($connection, $db_name)) { 
		?>
		<div id='message'>
			<img src="images/iconMessage.png">
			<div>
				<strong>Impossibile connettersi al server. Per favore riprovare più tardi.</strong>
			</div>
		</div>
		<?php
		exit();
	}
	?>
	<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="registrazioneLogin">
		<div>
		Crea il tuo account
		</div>
		<p>
		Tramite Stoudy potrai aumentare la tua produttività, focalizzando la tua energia sullo studio. <br />
		Compila il form sottostante per registrarti! 
		</p>
		<!-- placeholder : ci permette di rendere il valore del text semitrasparente e invisibile quando ci scriviamo sopra-->
		<?php
		if (isset($_POST['submit'])) {				
			if (!empty($_POST['nome']) && !empty($_POST['cognome']) && !empty($_POST['email']) && !empty($_POST['password'])) { /*Va utilizzato questo anzichè isset perchè il post è sempre set, anche se empty*/
				//Con trim() togliamo gli spazi inseriti per sbaglio nel form (alla fine e all'inizio di ogni input)
				$nome = trim($_POST['nome']);
				$cognome = trim($_POST['cognome']);

				$email = trim($_POST['email']);
				$email = filter_var($email, FILTER_SANITIZE_EMAIL);

				$password = trim($_POST['password']);									
				$sql = "SELECT email FROM studenti WHERE email ='".$email."'";
				$queryResult = mysqli_query($connection, $sql);

				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					?>
					<p style="color: red;">Indirizzo email non valido!</p>
					<?php
				}

				else if ($queryResult && mysqli_num_rows($queryResult) ) { //Se l'indirizzo email è già presente nel database
					?>
					<p style="color: red;">Indirizzo email già registrato!</p>
					<?php
				}
				else { //Altrimenti possiamo aggiungerlo al database senza problemi
					$sql = "INSERT INTO studenti (email, password) VALUES ('".$email."', '".$password."');";
					$queryResult = mysqli_query($connection, $sql);
					if (!$queryResult) {
						?>
						<p style="color: red;">Problemi con la registrazione, per favore riprovare più tardi.!</p>
						<?php
					}
					else { //Possiamo aggiungere lo studente nel file xml di riferimento
					
						/***Procedura standard per inizializzare il file XML***/
						$xmlString = ""; //Inizializziamo la variabile xmlString
						foreach (file("xml-schema/studenti.xml") as $node) { //Per ogni riga del file xml...
							$xmlString .= trim($node); //Aggiungi alla stringa $xmlString la riga $node
						}
						$doc = new DOMDocument(); //Creiamo l'oggetto documento DOM e lo assegnamo a $doc
						$doc->loadXML($xmlString); //$doc essendo un oggetto DOMDocument possiede il parser XML (metodo nella classe DOMDocument) che lo facciamo girare sulla stringa $xmlString
						$root = $doc->documentElement; //la chiamata a DocumentElement restituisce la radice del documento DOM
						$elementi = $root->childNodes; //$elementi contiene i nodi figli di root 
						/***/
						
						/***Procedura standard per l'inserimento di un nuovo elemento al DOM***/
						/*Creiamo un elemento per ogni nodo che vogliamo aggiungere al DOM*/
						$newStudente = $doc->createElement("studente");
						$newNome = $doc->createElement("nome", $nome);
						$newCognome = $doc->createElement("cognome", $cognome);
						$newEmail = $doc->createElement ("email", $email);
						$newMaterie = $doc->createElement ("materie");
						$newRiassunti = $doc->createElement ("riassunti");
						$newCreati = $doc->createElement ("creati");
						$newVisualizzati = $doc->createElement("visualizzati");
						$newPreferiti = $doc->createElement("preferiti");
						$newReputation = $doc->createElement("reputation", "0");
						$newCoins = $doc->createElement("coins", "0");
						
						/*Attacchiamo all'elemento principale tutti i suoi figli e i corrispettivi "nipoti"*/
						$newStudente->appendChild($newNome);
						$newStudente->appendChild($newCognome);
						$newStudente->appendChild($newEmail);
						$newStudente->appendChild($newMaterie);
						$newStudente->appendChild($newRiassunti);
						$newRiassunti->appendChild($newCreati);
						$newRiassunti->appendChild($newVisualizzati);
						$newRiassunti->appendChild($newPreferiti);
						$newStudente->appendChild($newReputation);
						$newStudente->appendChild($newCoins);

						$newStudente->setAttribute("dataUltimoAccesso", date("Y-m-d"));

						/*Inseriamo il nodo che abbiamo creato, prima del primo elemento della lista di elementi dopo root*/
						$root->insertBefore($newStudente);
						$path = dirname(__FILE__)."/xml-schema/studenti.xml"; //Troviamo un percorso assoluto al file xml di riferimento
						$doc->save($path); //Sovrascriviamolo
						/***/
						
						$_SESSION['emailDaRegistrazione'] = $email;
						header("Location: login.php");
						exit();
					}
				}
			}	
			else {	//Se alcuni campi non sono stati compilati...
				?>
				<p style="color: red;">E necessario compilare tutti i campi.</p>
				<?php
			} 
		}
		?>
		<input type="text" name="nome" placeholder=" Nome" <?php if (isset($_POST['nome'])){ echo 'value="'.$_POST['nome'].'"'; } ?> />
		<input type="text" name="cognome" placeholder=" Cognome" <?php if (isset($_POST['cognome'])){ echo 'value="'.$_POST['cognome'].'"'; } ?> /> <br />		
		<input type="text" name="email" placeholder=" Indirizzo email" <?php if (isset($_POST['email'])){ echo 'value="'.$_POST['email'].'"'; } ?> /> <br />				
		<input type="password" name="password" placeholder=" Password" <?php if (isset($_POST['password'])){ echo 'value="'.$_POST['password'].'"'; } ?>/> <br />		
		<input type="submit" name="submit" value="REGISTRATI" />
	</form>	
	
</body>
</html>

<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("index.css");
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
	$db_name = "stoudy";
	$table_name = "studenti";
	$connection = new mysqli("127.0.0.1", "root", "");

	//Se non si connette al server, usciamo subito
	if (mysqli_connect_errno()) { 
		?>
		<h1>Impossibile collegarsi al server!
			<div style="font-size: 75%; font-weight: normal;">Per favore riprovare pi� tardi.</div>
		</h1>
		<?php
		exit();
	}

	//Se non viene selezionato alcun database, allora forniamo un errore. 
	if (!mysqli_select_db ($connection, $db_name)) { 
		?>
		<h1>Problemi nel selezionare il database!
			<div style="font-size: 75%; font-weight: normal;">Per favore riprovare pi� tardi.</div>
		</h1>
		<?php
		exit();
	}
	?>
	<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="registrazioneLogin">
		<div>
		Entra nel tuo account
		</div>
		<p>
		Il tuo account e' il portale per realizzare i tuoi sogni. <br />
		Entrando avrai modo di apprendere in maniera efficace e rimanere al passo con il tuo programma di studi!
		</p>
		<!-- placeholder : ci permette di rendere il valore del text semitrasparente e invisibile quando ci scriviamo sopra-->
		<?php
		if (isset($_POST['submit'])) {				
			if (!empty($_POST['email']) && !empty($_POST['password'])) { /*Va utilizzato questo anzich� isset perch� il post � sempre set, anche se empty*/
				//Con trim() togliamo gli spazi inseriti per sbaglio nel form (alla fine e all'inizio di ogni input)
				$email = trim($_POST['email']);
				$password = trim($_POST['password']);							
				$sql = "SELECT email, password FROM studenti WHERE email ='".$email."' AND password ='".$password."'";
				$queryResult = mysqli_query($connection, $sql);
				if ( mysqli_num_rows($queryResult) ) { //Se l'indirizzo email e la password sono presenti nel database
					/*Avviamo la sessione per mantere la login nella home-studente*/
					session_start();
					$_SESSION['email']= $email;
					$_SESSION['accessoPermesso']= 1000;
				
					header("Location: home-studente.php");
					exit();
				}
				else { //Altrimenti abbiamo sbagliato qualcosa nel login
						echo '<p style="color: red;">Email o password non validi</p>';
					}
				}	
			else {	//Se alcuni campi non sono stati compilati...
				echo '<p style="color: red;">E necessario compilare tutti i campi.</p>';
			} 
		} ?>
		<input type="text" name="email" placeholder=" Indirizzo email"/> <br />				
		<input type="password" name="password" placeholder=" Password" /> <br />		
		<input type="submit" name="submit" value="ENTRA" />
	</form>	
	
</body>
</html>

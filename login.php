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
			<strong>Problemi nel selezionare il database. Per favore riprovare più tardi.</strong>
		</div>
	</div>
	<?php
		exit();
	}
	?>
	<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="registrazioneLogin">
		<div>
		Entra nel tuo account
		</div>
		<p>
		Tramite il tuo account potrai rimanere al passo con il tuo programma di studi. <br />
		Entrando avrai inoltre la possibilità di creare e pubblicare i riassunti delle tue materie o leggere quelli degli altri utenti!
		</p>
		<!-- placeholder : ci permette di rendere il valore del text semitrasparente e invisibile quando ci scriviamo sopra-->
		<?php
		if (isset($_POST['submit'])) {				
			if (!empty($_POST['email']) && !empty($_POST['password'])) { /*Va utilizzato questo anzichè isset perchè il post è sempre set, anche se empty*/
				//Con trim() togliamo gli spazi inseriti per sbaglio nel form (alla fine e all'inizio di ogni input)
				$email = trim($_POST['email']);
				$password = trim($_POST['password']);							
				$sql = "SELECT email, password FROM studenti WHERE email ='".$email."' AND password ='".$password."'";
				$queryResult = mysqli_query($connection, $sql);

				$sql2 = "SELECT email, password FROM admins WHERE email ='".$email."' AND password ='".$password."'";
				$queryResult2 = mysqli_query($connection, $sql2);
				if ( $queryResult && mysqli_num_rows($queryResult) ) { //Se l'indirizzo email e la password sono presenti nel database
					/*Avviamo la sessione per mantere la login nella home-studente*/
					session_start();
					$_SESSION['email']= $email;
					$_SESSION['accessoPermesso']= 1000;
				
					header("Location: home-studente.php");
					exit();
				}
				else if ($queryResult2 && mysqli_num_rows($queryResult2)) {
					/*Avviamo la sessione per mantere la login nella home-admin*/
					session_start();
					$_SESSION['email']= $email;
					$_SESSION['accessoPermessoAdmin']= 1000;
				
					unset($_SESSION['emailDaRegistrazione']);
					header("Location: home-admin.php");
					exit();
				}
				else { //Altrimenti abbiamo sbagliato qualcosa nel login
						echo '<p style="color: red;">Email o password non validi</p>';
					}
				}	
			else {	//Se alcuni campi non sono stati compilati...
				echo '<p style="color: red;">E necessario compilare tutti i campi.</p>';
			} 
		}

		?>
		<input type="text" name="email" placeholder=" Indirizzo email" <?php if (isset($_POST['email'])){ echo 'value="'.$_POST['email'].'"'; } ?><?php if (isset($_SESSION['emailDaRegistrazione'])){ echo 'value="'.$_SESSION['emailDaRegistrazione'].'"'; } ?> /> <br />				
		<input type="password" name="password" placeholder=" Password" <?php if (isset($_POST['password'])){ echo 'value="'.$_POST['password'].'"'; } ?> /> <br />		
		<input type="submit" name="submit" value="ENTRA" />
	</form>	
	
</body>
</html>

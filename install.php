<?php echo'<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>PHP - 13</title>
</head>
<body>
<?php 
$db_name = "lweb7";
$table_name = "studenti";
include("default-code/connection.php");
//Se non si connette al server, usciamo subito
if (mysqli_connect_errno()) { 
	?>
	<div>Impossibile collegarsi al server! <?php echo mysqli_connect_error();?></div>
	<?php
	exit();
	}

$queryCreazioneDatabase = "CREATE DATABASE $db_name";

$db = mysqli_query($connection, $queryCreazioneDatabase); //Mandiamo la query di creazione database..

//Se non viene selezionato alcun database, allora forniamo un errore. 
if (!mysqli_select_db ($connection, $db_name)) { 
	?>
	<div>Problemi nel selezionare il database!</div>
	<?php
	exit();
}


	
//Database selezionato: adesso passiamo creare/selezionare la tabella STUDENTI

$sql = "CREATE TABLE if not exists ".$table_name." (";
$sql .= "email VARCHAR(255) NOT NULL primary key, ";
$sql .= "password VARCHAR(50) NOT NULL";
$sql .= ")";

//Controlliamo che la query per la creazione della tabella sia andata a buon fine...
if (!mysqli_query($connection, $sql)) {
	?>
	<div>Creazione tabella non riuscita.</div>
	<?php
	exit();
	}			




//Database selezionato: adesso passiamo creare/selezionare la tabella ADMINS	
$table_name2 = "admins";

$sql2 = "CREATE TABLE if not exists ".$table_name2." (";
$sql2 .= "email VARCHAR(255) NOT NULL primary key, ";
$sql2 .= "password VARCHAR(50) NOT NULL";
$sql2 .= ")";

//Controlliamo che la query per la creazione della tabella sia andata a buon fine...
if (!mysqli_query($connection, $sql2)) {
	?>
	<div>Creazione tabella non riuscita.</div>
	<?php
	exit();
	}	
	
	
echo "Creazione database andata a buon fine.";

/*
for ($i = 0; $i < 3; $i++) {
	$sql = "INSERT INTO studenti (email, password) VALUES ('studente".$i."@gmail.com', 'studente".$i."');";
	$queryResult = mysqli_query($connection, $sql);
	if (!$queryResult) {
		$error = "<div>Problemi con l'aggiunta del film.</div>";
		exit();
	}
}
*/
for ($i = 1; $i < 3; $i++) {
	$sql = "INSERT INTO admins (email, password) VALUES ('admin".$i."@gmail.com', 'admin".$i."');";
	$queryResult = mysqli_query($connection, $sql);
	if (!$queryResult) {
		$error = "<div>Problemi con l'aggiunta del film.</div>";
		exit();
	}
}

echo "<div>Popolazione database...completata!</div>";	
exit();
?>

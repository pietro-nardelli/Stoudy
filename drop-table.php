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
$table_name2 = "admins";
$connection = new mysqli("localhost", "root", ""); //$connection = new mysqli("localhost", "lweb7", "lweb7");
//Se non si connette al server, usciamo subito
if (mysqli_connect_errno()) { 
	?>
	<div>Impossibile collegarsi al server! <?php echo mysqli_connect_error();?></div>
	<?php
	exit();
}

//Se non viene selezionato alcun database, allora forniamo un errore. 
if (!mysqli_select_db ($connection, $db_name)) { 
	?>
	<div>Problemi nel selezionare il database!</div>
	<?php
	exit();
}
    
$sql = "DROP TABLE ".$table_name.";";

//Controlliamo che la query per la creazione della tabella sia andata a buon fine...
if (!mysqli_query($connection, $sql)) {
	?>
	<div>Eliminazione tabella non riuscita.</div>
	<?php
	exit();
}		
    	
$sql2 = "DROP TABLE ".$table_name2.";";

//Controlliamo che la query per la creazione della tabella sia andata a buon fine...
if (!mysqli_query($connection, $sql2)) {
	?>
	<div>Eliminazione tabella non riuscita.</div>
	<?php
	exit();
	}			

echo "Eliminazione tabelle...completato!";
?>
</body>
</html>
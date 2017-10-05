<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Stoudy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Your tool for studying." />
	<!--<meta name="keywords" content="">-->

	<style type="text/css">
		@import url("riassunto.css");
	</style>	
</head>
<body>
<?php
session_start();

if (!isset($_SESSION['accessoPermesso'])) {
    header('Location: login.php');
}
error_reporting(E_ALL);
$db_name = "stoudy";
$table_name = "studenti";
$connection = new mysqli("127.0.0.1", "root", "");

//Se non si connette al server, usciamo subito
if (mysqli_connect_errno()) { 
    ?>
    <h1>Impossibile collegarsi al server!
        <div style="font-size: 75%; font-weight: normal;">Per favore riprovare più tardi.</div>
    </h1>
    <?php
    exit();
}

//Se non viene selezionato alcun database, allora forniamo un errore. 
if (!mysqli_select_db ($connection, $db_name)) { 
    ?>
    <h1>Problemi nel selezionare il database!
        <div style="font-size: 75%; font-weight: normal;">Per favore riprovare più tardi.</div>
    </h1>
    <?php
    exit();
}

if (!empty($_GET['IDRiassunto']) && !empty($_GET['emailStudente'])) { 
    $sql = "SELECT email FROM admins";
    $queryResult = mysqli_query($connection, $sql);
    if ( mysqli_num_rows($queryResult) ) { 
        $i = 0;
        while ($row = mysqli_fetch_row($queryResult)) { //Questo permette di immagazzinare ogni risultato da mysql in un array...
            $admins[$i] = $row[0]; //...sta a noi poi prendere quel valore e metterlo in un array che si mantenga anche dopo il while
            $i++;
       }
       $indexAdmin = rand(0, $i-1); //Scegliamo un admin a caso tra quelli presenti nella tabella admins.sql
       echo $admins[$indexAdmin];
    }
    else { //Altrimenti abbiamo sbagliato qualcosa nel login
            echo "Problemi nel segnalare il riassunto";
        }
    }	
else {	//Se alcuni campi non sono stati compilati...
    echo "Impossibile segnalare riassunto";
} 


/*Inizializziamo il file segnalazioni.xml*/
/*$xmlString4 = ""; 
foreach (file("xml-schema/riassunti.xml") as $node4) { 
	$xmlString4 .= trim($node4); 
}
$doc4 = new DOMDocument(); 
$doc4->loadXML($xmlString4); 
$root4 = $doc4->documentElement; 
$segnalazioni = $root4->childNodes;

for ($i=0; $i < $segnalazioni->length; $i++) {
    
}*/






?>
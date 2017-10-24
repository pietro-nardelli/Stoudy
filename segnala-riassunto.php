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
session_start();
$trovato = false;

if (!isset($_SESSION['accessoPermesso'])) {
    header('Location: login.php');
}
error_reporting(E_ALL);
$db_name = "lweb7";
$table_name = "admins";
$connection = new mysqli("127.0.0.1", "root", ""); //$connection = new mysqli("localhost", "lweb7", "lweb7");

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

if (!empty($_GET['IDRiassunto'])) { 
    $sql = "SELECT email FROM admins";
    $queryResult = mysqli_query($connection, $sql);
    if ( mysqli_num_rows($queryResult) ) { 
        $i = 0;
        while ($row = mysqli_fetch_row($queryResult)) { //Questo permette di immagazzinare ogni risultato da mysql in un array...
            $admins[$i] = $row[0]; //...sta a noi poi prendere quel valore e metterlo in un array che si mantenga anche dopo il while
            $i++;
       }
       $indexAdmin = rand(0, $i-1); //Scegliamo un admin a caso tra quelli presenti nella tabella admins.sql
    }
    else { //Altrimenti abbiamo sbagliato qualcosa nel login
        ?>
        <div id='message'>
            <img src="images/iconMessage.png">
            <div>
                <strong>Problemi nel segnalare il riassunto.</strong>
                <br />
                Ti stiamo reindirizzando...
            </div>
        </div>
        <?php
        header("refresh:3; url=visualizza-riassunto.php?IDRiassunto=".$_GET['IDRiassunto']."");
    }
}	
else {	//Se alcuni campi non sono stati compilati...
    ?>
    <div id='message'>
        <img src="images/iconMessage.png">
        <div>
            <strong>Impossibile segnalare il riassunto!</strong>
            <br />
            Ti stiamo reindirizzando...
        </div>
    </div>
    <?php
    header("refresh:3; url=visualizza-riassunto.php?IDRiassunto=".$_GET['IDRiassunto']."");
} 

include("default-code/caricamento-segnalazioni-xml.php");
for ($i=0; $i < $segnalazioni->length; $i++) {
    $segnalazione = $segnalazioni->item ($i); //Dobbiamo lasciare questi due elementi perchè altrimenti non funzionerebbe l'appendChild
    foreach ($emailStudenteLista[$riassuntoIDText[$i]] as $key => $value) { 
        //Se l'ID del riassunto coincide, quel riassunto è stato già segnalato
        if ( !strcasecmp ($_GET['IDRiassunto'], $riassuntoIDText[$i]) ) {
            $trovato = true; 
            //Controlliamo se lo studente non abbia già fatto una segnalazione per quel riassunto, in tal caso errore.
            if (!strcasecmp ($emailStudenteLista[$riassuntoIDText[$i]]->item($key)->textContent, $_SESSION['email'])) {
                ?>
                <div id='message'>
                    <img src="images/iconMessage.png">
                    <div>
                        <strong>Hai già segnalato questo riassunto.<br /> Un admin se ne sta già occupando.</strong>
                        <br />
                        Ti stiamo reindirizzando...
                    </div>
                </div>
                <?php
                header("refresh:3; url=visualizza-riassunto.php?IDRiassunto=".$_GET['IDRiassunto']."");
                exit();
            }
        }
    }
    if ($trovato) { //Se la segnalazione è stata fatta ma lo studente ancora non l'ha segnalato aggiungiamo emailStudente a quel riassunto
        $newEmailStudente = $doc4->createElement("emailStudente", $_SESSION['email']);
        $segnalazione->appendChild($newEmailStudente);

        $path4 = dirname(__FILE__)."/xml-schema/segnalazioni.xml"; //Troviamo un percorso assoluto al file xml di riferimento
        $doc4->save($path4); //Sovrascriviamolo
        echo "E' stata emessa una segnalazione per quel riassunto";
        header("refresh:3; url=visualizza-riassunto.php?IDRiassunto=".$_GET['IDRiassunto']."");
        exit();
    }
}

//Nel caso in cui non sia stato trovato nulla e abbiamo ciclato per tutte le segnalazioni...
$newSegnalazione = $doc4->createElement("segnalazione");
$newRiassuntoID = $doc4->createElement("riassuntoID", $_GET['IDRiassunto']);
$newEmailAdmin = $doc4->createElement("emailAdmin", $admins[$indexAdmin]);
$newEmailStudente = $doc4->createElement("emailStudente", $_SESSION['email']);
            
$newSegnalazione->appendChild($newRiassuntoID);
$newSegnalazione->appendChild($newEmailAdmin);	
$newSegnalazione->appendChild($newEmailStudente);	
   
$root4->appendChild($newSegnalazione);
   
$path4 = dirname(__FILE__)."/xml-schema/segnalazioni.xml"; //Troviamo un percorso assoluto al file xml di riferimento
$doc4->save($path4); //Sovrascriviamolo

?>
<div id='message'>
    <img src="images/iconMessage.png">
    <div>
        <strong>Grazie per aver segnalato il riassunto.<br /> Un admin se ne occuperà nel più breve tempo possibile.</strong>
        <br />
        Ti stiamo reindirizzando...
    </div>
</div>
<?php
header("refresh:3; url=visualizza-riassunto.php?IDRiassunto=".$_GET['IDRiassunto']."");
exit();
?>
</body>
</html>
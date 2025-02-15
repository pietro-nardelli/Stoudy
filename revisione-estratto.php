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

if (!isset($_SESSION['accessoPermesso'])) {
    header('Location: login.php');
}
error_reporting(E_ALL);
$db_name = "lweb7";
$table_name = "admins";
include("default-code/connection.php");

//Se non si connette al server, usciamo subito
if (mysqli_connect_errno()) { 
?>
<div id='message'>
    <img src="images/iconMessage.png">
    <div>
        <strong>Impossibile collegarsi al server.</strong>
        <br />
        Per favore riprovare più tardi.
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
        <strong>Problemi nel selezionare il database.</strong>
        <br />
        Per favore riprovare più tardi.
    </div>
</div>
<?php
exit();
}

if (!empty($_SESSION['tagRicercato'])) { 
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
                <strong>Problemi nel revisionare l'estratto.</strong>
                <br />
                Per favore riprovare più tardi.
                <br />
                Ti stiamo reindirizzando...
            </div>
        </div>
        <?php
        header("refresh:3; url=cerca-riassunti.php?tagRicercato=".$_SESSION['tagRicercato']."");
        exit();
    }
}	
else {	//Se alcuni campi non sono stati compilati...
    ?>
    <div id='message'>
        <img src="images/iconMessage.png">
        <div>
            <strong>Impossibile revisionare l'estratto.</strong>
        </div>
    </div>
    
    <?php
    unset($_SESSION['tagRicercato']);
    exit();
} 

include("default-code/caricamento-revisioni-xml.php");
for ($i=0; $i < $revisioni->length; $i++) {
    //Se il tag è già presente nelle revisioni allora non può essere revisionato nuovamente
    if ( !strcasecmp ($_SESSION['tagRicercato'], $nomeTagRevisioneText[$i]) ) {
        ?>
        <div id='message'>
            <img src="images/iconMessage.png">
            <div>
                <strong>Mi dispiace ma è stata già emessa una revisione da un altro studente per quell'estratto.</strong>
                <br />
                Ti stiamo reindirizzando...
            </div>
        </div>
        <?php
        header("refresh:3; url=modifica-estratto.php?tagRicercato=".$_SESSION['tagRicercato']."");
        exit();
    }
}

//Nel caso in cui non sia stato trovato nulla e abbiamo ciclato per tutte le revisioni...
$newRevisione = $doc5->createElement("revisione");
$newNomeTagRevisione = $doc5->createElement("nomeTag", $_SESSION['tagRicercato']);
$newModificaEstratto = $doc5->createElement("modificaEstratto", $_SESSION['modificaEstratto']);
$newEmailAdminRevisione = $doc5->createElement("emailAdmin", $admins[$indexAdmin]);
$newEmailStudenteRevisione = $doc5->createElement("emailStudente", $_SESSION['email']);
            
$newRevisione->appendChild($newNomeTagRevisione);
$newRevisione->appendChild($newEmailAdminRevisione);	
$newRevisione->appendChild($newModificaEstratto);	
$newRevisione->appendChild($newEmailStudenteRevisione);	
   
$root5->appendChild($newRevisione);
   
$path5 = dirname(__FILE__)."/xml-schema/revisioni.xml"; //Troviamo un percorso assoluto al file xml di riferimento
$doc5->save($path5); //Sovrascriviamolo

?>
<div id='message'>
    <img src="images/iconMessage.png">
    <div>
        <strong>E' stata emessa una revisione per quell'estratto.</strong>
        <br />
        Ti stiamo reindirizzando...
    </div>
</div>
<?php
header("refresh:3; url=cerca-riassunti.php?tagRicercato=".$_SESSION['tagRicercato']."");
unset($_SESSION['modificaEstratto']);
unset($_SESSION['tagRicercato']);
exit();
?>

</body>
</html>
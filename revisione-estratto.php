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
            echo "Problemi nel revisionare l'estratto";
        }
}	
else {	//Se alcuni campi non sono stati compilati...
    echo "Impossibile revisionare l'estratto";
} 


/*Inizializziamo il file revisioni.xml*/
$xmlString5 = ""; 
foreach (file("xml-schema/revisioni.xml") as $node5) { 
	$xmlString5 .= trim($node5); 
}
$doc5 = new DOMDocument(); 
$doc5->loadXML($xmlString5); 
$root5 = $doc5->documentElement; 
$revisioni = $root5->childNodes;

for ($i=0; $i < $revisioni->length; $i++) {
    $revisione = $revisioni->item ($i);
    $nomeTagRevisione[$i] = $revisione->firstChild; 
    $nomeTagRevisioneText[$i] = $nomeTagRevisione[$i]->textContent;

    $emailAdmin[$i] = $nomeTagRevisione[$i]->nextSibling;
    $emailAdminText[$i] = $emailAdmin[$i]->textContent;

    $modificaEstratto[$i] = $emailAdmin[$i]->nextSibling;
    $modificaEstrattoText[$i] = $modificaEstratto[$i]->textContent;

    $emailStudente[$i] = $emailAdmin[$i]->nextSibling;
    $emailStudenteText[$i] = $emailStudente[$i]->textContent;
    //Se il tag è già presente nelle revisioni allora non può essere revisionato nuovamente
    if ( !strcasecmp ($_SESSION['tagRicercato'], $nomeTagRevisioneText[$i]) ) {
        echo "E' stata già emessa una revisione per quel tag";
        header("refresh:3; url=cerca-riassunti.php?tagRicercato=".$_SESSION['tagRicercato']."");
        unset($_SESSION['tagRicercato']);
        exit();
    }
}

//Nel caso in cui non sia stato trovato nulla e abbiamo ciclato per tutte le revisioni...
$newRevisione = $doc5->createElement("revisione");
$newNomeTagRevisione = $doc5->createElement("nomeTag", $_SESSION['tagRicercato']);
$newModificaEstratto = $doc5->createElement("modificaEstratto", $_SESSION['modificaEstratto']);
$newEmailAdmin = $doc5->createElement("emailAdmin", $admins[$indexAdmin]);
$newEmailStudente = $doc5->createElement("emailStudente", $_SESSION['email']);
            
$newRevisione->appendChild($newNomeTagRevisione);
$newRevisione->appendChild($newEmailAdmin);	
$newRevisione->appendChild($newModificaEstratto);	
$newRevisione->appendChild($newEmailStudente);	
   
$root5->appendChild($newRevisione);
   
$path5 = dirname(__FILE__)."/xml-schema/revisioni.xml"; //Troviamo un percorso assoluto al file xml di riferimento
$doc5->save($path5); //Sovrascriviamolo

?>
E' stata emessa una revisione per quell'estratto del tag
<?php
header("refresh:3; url=cerca-riassunti.php?tagRicercato=".$_SESSION['tagRicercato']."");
unset($_SESSION['tagRicercato']);
exit();
?>

</body>
</html>
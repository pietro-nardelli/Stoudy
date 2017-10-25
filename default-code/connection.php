<?php
//Questa serve per non modificare ogni volta due cartelle diverse quando si vuole apportare modificare alla repository remota
//caricata su lweb7

if (!strcasecmp ($_SERVER['REMOTE_ADDR'], '127.0.0.1') || !strcasecmp($_SERVER['REMOTE_ADDR'], '::1')) {
    $connection = new mysqli("127.0.0.1", "root", "");
}
else {
    $connection = new mysqli("localhost", "lweb7", "lweb7");
}
?>

// Connessione al server
<?php
/*
Variabili utili alle (e) connessione al DB. 
File incluso in ogni pagina che richieda l'interazione col DB.
*/

$host="localhost";
$user="root";
$pwd="";
$dbname="CakeLab"; 

$mysqli = new mysqli($host, $user, $pwd, $dbname);

if ($mysqli->connect_errno) {
    echo "Errore di connessione al server MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

/* USO DEBUG
if ($connetti != 0) echo "Connessione riuscita";
*/


?>

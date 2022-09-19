<?php

// $servername = "localhost";
// $database = "practicas";
// $userDB = "practicas";
// $passDB = "20Universal22@";

$servername = "localhost";
$database = "albaran";
$userDB = "root";
$passDB = "";

$con_possible = true;

try {

    $con = mysqli_connect($servername, $userDB, $passDB, $database)
        or die ("No se ha podido establecer conexión a ".$servername."\n".mysqli_connect_error()); 

} catch(Exception $ex) {
    $con_possible = false;
}

?>
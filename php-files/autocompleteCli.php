<?php

include('connect.php');
include('pepareQuery.php');

$result = '';
$query = '';
$salida = "<option value='none'>Seleccione una empresa</option>";

if (isset($_POST['dato'])) {

    // Remove special char from the list
    //$q = $con -> real_escape_string($_POST['consulta']);
    $q = prepare($_POST['dato']);

    $query = "SELECT `id`, `cliente` FROM `cliente` WHERE `empresa` LIKE '" . $q . "'";

    // Execute only if there are results
    $resultado = mysqli_query($con, $query);

    if ($resultado->num_rows > 0) {

        // Row found -> dynamically add the results to the ul as a list item
        $cont = 0;
        while ($fila = $resultado->fetch_assoc()) {

            $salida .= '<option value="'.$fila['id'].'">'.$fila['cliente'].'</option>';
            $cont++; // Only increment for each non duplicated result

        }
    }
}

echo $salida;

$con->close();

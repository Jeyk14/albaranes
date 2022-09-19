<?php 

    include('connect.php');

    $result = '';
    $query = '';
    $salida = '';

    // If th user is typing
    if(isset($_POST['consulta'])){

        // Remove special char from the list
        //$q = $con -> real_escape_string($_POST['consulta']);
        $q = $_POST['consulta'];

        $query = "SELECT empresa FROM registro WHERE empresa LIKE '%".$q."%' ORDER BY empresa";

        // Execute only if there are results
        $resultado = mysqli_query($con, $query);
    

        if($resultado -> num_rows > 0){

            // The unordered list that'll contain the elements found in the database
            $salida .= ' <ul> ';

            // Dynamically add the results to the ul as a list item
            $cont = 0;
            while($fila = $resultado -> fetch_assoc()){
                $salida .= '<li onclick="seleccionar('.$cont.')" class="autocomplete-item">'.$fila['empresa'].'</li>';
                $cont ++;
            }

            $salida .= '</ul>';

        } else {

            // No data found -> do nothing

        }
    }

    echo $salida;

    $con -> close();

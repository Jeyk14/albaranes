<?php 

    include('connect.php');
    include('pepareQuery.php');

    $result = '';
    $query = '';
    $salida = '';

    // If th user is typing
    if(isset($_POST['consulta'])){

        // Remove special char from the list
        //$q = $con -> real_escape_string($_POST['consulta']);
        $q = str_replace("'", "\'", $_POST['consulta']);
        //$q = prepare($_POST['consulta']);

        //$query = 'SELECT reg.id, cli.cliente, cli.empresa, reg.direccion, reg.fecha, reg.hora, reg.comentarios, reg.firma FROM `registro` AS reg INNER JOIN `cliente` as cli ON reg.id_cliente = cli.id';
        $query = "SELECT `empresa` FROM cliente WHERE `empresa` LIKE '%".$q."%' GROUP BY `empresa` ORDER BY `empresa`";

        // Execute only if there are results
        $resultado = mysqli_query($con, $query);

        // To prevent duplicates
        $lastData = '';
    

        if($resultado -> num_rows > 0){

            // The unordered list that'll contain the elements found in the database
            $salida .= ' <ul> ';

            // Dynamically add the results to the ul as a list item
            $cont = 0;
            while($fila = $resultado -> fetch_assoc()){

                if($lastData == $fila['empresa']){
                    // This $fila is the same as the last one -> do not print
                }else{
                    $salida .= '<li onclick="seleccionar('.$cont.');" class="autocomplete-item">'.$fila['empresa'].'</li>';
                    $cont ++; // Only increment for each non duplicated result
                }

                $lastData = $fila['empresa'];

            }

            $salida .= '</ul>';

        } else {

            // No data found -> do nothing

        }
    }

    echo $salida;

    $con -> close();

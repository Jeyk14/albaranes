<?php 
    session_start();
    $idUsuario = $_SESSION['IDusr']; // User logged -> get the user ID and proceed

    // Get the user data from the DB
    include("connect.php");

    $query = 'SELECT nombre, apellido, email FROM `usuario` WHERE `id` = '.$_POST['IDusr'];
    $result = mysqli_query($con, $query);
    $row_count = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result); // load the columns into an array
    $IDusr = $row['id'];
    $nombre = $row['nombre'];
    $email = $row['email'];
?>
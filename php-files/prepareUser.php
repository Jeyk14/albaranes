<?php
$IDusr = $_SESSION['IDusr']; // User logged -> get the user ID and proceed
$query = 'SELECT nombre, apellido, email, nivel FROM `usuario` WHERE `id` = '.$IDusr;
$result = mysqli_query($con, $query);
$row_count = mysqli_num_rows($result);
$row = mysqli_fetch_array($result); // load the columns into an array

$nombre = $row['nombre'];
$apellido = $row['apellido'];
$email = $row['email'];
$level = $row['nivel'];
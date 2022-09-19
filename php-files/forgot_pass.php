<?php 

session_start();
include("connect.php");

$query = "SELECT nombre, apellido, pass, email FROM usuario WHERE id = ".$_SESSION['IDusr'];
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);

$to = $row['email'];
$subject = 'Recordar contraseña en Albaran';
$message = 'Hola, '.$row['nombre'].' '.$row['apellido'].'\nSu contraseña de inicio de sesión es '.$row['pass'].'\n\nNo la olvide, muchas gracias';
$header = 'From: JeykAntonio <jeykantonio@gmail.com>';

//mail($to, $subject, $message, $header);

header("Location: ../config.php");
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaranes - Home</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/home.css">

</head>
<body>	
	
    <?php

	session_start();
    include("php-files/connect.php");


    $IDusr =  '';
    $nombre = '';
    $apellido = '';
    $email = '';

    // Check if the user is logged
    if($_SESSION['newsession'] != "letstart") {

        header("location:index.php");
        exit(); //The user is not logged in -> back to login
        
    }
    else {

        // Get the user data from the DB
        include("php-files/connect.php");
        include("php-files/prepareUser.php");

	?>
	
    <?php include("php-files/header.html"); ?>
    
    <?php include("php-files/sidebar.html");?>

    <div class="panel">

        <div class="panel-content">

            <div class="welcome">
                <h2>Bienvenido al panel de control</h2>
                <h3>Utilice los botones para navegar</h3>
            </div>

        </div>
        
    </div>
    <script src="js/toggleMenu.js"></script>

		<?php } ?>
        
</body>
</html>
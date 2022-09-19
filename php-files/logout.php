<?php 

    session_start();

    unset($_SESSION['newsession']);     // index.php - Exists when the user in logged
    unset($_SESSION['IDusr']);          // index.php - The logged user's ID
    unset($_SESSION['order']);          // lista.php - The order the user searched the last
    unset($_SESSION['datefilter']);     // lista.php - The date filter the user choosed last
    unset($_SESSION['wordfilter']);     // lista.php - The word the user was searching for

    session_destroy(); // Destroys the session but not the session variables (unset above is used to do so)

    header("Location: ../index.php");

?>
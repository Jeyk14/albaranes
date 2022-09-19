<?php 

    session_start();

    unset($_SESSION['newsession']);     // Exists when the user in logged
    unset($_SESSION['IDusr']);          // The logged user's ID
    unset($_SESSION['order']);          // list.php navigation - The order the user searched the last
    unset($_SESSION['datefilter']);     // list.php datefilter - 
    unset($_SESSION['wordfilter']);
    unset($_SESSION['cliWordfilter']);  // The searched word filter of the clients
    unset($_SESSION['cliOrder']);       // The searched client order

    unset($_SESSION['userOrder']);     // Admin only
    unset($_SESSION['cliWordfilter']); // Admin only

    session_destroy(); // Destroys the session but not the session variables (unset above is used to do so)

    header("Location: index.php");

?>
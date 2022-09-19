<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaran - Usuarios</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/listaCliente.css">
</head>

<body>

    <?php 
    
    session_start();

    if ($_SESSION['newsession'] != "letstart") {

        header("location:index.php");
        exit(); //The user is not logged in -> back to login

    } else {

        include("php-files/connect.php");
        include("php-files/pepareQuery.php");
        include("php-files/prepareUser.php");

        $userOrder = 'none';
        $wordfilter = '';
        $page = 1;
        $query = 'SELECT id, nombre, apellido, email, privilegio FROM `cliente` ';
        $needsand = false; // if the query requires an AND

        $offset = 0;
        $offsetNext = 0;
        $hasNext = false; // Set to true if a query for the next page returns results
        $queryNext = 'SELECT id FROM `cliente` '; // Will contain the query for the next page
        $rowCountNext = ''; // The number of rows in the next page

        // Get the page number from the URL if exists
        if (isset($_GET['page'])) {

            if ($_GET['page'] <= 0 or !is_numeric($_GET['page'])) {
                $page = 1;
            } else {
                $page = $_GET['page'];
            }
        }

        // To show the user what was searched
        $orderStr = 'No ordenar';
        $wordfilterStr = 'No buscar coincidencias';

        // If the attribute exists in the session assign its value to the variables
        if (isset($_SESSION['userOrder'])) {
            $userOrder = $_SESSION['userOrder'];
        }

        if (isset($_SESSION['userWordfilter'])) {
            $wordfilter = $_SESSION['userWordfilter'];
        }

        if (isset($_GET['searched']) and isset($_POST['order']) and isset($_POST['word-filter'])) {

            $userOrder = $_POST['order'];
            $cliWordfilter = $_POST['word-filter'];

            $_SESSION['userOrder'] = $userOrder;
            $_SESSION['userWordfilter'] = $cliWordfilter;
        }

        // -------------------------------------------------------------------

        // If there is any condition, add the WHERE to the query
        if (strlen($wordfilter) > 1) {
            $query = $query . ' WHERE ';
            $queryNext = $queryNext . ' WHERE ';
        }

        // Add the word condition to the query (following the WHERE)
        // Search the given word in the nombre field
        if (strlen($cliWordfilter) > 1) {

            // deny the use of SQL keywords
            if (!isValidField($cliWordfilter)) {
                // do nothing
            } else {
                $wordfilterStr = "nombre: '" . $cliWordfilter . "'";
                $wordfilter = prepare($wordfilter);
                $query = $query . " nombre LIKE '%" . $cliWordfilter . "%' ";
                $queryNext = $queryNext . " empresa LIKE '%" . $cliWordfilter . "%' ";
                $needsand = true;
            }
        }

        // TODO: Continuar por aqui

    }
    
    ?>

    <?php include("php-files/header.html") ?>

    <?php include("php-files/sidebar.html") ?>

    <div class="panel">

        <div class="clients">

            <?php if (isset($_GET['added'])) {
                if ($success) { ?>
                    <div class="temp-message success">
                        <h4><?php echo $tempMsg ?></h4>
                    </div>
                <?php } else if ($affected_rows == 0) { ?>
                    <div class="temp-message fail">
                        <h4><?php echo $tempMsg ?></h4>
                    </div>
            <?php }
            } ?>

            <div class="filters">

                <form class="sort-form" action="listaClientes.php?searched=1" method="POST">
                    <div class="order">
                        <label for="order">Ordenar por:</label>
                        <select name="order" id="order">
                            <option value="none">Sin ordenar</option>
                            <option value="namedesc">Por nombre descendente (A-Z)</option>
                            <option value="nameasc">Por nombre ascendente (Z-A)</option>
                            <option value="lastnamedesc">Por apellido descendente (Z-A)</option>
                            <option value="lastnameasc">Por apellido ascendente (A-Z)</option>
                            <option value="leveldesc">Usuarios administradores primero</option>
                            <option value="levelasc">Usuarios colaboradores primero</option>
                        </select>
                    </div>

                    <div class="search-field filter-field">
                        <label for="word-filter">Nombre</label>
                        <input type="search" name="word-filter" autocomplete="off" placeholder="No buscar" />
                    </div>

                    <input type="submit" value="Buscar" />

                </form>
            </div>
        </div>
    </div>

</body>

</html>
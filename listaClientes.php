<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaranes - Gestionar clientes</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/listaCliente.css">
    <link rel="stylesheet" href="css/lista.css">
    

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

        $cliOrder = 'none';
        $cliWordfilter = '';
        $page = 1;
        $query = 'SELECT id, cliente, empresa, modificado FROM `cliente` ';
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
         if (isset($_SESSION['cliOrder'])) {
            $cliOrder = $_SESSION['cliOrder'];
        }

        if (isset($_SESSION['cliWordfilter'])) {
            $cliWordfilter = $_SESSION['cliWordfilter'];
        }

        if (isset($_GET['searched']) and isset($_POST['order']) and isset($_POST['word-filter'])) {

            $cliOrder = $_POST['order'];
            $cliWordfilter = $_POST['word-filter'];

            $_SESSION['cliOrder'] = $cliOrder;
            $_SESSION['cliWordfilter'] = $cliWordfilter;
        }

        // --------------------------------------------- QUERY BUILDING

        // If there is any condition, add the WHERE to the query
        if (strlen($cliWordfilter) > 1) {
            $query = $query . ' WHERE ';
            $queryNext = $queryNext . ' WHERE ';
        }

        // Add the word condition to the query (following the WHERE)
        // Search the given word in the nombre, empresa, direccion and comentarios field
        if (strlen($cliWordfilter) > 1) {

            // deny the use of SQL keywords
            if (!isValidField($cliWordfilter)) {
                // do nothing
            } else {
                $wordfilterStr = "Empresa: '" . $cliWordfilter . "'";
                $cliWordfilter = prepare($cliWordfilter);
                $query = $query . " empresa LIKE '%" . $cliWordfilter . "%' ";
                $queryNext = $queryNext . " empresa LIKE '%" . $cliWordfilter . "%' ";
                $needsand = true;
            }
        }

        // Add query order at the end of the query
        switch ($cliOrder) {
            case 'none':
                // use the inserction order
                break;
            case 'businessdesc':
                $query = $query . ' ORDER BY empresa DESC';
                $orderStr = 'Por nombre de empresa descendente (A-Z)';
                break;
            case 'businessasc':
                $query = $query . ' ORDER BY empresa ASC';
                $orderStr = 'Por nombre de empresa ascendente (Z-A)';
                break;
            case 'clientdesc':
                $query = $query . ' ORDER BY cliente DESC';
                $orderStr = 'Por nombre de cliente descendente (A-Z)';
                break;
            case 'clientasc':
                $query = $query . ' ORDER BY cliente ASC';
                $orderStr = 'Por nombre de cliente ascendente (Z-A)';
                break;
                case 'moddesc':
                    $query = $query . ' ORDER BY modificado DESC';
                    $orderStr = 'Modificados más recientes primero';
                    break;
                case 'modasc':
                    $query = $query . ' ORDER BY modificado ASC';
                    $orderStr = 'Modificados más antiguos primero';
                    break;
            default:
                // By default = the user has modified the HTML --> do nothing
                break;
        }

        // Calculates the offset of the element to make 10 element per page

        // Example: Page 1 = elements from 1 to 10 (offset = 1)
        //          Page 2 = elements from 11 to 20 (offset = 10)
        //          Page 3 = elements from 21 to 30 (offset = 20)
        if ($page > 1) {
            $offset = ($page - 1) * 10;
        }

        $offsetNext = $page * 10;

        $query = $query . ' limit 10 offset ' . $offset;
        $queryNext = $queryNext . ' limit 10 offset ' . $offsetNext;

        $result = mysqli_query($con, $queryNext);
        $rowCountNext = mysqli_num_rows($result);

        if ($rowCountNext > 0) {
            $hasNext = true;
        }

        // -------------------------------------------------------- QUERY EXECUTION

        $result = mysqli_query($con, $query);
        $row_count = mysqli_num_rows($result);
        $rows = array();
    
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
                            <option value="businessdesc">Por nombre de empresa descendente (A-Z)</option>
                            <option value="businessasc">Por nombre de empresa ascendente (Z-A)</option>
                            <option value="clientdesc">Por nombre de cliente descendente (A-Z)</option>
                            <option value="clientasc">Por nombre de cliente ascendente (Z-A)</option>
                            <option value="moddesc">Modificados más recientes primero</option>
                            <option value="modasc">Modificados más antiguos primero</option>
                        </select>
                    </div>

                    <div class="search-field filter-field">
                        <label for="word-filter">Empresa</label>
                        <div class="dropdown">
                            <input type="search" id="business" name="word-filter" autocomplete="off" placeholder="No buscar" />
                            <div id="search-result" class="search-list"></div>
                        </div>
                    </div>

                    <input type="submit" value="Buscar" />

                </form>
            </div>

            <?php if ($orderStr != 'No ordenar' or $wordfilterStr != 'No buscar coincidencias') { ?>
                <div class="list-config">
                    <h3>Buscado:</h3>
                    <ul>
                        <?php if ($orderStr != 'No ordenar') { ?>
                            <li><?php echo $orderStr ?></li>
                        <?php } ?>
                        <?php if ($wordfilterStr != 'No buscar coincidencias') { ?>
                            <li><?php echo $wordfilterStr ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <div class="search-result">

                <?php if ($row_count != 0) { ?>

                <!-- Result of the form above -->
                <?php while ($row = $result->fetch_assoc()) { ?>

                    <div class="row-data">
                        <div class="client-name">
                            <h3><?= $row['cliente'] ?></h3>
                            <p><?= $row['empresa'] ?></p>
                        </div>
                        <div class="last-mod">
                            <p>Ultima mofificacion: <?= $row['modificado'] ?></p>
                        </div>
                        <div class="client-id">
                            <p>Cliente #<?= $row['id'] ?></p>
                            <div>
                            <a href="modCliente.php?id=<?= $row['id'] ?>"><button>Modificar</button></a>
                        </div>
                        </div>
                    </div>

                <?php }} ?>

            </div>

        </div>

        <div class="page-controls">
                <?php if ($page > 1) { ?>
                     <div>
                        <a href="listaClientes.php?page=<?= $page - 1?>"><button>Anterior</button></a>
                     </div>
               <?php } else { ?>
                    <div>
                    <a><button class="gray-button" >Anterior</button></a>
                    </div>
                <?php } ?>
                <div>
                    <p>Página: <?php echo $page ?></p>
                </div>
                <?php if ($hasNext) { ?>
                    <div>
                        <a href="listaClientes.php?page=<?php echo $page + 1 ?>"><button>Siguiente</button></a>
                    </div>
                <?php } else { ?>
                    <div>
                        <a><button class="gray-button"> Siguiente </button></a>
                    </div>
                <?php } ?>
            </div>

    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/autocomplete.js"></script>
    <script src="js/toggleMenu.js"></script>

    <?php } ?>

</body>

</html>
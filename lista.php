<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaran - Listado</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/lista.css">

</head>

<body>

    <?php

    session_start();

    if ($_SESSION['newsession'] != "letstart") {

        header("location:index.php");
        exit(); //The user is not logged in -> back to login

    } else {

        include('php-files/connect.php');
        include('php-files/pepareQuery.php');
        include("php-files/prepareUser.php");

        // ----------------------------------------------

        $order = 'none';
        $datefilter = 'none';
        $wordfilter = '';
        $page = 1;
        $query = 'SELECT reg.id, cli.cliente, cli.empresa, reg.direccion, reg.fecha, reg.hora, reg.comentarios, reg.firma, usu.nombre, usu.apellido FROM `registro` AS reg INNER JOIN `cliente` as cli ON reg.id_cliente = cli.id INNER JOIN `usuario` as usu on reg.id_usuario = usu.id';
        $needsand = false; // if the query requires an AND

        $offset = 0;
        $offsetNext = 0;
        $hasNext = false; // Set to true if a query for the next page returns results
        $queryNext = 'SELECT reg.id, cli.cliente FROM `registro` AS reg INNER JOIN `cliente` as cli ON reg.id_cliente = cli.id '; // Will contain the query for the next page
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
        $datefilterStr = 'No filtrar por fecha';
        $wordfilterStr = 'No buscar coincidencias';

        // If the attribute exists in the session assign its value to the variables
        if (isset($_SESSION['order'])) {
            $order = $_SESSION['order'];
        }

        if (isset($_SESSION['datefilter'])) {
            $datefilter = $_SESSION['datefilter'];
        }

        if (isset($_SESSION['wordfilter'])) {
            $wordfilter = $_SESSION['wordfilter'];
        }

        // The user clicked the submit button, the values in the form will be used in the query
        // Unavalieble POST attribute due to user misbehave -> do nothing
        if (isset($_GET['searched']) and isset($_POST['order']) and isset($_POST['date-filter']) and isset($_POST['word-filter'])) {

            $order = $_POST['order'];
            $datefilter = $_POST['date-filter'];
            $wordfilter = $_POST['word-filter'];

            $_SESSION['order'] = $order;
            $_SESSION['datefilter'] = $datefilter;
            $_SESSION['wordfilter'] = $wordfilter;
        }

        // --------------------------------------------- QUERY BUILDING

        // If there is any condition, add the WHERE to the query
        if ($datefilter != 'none' or strlen($wordfilter) > 1) {
            $query = $query . ' WHERE ';
            $queryNext = $queryNext . ' WHERE ';
        }

        // Add the word condition to the query (following the WHERE)
        // Search the given word in the nombre, empresa, direccion and comentarios field
        if (strlen($wordfilter) > 1) {

            // deny the use of SQL keywords
            if (!isValidField($wordfilter)) {
                // do nothing
            } else {
                $wordfilterStr = "Empresa: '" . $wordfilter . "'";
                $wordfilter = prepare($wordfilter);
                $query = $query . " cli.empresa LIKE '%" . $wordfilter . "%' ";
                $queryNext = $queryNext . " cli.empresa LIKE '%" . $wordfilter . "%' ";
                $needsand = true;
            }
        }

        // Add the date condition to the query
        // If user is searching by word too -> add AND before
        switch ($datefilter) {
            case 'none':
                // nothing
                break;
            case 'past':
                if ($needsand) {
                    $query = $query . ' AND ';
                }
                $query = $query . ' `fecha` < CURDATE() ';
                $queryNext = $queryNext . ' `fecha` < CURDATE() ';
                $datefilterStr = 'Buscar registros pasados';
                break;
            case 'present':
                if ($needsand) {
                    $query = $query . ' AND ';
                }
                $query = $query . ' `fecha` = CURDATE() ';
                $queryNext = $queryNext . ' `fecha` = CURDATE() ';
                $datefilterStr = 'Buscar registros presentes';
                break;
            case 'future':
                if ($needsand) {
                    $query = $query . ' AND ';
                }
                $query = $query . ' `fecha` > CURDATE() ';
                $queryNext = $queryNext . ' `fecha` > CURDATE() ';
                $datefilterStr = 'Buscar registros furutos';
                break;
            default:
                // By default = the user has modified the HTML --> do nothing
                break;
        }


        // Add query order at the end of the query
        switch ($order) {
            case 'none':
                // use the inserction order
                break;
            case 'businessdesc':
                $query = $query . ' ORDER BY cli.empresa DESC';
                $orderStr = 'Por nombre de empresa descendente (A-Z)';
                break;
            case 'businessasc':
                $query = $query . ' ORDER BY cli.empresa ASC';
                $orderStr = 'Por nombre de empresa ascendente (Z-A)';
                break;
            case 'clientdesc':
                $query = $query . ' ORDER BY cli.cliente DESC';
                $orderStr = 'Por nombre de cliente descendente (A-Z)';
                break;
            case 'clientasc':
                $query = $query . ' ORDER BY cli.cliente ASC';
                $orderStr = 'Por nombre de cliente ascendente (Z-A)';
                break;
            case 'addressdesc':
                $query = $query . ' ORDER BY reg.direccion DESC';
                $orderStr = 'Por direccion descendente (A-Z)';
                break;
            case 'addressasc':
                $query = $query . ' ORDER BY reg.direccion ASC';
                $orderStr = 'Por direccion ascendente (Z-A)';
                break;
            case 'datedesc':
                $query = $query . ' ORDER BY reg.fecha DESC';
                $orderStr = 'Por fecha descendente';
                break;
            case 'dateasc':
                $query = $query . ' ORDER BY reg.fecha ASC';
                $orderStr = 'Por fecha ascendente';
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

            <div class="filters">

                <form class="sort-form" action="lista.php?searched=1" method="POST">
                    <div class="order">
                        <label for="order">Ordenar por:</label>
                        <select name="order" id="order">
                            <option value="none">Sin ordenar</option>
                            <option value="businessdesc">Por nombre de empresa descendente (A-Z)</option>
                            <option value="businessasc">Por nombre de empresa ascendente (Z-A)</option>
                            <option value="clientdesc">Por nombre de cliente descendente (A-Z)</option>
                            <option value="clientasc">Por nombre de cliente ascendente (Z-A)</option>
                            <option value="addressdesc">Por direccion descendente (A-Z)</option>
                            <option value="addressasc">Por direccion ascendente (Z-A)</option>
                            <option value="datedesc">Por fecha descendente</option>
                            <option value="dateasc">Por fecha ascendente</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="date-filter">Fecha</label>
                        <select name="date-filter" id="date-filter">
                            <option value="none">No filtrar por fecha</option>
                            <option value="past">Pasados</option>
                            <option value="present">Presentes</option>
                            <option value="future">Furutos</option>
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

            <?php if ($orderStr != 'No ordenar' or $datefilterStr != 'No filtrar por fecha' or $wordfilterStr != 'No buscar coincidencias') { ?>
                <div class="list-config">
                    <h3>Buscado:</h3>
                    <ul>
                        <?php if ($orderStr != 'No ordenar') { ?>
                            <li><?php echo $orderStr ?></li>
                        <?php } ?>

                        <?php if ($datefilterStr != 'No filtrar por fecha') { ?>
                            <li><?php echo $datefilterStr ?></li>
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

                            <div class="entry-info">

                                <div class="client-info">
                                    <div class="client-name">
                                        <label for="client-name">Cliente</label><input type="text" name="client-name" value="<?php echo $row['cliente'] ?>" disabled />
                                    </div>
                                    <div class="client-business">
                                        <label for="client-business">Empresa</label><input type="text" name="client-business" value="<?php echo $row['empresa'] ?>" disabled />
                                    </div>
                                </div>

                                <div class="delivery-info">
                                    <div class="delivery-datetime">
                                        <label>Hora y fecha</label><br />
                                        <input type="date" name="delivery-date" value="<?php echo $row['fecha'] ?>" disabled />
                                        <input type="time" name="delivery-time" value="<?php echo $row['hora'] ?>" disabled />
                                    </div>

                                    <div class="address-info">
                                        <label for="client-address">Dirección</label><input type="text" name="client-address" value="<?php echo $row['direccion'] ?>" disabled />
                                    </div>
                                </div>

                            </div>

                            <div class="delivery-comments">
                                <div class="comments">
                                    <textarea rows="3" readonly><?php echo $row['comentarios'] ?></textarea>
                                </div>
                                <div class="signature">
                                    <img src='<?php echo $row['firma'] ?>' title="firma de <?= $row['cliente'] ?>" onerror="this.style.display='none'" />
                                </div>
                            </div>
                            <div class="mod-row">
                                <p>Encargado: <?= $row['nombre']." ".$row['apellido'] ?></p>
                                <a href="modificar.php?id=<?php echo $row['id'] ?>"><button class="subtle-button">Modificar</button></a>
                            </div>
                        </div>

                    <?php } ?>

            </div>

            <div class="page-controls">
                <?php if ($page > 1) { ?>
                    <div>
                        <a href="lista.php?page=<?= $page - 1 ?>"><button>Anterior</button></a>
                    </div>
                <?php } else { ?>
                    <div>
                        <a><button class="gray-button">Anterior</button></a>
                    </div>
                <?php } ?>
                <div>
                    <p>Página: <?php echo $page ?></p>
                </div>
                <?php if ($hasNext) { ?>
                    <div>
                        <a href="lista.php?page=<?php echo $page + 1 ?>"><button>Siguiente</button></a>
                    </div>
                <?php } else { ?>
                    <div>
                        <a><button class="gray-button"> Siguiente </button></a>
                    </div>
                <?php } ?>
            </div>

        <?php } else { ?>

            <div class="no-result">
                <h2>No hay resultados...</h2>
            </div>

        <?php } ?>

        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="js/autocomplete.js"></script>
        <script src="js/toggleMenu.js"></script>

    <?php } ?>
        </div>

</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaranes - Añadir cliente</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/anadir.css">
    <link rel="stylesheet" href="css/modificar.css">

</head>

<body>

    <?php
    session_start();

    if ($_SESSION['newsession'] != "letstart") {

        header("location:index.php");
        exit(); //The user is not logged in -> back to login

    } else {

        $IDusr = $_SESSION['IDusr']; // User logged -> get the user ID and proceed

        // Get the user data from the DB
        include("php-files/connect.php");
        include('php-files/pepareQuery.php');
        include("php-files/prepareUser.php");

        $found = 0;
        $modified_rows = 0;

        $found_rows = -1;
        $tempMsg = '';
        $numProblems = 0;
        $success = true;
        $coma = false;

        $client = '';
        $business =  '';
        $modified = date("Y-m-d");

        $queryGet = 'SELECT id, cliente, empresa, modificado FROM `cliente` WHERE id = ';
        $queryMod = 'UPDATE `cliente` SET ';
        $queryDel = 'DELETE FROM `cliente` WHERE id = ';


        // If the user is deleting the row 
        if (isset($_POST['id_del'])) {
            $queryDel .= ' ' . $_POST['id_del'];

            $resultDel = mysqli_query($con, $queryDel)
                or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

            header("Location: lista.php");
            exit();
        }

        // A query to fill the empty form with the data in the DB
        if (isset($_GET['id'])) {

            $id = $_GET['id'];

            $queryGet = $queryGet . ' ' . $id;

            $resultGet = mysqli_query($con, $queryGet)
                or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

            $row = mysqli_fetch_array($resultGet);
            $found_rows = mysqli_num_rows($resultGet);

            if ($found_rows > 0) {
                $found = 1;
            }
        }

        // Uploads all the modifications to the database
        if (isset($_GET['modified'])) {

            $dataChanged = 0;

            // If the client name is modified and is not empty
            if ($_POST['client'] != 'none' and $_POST['client'] != $row['cliente']) {
                if (isValidField($_POST['client'])) {
                    $queryMod = $queryMod . " cliente = '" . prepare($_POST['client']) . "' ";
                    $coma = true;
                    $dataChanged = 1;
                } else {
                    $numProblems++;
                    $dataChanged = 1;
                }
            }

            if (strlen($_POST['business']) > 0 and $_POST['business'] != $row['empresa']) {
                if (isValidField($_POST['business'])) {
                    if ($coma) {
                        $queryMod .= ',';
                    }
                    $queryMod = $queryMod . " empresa = '" . prepare($_POST['business']) . "'";
                    $coma = true;
                    $dataChanged = 1;
                } else {
                    $numProblems++;
                    $dataChanged = 1;
                }
            }

            // Execute the query if any data changed
            if ($dataChanged == 1) {

                switch ($numProblems) {
                    case 0:
                        $queryMod = $queryMod . ", modificado = '$modified' WHERE `id` = " . $_GET['id'];
                        $resultMod = mysqli_query($con, $queryMod)
                            or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

                        $modified_rows = mysqli_affected_rows($con);
                        $tempMsg = 'Se ha actualizado el registro de forma exitosa';
                        break;
                    case 1:
                        $success = false;
                        $tempMsg = 'Uno de los campos contiene una palabra reservada y no se puede actualizar';
                        break;

                    default:
                        $success = false;
                        $tempMsg = 'Algunos campos contiene palabras reservadas y no se pueden actualizar';
                        break;
                }
            } else {
                $tempMsg = 'No ha rellenado ningún campo';
                $success = false;
            }
        }

    ?>

        <?php include("php-files/header.html") ?>

        <?php include("php-files/sidebar.html") ?>

        <div class="panel">

            <?php if ($found == 1) { ?>

                <?php if (strcmp($level, 'adm') == 0) { // Only delete if admin
                ?>

                    <div class="del-form">
                        <form action="modificar.php?" method="POST">
                            <input type="hidden" name="id_del" value="<?= $row['id'] ?>" />
                            <input type="submit" class="danger-button" value="Borrar" />
                        </form>
                    </div>

                <?php } ?>

                <div class="add-form">

                    <?php if (isset($_GET['modified'])) {
                        if ($success) { ?>
                            <div class="temp-message success">
                                <h4><?php echo $tempMsg ?></h4>
                            </div>
                        <?php } else if ($modified_rows == 0) { ?>
                            <div class="temp-message fail">
                                <h4><?php echo $tempMsg ?></h4>
                            </div>
                    <?php }
                    } ?>

                    <div class="client-options">
                        <form action="modCliente.php?id=<?= $row['id'] ?>&modified=1" method="post">

                            <div class="client-info">
                                <div class="name-lastname">
                                    <label>Nombre del cliente</label>
                                    <input type="text" name="client" value="<?= $row['cliente'] ?>" required />
                                </div>

                                <div class="client-business">
                                    <label>Empresa</label>
                                    <div class="dropdown">
                                        <input type="search" id="business" name="business" autocomplete="off" value="<?= $row['empresa'] ?>" placeholder="No buscar" />
                                        <div id="search-result" class="search-list"></div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="lastMod">Fecha de hoy</label>
                                <input type="date" value="<?= date("Y-m-d") ?>" disabled />
                                <label for="lastMod">Última modificación</label>
                                <input type="date" value="<?= $row['modificado'] ?>" disabled />
                            </div>

                            <input type="hidden" value="idClient" name="id" />

                            <div class="form-options">
                                <input type="submit" value="Modificar" />
                                <p class="small-text">No utilice los caracteres !, ", %, \, /</p>
                            </div>

                        </form>
                    </div>

                </div>

        </div>

        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="js/autocomplete.js"></script>

    <?php } else { ?>

        <div class="add-form">

            <form action="modCliente.php" method="GET">
                <label for="id">Id del registro a actualizar</label>
                <input type="number" min="1" value="1" name="id" id="id" />
                <input type="submit" value="Modifiar" />
            </form>

        </div>

    <?php } ?>

<?php } ?>

</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaranes - Añadir</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/modificar.css">
    <link rel="stylesheet" href="css/anadir.css">

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

        $found = 0;
        $row;
        $modified_rows = 0;

        $found_rows = -1;
        $tempmessage = '';
        $numProblems = 0;
        $success = true;

        $from = '';
        $id;
        $id_client = '';
        $address = '';
        $date = '';
        $time = '';
        $comment = '';
        $signature = '';

        $idOwner = 0;

        $queryGet = 'SELECT reg.id, reg.id_cliente, reg.id_usuario, cli.cliente, cli.empresa, reg.direccion, reg.fecha, reg.hora, reg.comentarios, reg.firma FROM `registro` AS reg INNER JOIN `cliente` as cli ON reg.id_cliente = cli.id WHERE reg.id = ';
        $queryMod = 'UPDATE `registro` SET ';
        $queryDel = 'DELETE FROM `registro` WHERE id = ';
        $coma = false;

        $resultGet;

        // If the user is deleting the row 
        if (isset($_POST['id_del'])) {
            $queryDel = $queryDel . ' ' . $_POST['id_del'];

            $resultDel = mysqli_query($con, $queryDel)
                or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

            header("Location: lista.php");
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

            // Get the name and last name of the user that created this
            $query = 'SELECT nombre, apellido FROM `usuario` WHERE `id` = ' . $row['id_usuario'];
            $result = mysqli_query($con, $query);
            $rowUsuario = mysqli_fetch_array($result); // load the columns into an array
            $from = $rowUsuario['nombre'] . ' ' . $rowUsuario['apellido'];
        }

        // Uploads all the modifications to the database
        if (isset($_GET['modified'])) {

            $dataChanged = 0;

            // If the client name is modified and is not empty
            if ($_POST['id_client'] != 'none' and $_POST['id_client'] != $row['id_cliente']) {
                if (isValidField($_POST['id_client'])) {
                    $queryMod = $queryMod . " id_cliente = " . prepare($_POST['id_client']) . " ";
                    $coma = true;
                    $dataChanged = 1;
                } else {
                    $numProblems++;
                    $dataChanged = 1;
                }
            }

            // if (strlen($_POST['business']) > 0 and $_POST['business'] != $row['empresa']) {
            //     if(isValidField($_POST['business'])){
            //         $queryMod = $queryMod . " empresa = '" . prepare($_POST['business']) . "'";
            //         $dataChanged = 1;
            //     } else {
            //         $numProblems ++;
            //         $dataChanged = 1;
            //     }
            // }

            if (strlen($_POST['address']) > 0 and $_POST['address'] != $row['direccion']) {
                if (isValidField($_POST['address'])) {
                    if ($coma) {
                        $queryMod .= ',';
                    }
                    $queryMod = $queryMod . " direccion = '" . prepare($_POST['address']) . "'";
                    $coma = true;
                    $dataChanged = 1;
                } else {
                    $numProblems++;
                    $dataChanged = 1;
                }
            }

            // TODO: Check if user modified the HTML to put anything different
            if (strlen($_POST['delivery-date']) > 0 and $_POST['delivery-date'] != $row['fecha']) {
                if ($coma) {
                    $queryMod .= ',';
                }
                $queryMod = $queryMod . " fecha = '" . $_POST['delivery-date'] . "'";
                $coma = true;
                $dataChanged = 1;
            }

            // TODO: Check if user modified the HTML to put anything different
            if (strlen($_POST['delivery-time']) > 0 and $_POST['delivery-time'] != $row['hora']) {
                if ($coma) {
                    $queryMod .= ',';
                }
                $queryMod = $queryMod . " hora = '" . $_POST['delivery-time'] . "'";
                $coma = true;
                $dataChanged = 1;
            }

            if (strlen($_POST['details']) > 0 and $_POST['details'] != $row['comentarios']) {
                if (isValidField($_POST['details'])) {
                    if ($coma) {
                        $queryMod .= ',';
                    }
                    $queryMod = $queryMod . " comentarios = '" . prepare($_POST['details']) . "'";
                    $coma = true;
                    $dataChanged = 1;
                } else {
                    $numProblems++;
                    $dataChanged = 1;
                }
            }

            if (strlen($_POST['signature']) > 26 and $_POST['signature'] != $row['firma']) {
                if (isValidField($_POST['details'])) {
                    if ($coma) {
                        $queryMod .= ',';
                    }
                    $queryMod = $queryMod . " firma = '" . $_POST['signature'] . "'";
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
                        $queryMod = $queryMod . " WHERE `id` = " . $_GET['id'];
                        $resultMod = mysqli_query($con, $queryMod)
                            or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

                        $modified_rows = mysqli_affected_rows($con);
                        $tempmessage = 'Se ha actualizado el registro de forma exitosa';
                        break;
                    case 1:
                        $success = false;
                        $tempmessage = 'Uno de los campos contiene una palabra reservada y no se puede actualizar';
                        break;

                    default:
                        $success = false;
                        $tempmessage = 'Algunos campos contiene palabras reservadas y no se pueden actualizar';
                        break;
                }
            } else {
                $tempmessage = 'No ha rellenado ningún campo';
                $success = false;
            }
        }

    ?>

        <?php include("php-files/header.html") ?>

        <?php include("php-files/sidebar.html") ?>

        <div class="panel">

            <?php if ($found == 1) { ?>

                <?php if ($level == 'adm' or $IDusr == $row['id_usuario']) { // Only delete if admin or creator
                ?>

                    <div class="del-form">
                        <form action="modificar.php?" method="POST">
                            <input type="hidden" name="id_del" value="<?php echo ($id) ?>" />
                            <input type="submit" class="danger-button" value="Borrar" />
                        </form>
                    </div>

                <?php } ?>

                <div class="add-form">

                    <form action="modificar.php?id=<?php echo ($id) ?>&modified=1" method="POST">

                        <?php if (isset($_GET['modified'])) {
                            if ($success) { ?>
                                <div class="temp-message success">
                                    <h4><?php echo $tempmessage ?></h4>
                                </div>
                            <?php } else if ($modified_rows == 0) { ?>
                                <div class="temp-message fail">
                                    <h4><?php echo $tempmessage ?></h4>
                                </div>
                        <?php }
                        } ?>

                        <div>
                            <div>
                                <label for="username">Encargado</label>
                                <input type="text" name="username" value="<?php echo $from ?>" disabled>
                            </div>
                        </div>

                        <div class="from-to">

                            <div class="form-business">
                                <label for="business">Empresa</label>
                                <div class="dropdown">
                                    <input type="search" id="business" name="word-filter" value="<?php echo ($row['empresa']) ?>" autocomplete="off" placeholder="No buscar" />
                                    <div id="search-result" class="search-list"></div>
                                </div>
                            </div>

                            <div>
                                <label for="client">Cliente</label>
                                <?php // <input type="text" id="client" name="client" required> 
                                ?>
                                <select name="id_client" id="client">
                                    <option value="<?php echo ($row['id_cliente']) ?>"><?php echo ($row['cliente']) ?></option>
                                </select>
                            </div>

                            <div>
                                <label for="address">Dirección de entrega</label>
                                <input type="text" id="address" name="address" value="<?php echo ($row['direccion']) ?>" required>
                            </div>
                        </div>

                        <div class="date-hour">
                            <div>
                                <label for="delivery-date">Fecha de la entrega</label>
                                <input type="date" id="delivery-date" name="delivery-date" value="<?php echo ($row['fecha']) ?>" required>
                            </div>

                            <div>
                                <label for="delivery-time">Hora de la entrega</label>
                                <input type="time" id="delivery-time" name="delivery-time" value="<?php echo ($row['hora']) ?>" required>
                            </div>
                        </div>

                        <div class="big-blocks">
                            <div class="comment-area">
                                <label for="details">Comentarios de la entrega</label>
                                <textarea name="details" id="details" cols="30" rows="4"><?php echo ($row['comentarios']) ?></textarea>
                            </div>

                            <div class="signature-area">
                                <label for="signature">Firma</label>

                                <div id="signature-pad" class="signature-pad">
                                    <div class="signature-pad--body">
                                        <canvas width="200px" height="150px">
                                        </canvas>
                                    </div>
                                    <div class="signature-pad--footer">

                                        <div class="signature-pad--actions">
                                            <div>

                                                <button type="button" class="button clear" data-action="clear">Borrar</button>
                                                <button type="button" class="button" data-action="undo">Deshacer</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" value="<?php echo ($row['firma']) ?>" id="signature" name="signature">

                            </div>
                        </div>

                        <input type="hidden" name="id" value="<?php echo ($id) ?>" />
                        <input type="submit" value="Modificar registro" />
                        <p class="small-text">No utilice los caracteres !, ", %, \, /</p>

                    </form>

                </div>

            <?php } else { ?>

                <div class="add-form">

                    <form action="modificar.php" method="GET">
                        <label for="id">Id del registro a actualizar</label>
                        <input type="number" min="1" value="1" name="id" id="id" />
                        <input type="submit" value="Modifiar" />
                    </form>

                </div>

            <?php } ?>

        </div>

        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="js/autocomplete.js"></script>
        <script src="js/autocompleteClie.js"></script>
        <script src="js/toggleMenu.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script src="js/signature.js"></script>

        <script>
            loadFromInput()
        </script>
    <?php } ?>


</body>

</html>
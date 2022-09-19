<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaranes - Añadir</title>

    <link rel="stylesheet" href="css/main.css">
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

        $tempSmg = "";
        $success = false;

        $affected_rows = 0;

        if (isset($_GET['added'])) {

            if (empty($_POST['id_client']) or $_POST['id_client'] == "none" or empty($_POST['address']) or empty($_POST['delivery-date']) or empty($_POST['delivery-time'])) {

                // A necessary field is empty -> do nothing

            } else {

                $IDusr = $_SESSION['IDusr'];
                // $business = prepare($_POST['business']); // Not required
                // $client = prepare($_POST['client']);
                $id_client = $_POST['id_client'];
                $address = prepare($_POST['address']);
                $d_date = $_POST['delivery-date'];
                $d_time = $_POST['delivery-time'];
                $detail = prepare($_POST['details']); // Not required
                $signature = $_POST['signature'];

                if ( !isValidField($address) || !isValidField($detail)) {

                    $success = false;
                    $tempMsg = 'No utilice palabras reservadas ni los caracteres<br/> !, ", %, \\, /';

                } else {

                    $query = "INSERT INTO `registro` (id_usuario, id_cliente, direccion, fecha, hora, comentarios, firma) 
                    VALUES ('$IDusr', '$id_client', '$address', '$d_date', '$d_time', '$detail', '$signature')";

                    $result = mysqli_query($con, $query)
                        or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

                    $affected_rows = mysqli_affected_rows($con);

                    if ($affected_rows > 0) {

                        $success = true;
                        $tempMsg = "Registro insertado con éxito";
                    } else {

                        $success = false;
                        $tempMsg = "El registro no se ha podido insertar";
                    }
                }
            }
        }

    ?>

        <?php include("php-files/header.html") ?>

        <?php include("php-files/sidebar.html") ?>

        <div class="panel">

            <div class="add-form">

                <form action="anadir.php?added=1" method="POST" accept-charset="utf-8">

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

                    <div>
                        <div>
                            <label for="username">Encargado</label>
                            <input type="text" name="username" value="<?php echo ($nombre . ' ' . $apellido) ?>" disabled>
                        </div>
                    </div>

                    <div class="from-to">

                        <div class="form-business">
                            <label for="business">Empresa</label>
                            <div class="dropdown">
                                <input type="search" id="business" name="word-filter" autocomplete="off" placeholder="No buscar" />
                                <div id="search-result" class="search-list"></div>
                            </div>
                        </div>

                        <div>
                            <label for="client">Cliente</label>
                            <?php // <input type="text" id="client" name="client" required> ?>
                            <select name="id_client" id="client">
                                <option value="none">Seleccione una empresa</option>
                            </select>
                        </div>

                        <div>
                            <label for="address">Dirección de entrega</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                    </div>

                    <div class="date-hour">
                        <div>
                            <label for="delivery-date">Fecha de la entrega</label>
                            <input type="date" id="delivery-date" name="delivery-date" required>
                        </div>

                        <div>
                            <label for="delivery-time">Hora de la entrega</label>
                            <input type="time" id="delivery-time" name="delivery-time" required>
                        </div>
                    </div>

                    <div class="big-blocks">
                        <div class="comment-area">
                            <label for="details">Comentarios de la entrega</label>
                            <textarea name="details" id="details" cols="30" rows="4"></textarea>
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

                            <input type="hidden" value="" id="signature" name="signature">

                        </div>
                    </div>

                    <input type="submit" value="Añadir registro">
                    <p class="small-text">No utilice los caracteres !, ", %, \, /</p>

                </form>

            </div>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="js/autocomplete.js"></script>
        <script src="js/autocompleteClie.js"></script>
        <script src="js/toggleMenu.js"></script>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="js/signature.js"></script>

</body>

</html>
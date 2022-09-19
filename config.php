<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaranes - Configuración</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/config.css">

</head>

<body>

    <?php

    session_start();

    if ($_SESSION['newsession'] != "letstart") {

        header("location:index.php");
        exit(); //The user is not logged in -> back to login

    } else {

        include("php-files/connect.php");
        include("php-files/prepareUser.php");

        $query = 'UPDATE usuario SET ';
        $row = array();
        $tempmessage = '';
        $success = false;
        $modified_rows = 0;

        if (isset($_GET['mod'])) {

            include('php-files/pepareQuery.php');

            // Modifying the email (mod = 1)
            if ($_GET['mod'] == 1) {
                if (isset($_POST['input-email'])) {
                    $newemail = prepare($_POST['input-email']);
                     $query .= "email = '" . $newemail . "' WHERE id = " . $_SESSION['IDusr'];

                    if(isValidField($newemail)){

                        $result = mysqli_query($con, $query);
                        $modified_rows = mysqli_affected_rows($con);

                        // TODO: Send a mail with a code, type the code here to confirm the new mail

                        // TODO: Check if email already exists

                        if ($modified_rows < 1) {

                            die('Error actualizando el campo email en la tabla usuario ' . mysqli_error($con));
                            $success = false;
                            $tempmessage = "No se ha podido modificar su nombre de usuario, inténtelo más tarde";
                        } else {

                            $success = true;
                            $tempmessage = 'Se ha cambiado el email de a "' . $newemail . '"';
                        }
                    } else {
                        $success = false;
                        $tempmessage = 'El contenido del formulario es inválido.<br/>No utilice palabras reservadas ni los caracteres<br/> !, ", %, \\, /';
                    }
                }
            }

            // Modifying the password (mod = 2)
            if ($_GET['mod'] == 2) {

                if (isset($_POST['input-newpass']) and isset($_POST['repepass']) and isset($_POST['input-oldpass'])) {

                    $newpass = prepare($_POST['input-newpass']);
                    $repepass = prepare($_POST['repepass']);
                    $oldpass = $_POST['input-oldpass'];
                    $correctpass = false;
                    $checkquery = 'SELECT pass FROM usuario WHERE id = ' . $_SESSION['IDusr'];
                    $query .= "pass = '" . $newpass . "' WHERE id = " . $_SESSION['IDusr'];

                    if(isValidField($newpass) && isValidField($repepass) && isValidField($oldpass)){

                        $result = mysqli_query($con, $checkquery);
                        $row = mysqli_fetch_array($result);
                        $modified_rows = 0;

                        if ($row['pass'] === $oldpass) {
                            // The password given match the password in the database

                            // password must be at least 4 char
                            if (strlen($newpass) < 4) {

                                $success = false;
                                $tempmessage = "La contraseña debe tener al menos 4 caracteres";
                            } else {

                                // Check if both passwords matches
                                if ($newpass === $repepass) {

                                    $result = mysqli_query($con, $query);
                                    $modified_rows = mysqli_affected_rows($con);

                                    if (!$result or $modified_rows < 1) {
                                        // SQL error
                                        die('Error actualizando el campo pass en la tabla usuario ' . mysqli_error($con));
                                        $success = false;
                                        $tempmessage = 'Ha ocurrido un error, vuelva a intentarlo más tarde';
                                    } else {
                                        // Password match
                                        $success = true;
                                        $tempmessage = 'Se ha cambiado la contraseña correctamente';
                                    }
                                } else {
                                    // The passowrd doesn't match
                                    $success = false;
                                    $tempmessage = 'Las contraseñas no coinciden';
                                }
                            }
                        } else {
                            // The password given does not match que password in the database

                            $success = false;
                            $tempmessage = "La contraseña que ha escrito no es correcta";
                        }
                    } else {
                        $success = false;
                        $tempmessage = 'El contenido del formulario es inválido.<br/>No utilice palabras reservadas ni los caracteres<br/> !, ", %, \\, /';
                    }
                }
            }

            // Modifying the name (mod = 3)
            if ($_GET['mod'] == 3) {

                if (isset($_POST['input-name'])) {
                    $newname = prepare($_POST['input-name']);
                    $newlastname = prepare($_POST['input-lastname']);
                    $namequery = "SELECT COUNT(*) FROM `usuario` WHERE nombre like '" . $newname . "'";
                    $query .= " nombre = '" . $newname . "' WHERE id = " . $_SESSION['IDusr'];

                    if(isValidField($newname) && isValidField($newlastname)){

                        $result = mysqli_query($con, $namequery);
                        $row_count = mysqli_num_rows($result);

                        if ($row_count > 1) {
                            // found a row with the same name -> do nothing
                            $success = false;
                            $tempmessage = "Ya existe alguien con ese nombre";
                        } else {

                            $result = mysqli_query($con, $query);
                            $modified_rows = mysqli_affected_rows($con);

                            if ($modified_rows < 1) {

                                die('Error actualizando el campo nombre, apellido en la tabla usuario ' . mysqli_error($con));
                                $success = false;
                                $tempmessage = 'No se ha podido actualizar el nombre o el apellido de usuario,<br/>vuelva a intentarlo más tarde';
                            } else {

                                $success = true;
                                $tempmessage = "Nombre de usuario cambiado con éxito";
                            }
                        }
                    } else {
                        $success = false;
                        $tempmessage = 'El contenido del formulario es inválido.<br/>No utilice palabras reservadas ni los caracteres<br/> !, ", %, \\, /';
                    }
                }
            }
        }

    ?>

        <?php include("php-files/header.html") ?>

        <?php include("php-files/sidebar.html") ?>

        <div class="panel">

            <div class="config-panel">
                <?php if (isset($_GET['mod'])) {
                    if ($success == true) { ?>
                        <div class="temp-message success">
                            <h4><?php echo $tempmessage ?></h4>
                        </div>
                    <?php } else { ?>
                        <div class="temp-message fail">
                            <h4><?php echo $tempmessage ?></h4>
                        </div>
                <?php }
                } ?>

                <div class="acc-mod">
                    <form action="config.php?mod=1" method="POST">
                        <h3>Cambiar correo electrónico</h3>
                        <div class="form-content">
                            <div class="form-element">
                                <label for="input-email">Nuevo correo</label>
                                <input type="email" name="input-email" required />
                            </div>
                        </div>
                        <input type="submit" value="Cambiar mi correo" />
                    </form>
                </div>

                <div class="acc-mod">
                    <form action="config.php?mod=3" method="POST">
                        <h3>Cambir nombre de usuario</h3>
                        <div class="form-content">
                            <div class="form-element">
                                <label for="input-name">Nuevo nombre de cuenta</label>
                                <input type="text" name="input-name" required />
                            </div>
                            <div class="form-element">
                                <label for="input-name">Apellido</label>
                                <input type="text" name="input-lastname" />
                            </div>
                            <input type="submit" value="Cambiar mi nombre" />
                        </div>
                    </form>
                </div>

                <div class="acc-mod">
                    <form action="config.php?mod=2" method="POST">
                        <h3>Cambiar contraseña</h3>
                        <div class="form-content pass-form">
                            <div class="form-element">
                                <label for="input-oldpass">Antigua contraseña</label>
                                <input type="password" name="input-oldpass" required />
                            </div>
                            <div class="form-element">
                                <label for="input-newpass">Nueva contraseña</label>
                                <input type="password" name="input-newpass" required />
                            </div>
                            <div class="form-element">
                                <label for="repe-pass">Repetir contraseña</label>
                                <input type="password" name="repepass" required />
                            </div>
                        </div>
                        <input type="submit" value="Cambiar mi contraseña" />
                        <a href="">
                            <p>Recordar mi contraseña</p>
                        </a>
                    </form>
                </div>

            </div>

        </div>

        <script src="js/toggleMenu.js"></script>
    <?php } ?>

</body>

</html>
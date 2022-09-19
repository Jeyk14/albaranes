<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaran - Crear usuario</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/anadirUsuario.css">
</head>

<body>

    <?php

    session_start();

    if ($_SESSION['newsession'] != "letstart") {

        header("location:index.php");
        exit(); //The user is not logged in -> back to login

    } else {

        // Get the user data from the DB
        include("php-files/connect.php");
        include('php-files/pepareQuery.php');
        include('php-files/prepareUser.php');

        if ($level != 'adm') {

            header("location:index.php");
            exit(); //The user is not admin -> back to login

        } else {

            if (isset($_GET['added'])) {

                $username = prepare($_POST['username']);
                $lastname = prepare($_POST['lastname']);
                $userEmail = prepare($_POST['email']);
                $userPass = prepare($_POST['password']);
                $userLevel = prepare($_POST['level']);

                $success = true;
                $tempMsg;

                $query = "INSERT INTO `usuario`(`nombre`, `apellido`, `email`, `pass`, `nivel`) VALUES (";

                if (empty($username) || empty($userEmail) || empty($userPass) || empty($userLevel)) {
                    // an esential field is missing -> nothing
                } else {

                    if (!isValidField($username)) {

                        $success = false;
                        $tempMsg = "El nombre de usuario contiene palabras reservadas o caracteres ilegales";
                    } else if (!isValidField($lastname)) {

                        $success = false;
                        $tempMsg = "El apellido contiene palabras reservadas o caracteres ilegales";
                    } else {

                        $query .= "'" . $username . "', '" . $userEmail . "', '" . $userEmail . "', '" . $userPass . "', '" . $userLevel . "');";

                        $result = mysqli_query($con, $query)
                            or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

                        $affected_rows = mysqli_affected_rows($con);

                        if ($affected_rows > 0) {

                            $success = true;
                            $tempMsg = "Usuario añadido con éxito";
                        } else {

                            $success = false;
                            $tempMsg = "El usuario no se ha podido insertar";
                        }
                    }
                }
            }
        }
    }

    ?>

    <?php include("php-files/header.html") ?>

    <?php include("php-files/sidebar.html") ?>

    <div class="panel">

        <div class="add-form">

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

            <div class="newuser-options">
                <form action="crearUsuario.php?added=1" method="post">

                    <div class="newuser-info">
                        <div class="name">
                            <label>Nombre del usuario</label>
                            <input type="text" name="username" value="" required />
                        </div>

                        <div class="lastname">
                            <label>Apellido del usuario</label>
                            <input type="text" name="lastname" value="" />
                        </div>

                    </div>
                    <div class="newuser-data">

                        <div class="password">
                            <label>Contraseña</label>
                            <input type="password" id="password" name="password" autocomplete="off" />
                        </div>

                        <div class="user-mail">
                            <label>Email</label>
                            <input type="email" id="email" name="email" autocomplete="off" />
                        </div>

                        <div class="level">
                            <label>Privilegio</label>
                            <select name="level" id="level">
                                <option value="col" selected>Colaborador</option>
                                <option value="adm">Administrador</optio>
                            </select>
                        </div>

                    </div>

                    <div class="form-options">
                        <input type="submit" value="Añadir" />
                        <p class="small-text">No utilice los caracteres !, ", %, \, /</p>
                    </div>

                </form>
            </div>

        </div>

    </div>

</body>

</html>
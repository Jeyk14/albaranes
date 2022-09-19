<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albaranes - Iniciar sesión</title>

    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <?php
    ob_start();

    /*
        Note:
        PHP, CSS, JS --> English
        SQL, HTML --> Español
    */

    $emptyUser = "";
    $emptyPass = "";

    $errorMsg = '';

    if (isset($_POST['username']) && isset($_POST['pass'])) {

        include("php-files/connect.php");
        include("php-files/pepareQuery.php");

        if ($con_possible) {

            if (empty($_POST['username'])) {

                $errorMsg = "El nombre de usuario no puede estar vacío";
            } else if (empty($_POST['pass'])) { // both username and pass are filled

                $errorMsg = "La contraseña no puede estar vacía";

            } else {

                $username = strval($_POST['username']);
                $pass = strval($_POST['pass']);

                if (!isValidField($username)) {
                    
                    // Username or password contains a reserved word -> do nothing and message  
                    $errorMsg = 'El contenido del formulario es inválido.<br/>No utilice palabras reservadas ni los caracteres<br/> !, ", %, \\, /';
                
                } else if (!isValidField($pass)) {
                    
                    $errorMsg = 'El contenido del formulario es inválido.<br/>No utilice palabras reservadas ni los caracteres<br/> !, ", %, \\, /';
                
                } else {

                    // $query = "SELECT id, nombre, apellido, email, pass FROM `usuario` WHERE `nombre` = '".strval($username)."'";
                    $query = "SELECT id, nombre, apellido, email, pass, nivel FROM `usuario` WHERE `nombre` = '" . prepare($username) . "'";


                    $result = mysqli_query($con, $query);
                    $row_count = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result); // load the columns into an array

                    if (isset($row['id'])) {

                        $IDusr = $row['id'];
                        $level = $row['nivel'];

                        if ($row['nombre'] == "" or $row['nombre'] == null) {
                            // No such user in the database
                            // tip: avoid hinting the user about other existing usernames
                            // Don't be like Google or Facebook...
                            $errorMsg = 'El nombre de usuario o la contraseña no son correctos';
                        } else {

                            if ($row['nombre'] == "" or $row['nombre'] == null) {
                                // User without pass
                                $errorMsg = 'El nombre de usuario o la contraseña no son correctos';
                            } elseif ($row['pass'] != $pass) {
                                // Wrong pass
                                $errorMsg = 'El nombre de usuario o la contraseña no son correctos';
                            } else {
                                // let the user in
                                session_start();
                                $_SESSION['newsession'] = "letstart";
                                $_SESSION['IDusr'] = $IDusr;
                                $_SESSION['level'] = $level;
                                header("Location:home.php");
                                exit();
                            }
                        }
                    } else {
                        // No matching usernames in the database
                        $errorMsg = 'El nombre de usuario o la contraseña no son correctos';
                    }
                }
            } // END else of if(empty($_POST['username']) or empty($_POST['pass']))

        } else {
            $errorMsg .= 'El servicio solicitado no está disponible<br>Vuelva a intentarlo más tarde';
        }
    } // END if (isset($_GET['logging']))

    ?>

    <div class="login">

        <div class="login-content">

            <img class="img-user circle" src="img/modern.jpg" />

            <?php if (strlen($errorMsg) > 1) { ?>
                <h4 style="color:red;"><?php echo $errorMsg ?></h4>
            <?php } ?>

            <form class="login-form" action="index.php" method="post" accept-charset="utf-8">

                <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>

                <input type="password" id="pass" name="pass" placeholder="Contraseña" required>

                <input type="submit" class="button-login" name="tryLog" value="LOGIN">

            </form>

        </div>

    </div>

</body>

</html>
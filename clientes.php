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

        // Get the user data from the DB
        include("php-files/connect.php");
        include('php-files/pepareQuery.php');
        include('php-files/prepareUser.php');

        if (isset($_GET['added'])) {

            $success = false;
            $tempMsg = "";

            $client = "";
            $busiess = "";
            $id;
            $query = "INSERT INTO `cliente` (`cliente`, `empresa`, `modificado`) VALUES (";

            // TODO: if $lastMod != today -> $lastmod = today

            if (!isset($_POST['client']) || !isset($_POST['business'])) {

                // client or business are empty
                $success = false;
                $tempMsg = "El nombre del cliente o la empresa no pueden estar vacíos";
            } else if (!isValidField($_POST['client']) || !isValidField($_POST['business'])) {

                // client or business are invalid
                $success = false;
                $tempMsg = 'No utilice palabras reservadas ni los caracteres<br/> !, ", %, \\, /';
            } else {

                $client = $_POST['client'];
                $busiess = $_POST['business'];

                // If client and business are given and valid -> insert
                $query .= "'" . $client . "', '" . $busiess . "', '" . date("Y-m-d") . "');";
                $result = mysqli_query($con, $query)
                    or die('No se ha podido ejecutar la consulta ' . mysqli_error($con));

                $affected_rows = mysqli_affected_rows($con);

                if ($affected_rows > 0) {

                    $success = true;
                    $tempMsg = "Cliente añadido con éxito";
                } else {

                    $success = false;
                    $tempMsg = "El cliente no se ha podido añadir";
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

            <div class="client-options">
                <form action="clientes.php?added=1" method="post">

                    <div class="client-info">
                        <div class="name-lastname">
                            <label>Nombre del cliente</label>
                            <input type="text" name="client" value="" required />
                        </div>

                        <div class="client-business">
                            <label>Empresa</label>
                            <div class="dropdown">
                                <input type="search" id="business" name="business" autocomplete="off" placeholder="No buscar" />
                                <div id="search-result" class="search-list"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="lastMod">Última modificación</label>
                        <input type="date" value="<?= date("Y-m-d") ?>" disabled />
                    </div>

                    <input type="hidden" value="idClient" name="id" />

                    <div class="form-options">
                        <input type="submit" value="Añadir" />
                        <p class="small-text">No utilice los caracteres !, ", %, \, /</p>
                    </div>

                </form>
            </div>

        </div>

    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/autocomplete.js"></script>

</body>

</html>
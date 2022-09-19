<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo signature</title>
</head>

<body style="background-color: gray;">

    <?php

    $servername = "localhost";
    $database = "prueba";
    $userDB = "root";
    $passDB = "";
    try {
        $con = mysqli_connect($servername, $userDB, $passDB, $database)
            or die("No se ha podido establecer conexión a " . $servername . "\n" . mysqli_connect_error());
    } catch (Exception $ex) {
        $con_possible = false;
    }

    // get a signature from the database -> add it as the value of the input signature (line:71)
    $result = mysqli_query($con, 'SELECT`firma` FROM `firma` WHERE `id` = 1');
    $row = mysqli_fetch_array($result);

    $rowAdded = 0;
    $signature = null;

    if (isset($_GET["sent"])) {

        $con_possible = true;

        if ($con_possible) {
            // connection possible + form used
            $query = "UPDATE `firma` SET `firma` = ";
            $queryEnd = " WHERE `id` = 1 ";

            // TODO: dataURL to blob and update
            $signature = $_POST['signature'];

            $query .= " '" . $signature . "'".$queryEnd." ";

            mysqli_query($con, $query);
            $rowAdded = mysqli_affected_rows($con);
        }
    }
    ?>

    <form action="demoSign.php?sent=1" method="POST">
        <div id="signature-pad" class="signature-pad">
            <div class="signature-pad--body">
                <canvas width="200px" height="150px">
                </canvas>
            </div>
            <div class="signature-pad--footer">

                <div class="signature-pad--actions">
                    <div>

                        <button type="button" class="button clear" data-action="clear">Clear</button>
                        <button type="button" class="button" data-action="undo">Undo</button>

                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" value="<?php echo $row['firma'] ?>" id="signature" name="signature">

        <input type="submit" value="guardar" />
    </form>

    <p>Rows inserted = <?php echo $rowAdded ?></p>

    <p>Espacio consumido es de <?php echo mb_strlen($row['firma'], '8bit') ?> bytes</p>

    <h5>To check if th signature easily loads as an IMG tag</h5>
    <img src='<?php echo $row['firma']?>' />

    <h5>To see what's stored</h5>
    <textarea style="width: 100%; height: 80vh;">
        <?php echo $row['firma']?>
    </textarea>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // app.js

        // Notes
        /*
            To store the signature as SVG
                Store as sql LongBlob? text? and php 
            To retrieve
                as source of <img> = header("Content-type: image/svg+xml");
        */

        var wrapper = document.getElementById("signature-pad"); // The signature pad
        var clearButton = wrapper.querySelector("[data-action=clear]"); // Delete the signature
        var undoButton = wrapper.querySelector("[data-action=undo]"); // Undo the last stroke
        // var savePNGButton = wrapper.querySelector("[data-action=save-png]");
        // var saveJPGButton = wrapper.querySelector("[data-action=save-jpg]");
        // var saveSVGButton = wrapper.querySelector("[data-action=save-svg]");
        var canvas = wrapper.querySelector("canvas"); // The drawing area

        // Instanciate and iniciate the signature pad "object" to be used and configured
        var signaturePad = new SignaturePad(canvas, {
            dotSize: 1,
            maxWidth: 1.25,
            backgroundColor: 'rgb(255, 255, 255)' //Optional for PNG and SVG
        });


        // Adapt the canvas size
        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);

            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            signaturePad.clear();
        }

        //change the size of the canvas to better fit mobile screens
        window.onresize = resizeCanvas;
        resizeCanvas();

        // Clear the pad
        clearButton.addEventListener("click", function(event) {
            signaturePad.clear();
        });

        /*
            How undo workd (in general):
                Every action is stored in a LIFO colection (a pile of elements) where every change made is stored
                "one on top of another". Using undo removes the action on top of the collection, leaving you with
                all actions but the last one (like going back 1 action)
        */
        undoButton.addEventListener("click", function(event) {
            var data = signaturePad.toData();

            if (data) {
                data.pop(); // remove the last dot or line
                signaturePad.fromData(data);
            }
        });

        // ------------------------------------------------------------------------------------------------------------

        // Save each stroke into input signature, a hidden input
        signaturePad.addEventListener("endStroke", () => {
            document.getElementById("signature").value = signaturePad.toDataURL('image/svg+xml');
        });

        // ------------------------------------------------------------------------------------------------------------

        // get the hidden input signature and load it's data into the signaturePad
        var signature = document.getElementById("signature").value;
        console.log(signature);
        signaturePad.fromDataURL(signature);

    </script>

</body>

</html>
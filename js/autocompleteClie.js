// $(buscarCli());

// Send "consulta" to php-files/prepareList.php
// If no error print the result in #search-result
// Else console log -> error
function buscarCli(dato){
    $.ajax({
        url: 'php-files/autocompleteCli.php',
        type: 'POST',
        dataType: "html",
        data: {dato: dato},
    })
    .done(function(respuesta){
        $("#client").html(respuesta);
    })
    .fail(function(){
        console.log("error");
    })
}

// Press a key -> Show the list that match what's written on the input
$(document).on('change', '#business', function(event){

    var valor = $(this).val();

    if(valor != "" ){
        buscarCli(valor);
    }

});
$(buscar());

// Send "consulta" to php-files/prepareList.php
// If no error print the result in #search-result
// Else console log -> error
function buscar(consulta){
    $.ajax({
        url: 'php-files/autocomplete.php',
        type: 'POST',
        dataType: "html",
        data: {consulta: consulta},
    })
    .done(function(respuesta){
        $("#search-result").html(respuesta);
    })
    .fail(function(){
        console.log("error");
    })
}

var index = -1;

// Press a key -> Show the list that match what's written on the input
$(document).on('keydown', '#business', function(event){

    var elems = document.getElementsByClassName("autocomplete-item");

    if (event.keyCode === 13) {

        if(elems.length > 0){
            if (index > -1) {
                // Do not submit the form when pressing Enter
                event.preventDefault();
                // Cause click event on active element
                if (elems) elems[index].click();
            }
        }

    } else {

        switch (event.key) {
            case 'ArrowDown':
                index ++;
                activar(elems);
                break;
            case 'ArrowUp':
                index --;
                activar(elems);
                break;
            case 'ArrowUp':
                index --;
                activar(elems);
                break;
        
            default:
                var valor = $(this).val();
                if(valor != ""){
                    buscar(valor);
                } else {
                    buscar();
                }
                break;
        }
    }
    
});

// Close the list when clicking anywhere
document.addEventListener("click", function (e) {
    var x = document.getElementById("search-result");
    x.innerHTML = "";
});

function activar(elem){

    if (!elem) return false;
    desactivar(elem);

    // If the index overflows -> back to the first
    if (index >= elem.length) index = 0;
    // If the index is negative -> back to position 1
    if (index < 0) index = (elem.length - 1);
    
    elem[index].classList.add("autocomplete-active");

}

// Deactivate all elements
function desactivar(elem){
    for (var i = 0; i < elem.length; i++) {
        elem[i].classList.remove("autocomplete-active");
    }
}

// Item from the list clicked -> put value in the input
function seleccionar(givenIndex){
    elems = document.getElementsByClassName("autocomplete-item");
    document.getElementById("business").value = elems[givenIndex].innerHTML;
    $('#business').trigger('change'); // Trigger an change event to simulate the Enter keypress
}
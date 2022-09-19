document.getElementById("toggle-menu").addEventListener("click", function(){
    
    var menu = document.getElementById("sidebar");
    var togButton = document.getElementById("toggle-menu");

    if(menu.style.display == null | menu.style.display == 'none'){
        menu.style.display = "block";
        togButton.innerText = "Ocultar"
    } else {
        menu.style.display = "none";
        togButton.innerText = "Mostrar"
    }
});
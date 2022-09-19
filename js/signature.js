var wrapper = document.getElementById("signature-pad"); // The signature pad
var clearButton = wrapper.querySelector("[data-action=clear]"); // Delete the signature
var undoButton = wrapper.querySelector("[data-action=undo]"); // Undo the last stroke
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

// - - - - CLEAR and UNDO - - - - -


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

// - - - - - SAVE and LOAD the signature from the DB - - - - -

// Save each stroke into input signature, a hidden input
signaturePad.addEventListener("endStroke", () => {
    document.getElementById("signature").value = signaturePad.toDataURL('image/svg+xml');
    //document.getElementById("signature").value = canvas.toBlob; // <-- I prefer the method above
});

// get the hidden input signature and load it's data into the signaturePad
function loadFromInput(){
    var signature = document.getElementById("signature").value;
    signaturePad.fromDataURL(signature);
}
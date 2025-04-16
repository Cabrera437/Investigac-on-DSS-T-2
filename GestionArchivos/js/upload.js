// Esta es una variable de control para mantener nombres
// diferentes de cada campo de texto creado dinámicamente.
var numero = 0;

var idText = 0;
var idField = 0;
var idSpan = 0;

window.onload = init;

function init() {
    // Asociando el enlace "Subir otro archivo" al enlace apropiado
    var link = document.getElementById("addfieldlink");

    if (link.addEventListener) {
        link.addEventListener("click", addCampo, false);
    } else if (link.attachEvent) {
        link.attachEvent("onclick", addCampo);
    }

    // Para añadirle funcionalidad al span del primer input file type
    var span = document.getElementById("spanadj");

    if (span.addEventListener) {
        span.addEventListener("click", function() {
            var adj = document.getElementById("uploadBtn0");
            adj.click();
            adj.onchange = function() {
                document.getElementById('uploadFile0').value = this.value;
            }
        }, false);
    }
}

// Esta función nos devuelve el tipo de evento disparado
evento = function(evt) {
    return (!evt) ? event : evt;
}

// Con esta función creamos dinámicamente los nuevos campos file
addCampo = function() {
    // Contador para los elementos dinámicos que se generarán
    ++numero;

    // Creamos un nuevo div para que contenga el nuevo campo
    nDiv = document.createElement('div');
    nDiv.className = 'contenedor';
    nDiv.id = 'file' + (numero);

    // Creamos un elemento input que estará deshabilitado
    nInput = document.createElement('input');
    nInput.type = 'text';
    nInput.id = 'uploadFile' + (numero);
    nInput.placeholder = 'Seleccionar archivo';
    nInput.disabled = 'disabled';

    // Creamos otro div que contendrá los elementos
    nDivAdjunto = document.createElement('div');
    nDivAdjunto.className = "file-upload btn";

    // Creamos el elemento span que hará las veces del botón
    nSpan = document.createElement('span');
    nSpan.className = "btn btn-primary";
    nSpan.innerHTML = 'Adjunto';
    nSpan.id = (numero);
    nSpan.onclick = addOneClick;

    // Creamos el input type=file para el formulario
    nCampo = document.createElement('input');
    nCampo.name = 'archivos[]';  // Es importante que se nombre como vector/matriz
    nCampo.type = 'file';
    nCampo.id = 'uploadBtn' + (numero);
    nCampo.className = 'upload';

    // Creamos un elemento a href para poder eliminar un campo que ya no deseemos
    a = document.createElement('a');
    a.name = nDiv.id;  // El link debe tener el mismo nombre de la div padre
    a.href = 'javascript:void(0)';
    a.onclick = elimCamp;
    a.innerHTML = 'Eliminar';

    // Integramos los elementos creados al documento
    nDiv.appendChild(nInput);
    nDiv.appendChild(nDivAdjunto);
    nDivAdjunto.appendChild(nSpan);
    nDivAdjunto.appendChild(nCampo);
    nDivAdjunto.appendChild(a);

    // Añadimos la nueva div al contenedor
    container = document.getElementById('conteGeneral');
    container.appendChild(nDiv);
}

// Con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCamp = function(evt) {
    evt = evento(evt);
    nCampo = rObj(evt);
    console.log(nCampo.name);
    div = document.getElementById(nCampo.name);
    div.parentNode.removeChild(div);
}

// Esta función maneja el click para seleccionar un archivo
addOneClick = function(evt) {
    evt = evento(evt);
    nCampo = rObj(evt);
    div = document.getElementsByTagName("span")[this.id];
    file = "uploadBtn" + div.id;
    var fileUp = document.getElementById(file);
    console.log(fileUp);
    var adj = document.getElementById(fileUp.id);
    adj.click();
    adj.onchange = function() {
        document.getElementById('uploadFile' + div.id).value = this.value;
    }
}

// Con esta función recuperamos una instancia del objeto que disparó el evento
rObj = function(evt) {
    return evt.srcElement ? evt.srcElement : evt.target;
}

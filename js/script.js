// ============================================================
// script.js - JavaScript de la Tienda Virtual
//
// Función: al hacer clic en la foto de un producto se abre el
// modal de Bootstrap con la imagen ampliada y su nombre.
// ============================================================

// querySelectorAll(): obtiene todas las fotos de la galería
var imagenes = document.querySelectorAll('.img-producto');

// forEach(): ejecuta el bloque por cada imagen encontrada
imagenes.forEach(function(imagen) {

    // addEventListener(): ejecuta la función al hacer clic
    imagen.addEventListener('click', function() {

        // this: la imagen sobre la que se hizo clic
        var imagenAmpliada = document.getElementById('imagenAmpliada');

        // src: copia la URL de la foto original al modal
        imagenAmpliada.src = this.src;

        // alt: copia el nombre del producto como texto alternativo
        imagenAmpliada.alt = this.alt;

        // textContent: coloca ese nombre como título del modal
        document.getElementById('modalImagenTitulo').textContent = this.alt;

        // bootstrap.Modal: controla la ventana emergente de Bootstrap
        var modal = new bootstrap.Modal(document.getElementById('modalImagen'));

        // show(): muestra el modal; se cierra con X, Esc o clic afuera
        modal.show();
    });
});
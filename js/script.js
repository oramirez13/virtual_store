// ============================================================
// script.js - JavaScript de la Tienda Virtual
//
// Funcionalidad: al hacer clic en la foto de una camiseta se
// abre la imagen ampliada.
// ============================================================

// querySelectorAll(): obtiene todas las fotos de la galería (.img-producto)
var imagenes = document.querySelectorAll('.img-producto');

// forEach(): recorre la lista y ejecuta el bloque por cada imagen
imagenes.forEach(function(imagen) {

    // addEventListener(): registra la acción al hacer clic en la imagen
    imagen.addEventListener('click', function() {

        // this: la imagen clickeada; se obtiene la <img> del modal
        var imagenAmpliada = document.getElementById('imagenAmpliada');

        // src: copia la URL de la foto original al modal
        imagenAmpliada.src = this.src;

        // alt: copia el nombre del producto
        imagenAmpliada.alt = this.alt;

        // textContent: coloca el nombre como título del modal
        document.getElementById('modalImagenTitulo').textContent = this.alt;

        // Modal: objeto de Bootstrap que controla la ventana emergente
        var modal = new bootstrap.Modal(document.getElementById('modalImagen'));

        // show(): despliega el modal; cierra con X, Esc o clic fuera
        modal.show();
    });
});

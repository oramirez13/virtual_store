/* ============================================================
   script.js - JavaScript for the Virtual Store
   ============================================================ */

// querySelectorAll(): gets all product photos in the gallery
var images = document.querySelectorAll('.img-producto');

// forEach(): runs the block for each image found
images.forEach(function(image) {

    // addEventListener(): executes the function on click
    image.addEventListener('click', function() {

        // this: the image that was clicked
        var enlargedImage = document.getElementById('enlargedImage');

        // src: copies the original photo URL to the modal
        enlargedImage.src = this.src;

        // alt: copies the product name as alternative text
        enlargedImage.alt = this.alt;

        // textContent: places that name as the modal title
        document.getElementById('imageModalTitle').textContent = this.alt;

        // bootstrap.Modal: controls the Bootstrap modal window
        var modal = new bootstrap.Modal(document.getElementById('imageModal'));

        // show(): displays the modal; closes with X, Esc or outside click
        modal.show();
    });
});
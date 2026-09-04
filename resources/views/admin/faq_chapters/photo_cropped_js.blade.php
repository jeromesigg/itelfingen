<script type="module">
    var $modal = $('#modal');
    var image = document.getElementById('image');
    <script type="module">
        const modal = document.getElementById('modal');
        const image = document.getElementById('image');
        const cropButton = document.getElementById('crop');
        const fileInput = document.querySelector('.old_photo_id');
        let cropper;
        let objectUrl;

        const closeModal = function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }
        };

        const openModal = function () {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            cropper = new Cropper(image, {
                aspectRatio: 4,
                viewMode: 3,
                preview: '.preview'
            });
            modal.querySelector('[data-modal-dialog]').focus();
        };

        document.addEventListener('change', function (event) {
            if (!event.target.matches('.old_photo_id') || !event.target.files.length) {
                return;
            }

            objectUrl = URL.createObjectURL(event.target.files[0]);
            image.src = objectUrl;
            openModal();
        });

        cropButton.addEventListener('click', function () {
            const canvas = cropper.getCroppedCanvas({
                width: 780,
                height: 220,
            });

            canvas.toBlob(function (blob) {
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function () {
                    document.getElementById('new_photo_id').value = reader.result;
                    closeModal();
                    fileInput.value = '';
                };
            });
        });

        modal.querySelectorAll('[data-modal-close]').forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>

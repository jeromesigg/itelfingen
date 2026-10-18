
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
        fileInput.value = '';

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
        console.log('openModal function called');
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
        console.log((event.target));
            return;
        }

        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
        }
        console.log('change event triggered2');

        objectUrl = URL.createObjectURL(event.target.files[0]);
        console.log('change event triggered3');
        image.onload = openModal;
        image.src = objectUrl;
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

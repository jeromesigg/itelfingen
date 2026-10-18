@props([
    'title' => 'Bild zuschneiden',
    'input',
    'target',
    'aspectRatio' => 1,
    'width' => 640,
    'height' => 640,
    'clearInput' => false,
])

{{--
    Dialog zum Zuschneiden eines hochgeladenen Bildes mit CropperJS.
    input:  CSS-Selektor des Datei-Feldes, target: ID des versteckten Feldes für das zugeschnittene Bild (Base64).
    clearInput: Datei-Feld nach dem Dialog leeren, wenn nur das zugeschnittene Bild gespeichert wird.
--}}
<dialog id="crop-dialog" aria-labelledby="crop-dialog-title"
        data-input="{{ $input }}" data-target="{{ $target }}"
        data-aspect-ratio="{{ $aspectRatio }}" data-width="{{ $width }}" data-height="{{ $height }}" data-clear-input="{{ $clearInput ? 'true' : 'false' }}"
        class="m-auto w-full max-w-5xl rounded-lg bg-white p-0 shadow-xl backdrop:bg-gray-950/60 dark:bg-gray-800">
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white" id="crop-dialog-title">{{ $title }}</h2>
        <button type="button" class="rounded-lg p-2 text-2xl leading-none text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700" data-crop-close aria-label="Schliessen">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="grid gap-6 p-6 md:grid-cols-[minmax(0,2fr)_minmax(12rem,1fr)]">
        <div class="flex min-h-64 items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900">
            <img class="block max-h-[60vh] max-w-full" id="crop-dialog-image" src="" alt="Ausgewähltes Bild">
        </div>
        <div>
            <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Vorschau</p>
            <div class="crop-dialog-preview w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-900" style="aspect-ratio: {{ $aspectRatio }}"></div>
        </div>
    </div>
    <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-700">
        <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-700" data-crop-close>Abbrechen</button>
        <button type="button" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-strong focus:outline-hidden focus:ring-4 focus:ring-primary-soft" data-crop-confirm>Zuschneiden</button>
    </div>
</dialog>

@once
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.min.css"
              integrity="sha512-aRKUyPVhQ+l8F3zkQzJH3IF7BbS9hJ7knfWJHMooCmte0L0nnDGp2wwCQrXfVqhM5YSlyucSh3/e6ypmJ3wNvg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.min.js"
                integrity="sha512-RmS22KqPHAotCK4+S4tPn8++dv6Tun0zIrk3kaDtE62S+3dnC7lHskeDdBDEhlpQ5QyO+9A2b+q/B3AouS3Cyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script type="module">
            const dialog = document.getElementById('crop-dialog');
            const image = document.getElementById('crop-dialog-image');
            const options = dialog.dataset;
            let cropper;
            let objectUrl;

            const reset = function () {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
            };

            document.addEventListener('change', function (event) {
                if (!event.target.matches(options.input) || !event.target.files.length) {
                    return;
                }
                reset();
                objectUrl = URL.createObjectURL(event.target.files[0]);
                image.onload = function () {
                    dialog.showModal();
                    cropper = new Cropper(image, {
                        aspectRatio: Number(options.aspectRatio),
                        viewMode: 3,
                        preview: '.crop-dialog-preview',
                    });
                };
                image.src = objectUrl;
            });

            dialog.querySelector('[data-crop-confirm]').addEventListener('click', function () {
                const canvas = cropper.getCroppedCanvas({
                    width: Number(options.width),
                    height: Number(options.height),
                });
                canvas.toBlob(function (blob) {
                    const reader = new FileReader();
                    reader.onloadend = function () {
                        document.getElementById(options.target).value = reader.result;
                        dialog.close();
                    };
                    reader.readAsDataURL(blob);
                });
            });

            dialog.querySelectorAll('[data-crop-close]').forEach(function (button) {
                button.addEventListener('click', function () {
                    dialog.close();
                });
            });

            // Klick auf den Hintergrund schliesst den Dialog
            dialog.addEventListener('click', function (event) {
                if (event.target === dialog) {
                    dialog.close();
                }
            });

            dialog.addEventListener('close', function () {
                if (options.clearInput === 'true') {
                    document.querySelector(options.input).value = '';
                }
                reset();
            });
        </script>
    @endpush
@endonce

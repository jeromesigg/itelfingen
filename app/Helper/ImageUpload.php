<?php

namespace App\Helper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Exceptions\ArgumentException;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\ImageManager;

/**
 * Bilder für public/images: nur echte Bilder, Dateiname und Endung vergibt der Server.
 * Der Name aus dem Upload wird nie übernommen, sonst liesse sich z. B. eine .php-Datei ablegen und aufrufen.
 */
class ImageUpload
{
    public const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Prüft den Inhalt und speichert die Datei in images/. Gibt den Dateinamen zurück.
     */
    public static function store(UploadedFile $file, string $field = 'photo_id'): string
    {
        $name = self::name($file, $field);
        $file->move('images', $name);

        return $name;
    }

    /**
     * Sicherer Dateiname mit der Endung, die zum Inhalt passt (z. B. für saveDataUri()).
     */
    public static function name(UploadedFile $file, string $field = 'photo_id', string $suffix = ''): string
    {
        // guessExtension() liest den MIME-Typ aus dem Inhalt, nicht aus dem Namen.
        $extension = $file->guessExtension();

        if (! in_array($extension, self::EXTENSIONS, true)) {
            throw ValidationException::withMessages([$field => 'Nur Bilder (JPG, PNG, GIF, WebP) sind erlaubt.']);
        }

        return time().'_'.Str::random(8).$suffix.'.'.$extension;
    }

    /**
     * Speichert ein Bild aus einer Data-URL (z. B. vom Zuschneiden im Browser). Das Format folgt der Endung von $path.
     * Andere Quellen wie Dateipfade oder URLs werden nicht gelesen.
     */
    public static function saveDataUri(string $dataUri, string $path, string $field = 'photo_id'): void
    {
        try {
            $image = ImageManager::usingDriver(Driver::class)->decodeDataUri($dataUri);
        } catch (ArgumentException|DecoderException) {
            throw ValidationException::withMessages([$field => 'Das zugeschnittene Bild konnte nicht gelesen werden.']);
        }

        $image->save($path);
    }
}

<?php

namespace Tests\Feature\Security;

use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Bild-Uploads im Admin-Bereich landen in public/images und sind direkt aufrufbar.
 * Es dürfen nur echte Bilder mit eigenem Dateinamen gespeichert werden, nie z. B. eine .php-Datei.
 */
class ImageUploadTest extends TestCase
{
    private string $publicDir;

    private string $originalCwd;

    protected function setUp(): void
    {
        parent::setUp();

        // Die Controller schreiben relativ zum Arbeitsverzeichnis (im Webserver: public/).
        $this->publicDir = sys_get_temp_dir().'/itelfingen-upload-'.uniqid();
        mkdir($this->publicDir.'/images', 0777, true);
        $this->originalCwd = getcwd();
        chdir($this->publicDir);
        $this->app->usePublicPath($this->publicDir);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        array_map('unlink', glob($this->publicDir.'/images/*'));
        rmdir($this->publicDir.'/images');
        rmdir($this->publicDir);

        parent::tearDown();
    }

    public static function uploads(): array
    {
        return [
            'faq' => ['/admin/faqs', ['name' => 'Frage', 'description' => 'Antwort']],
            'history' => ['/admin/histories', ['title' => 'Titel', 'description' => 'Text']],
            'picture' => ['/admin/pictures', ['name' => 'Bild', 'cropped_photo_id' => null]],
        ];
    }

    #[DataProvider('uploads')]
    public function test_php_file_is_not_stored(string $uri, array $data): void
    {
        $s = BookingScenario::create();
        $file = $this->realUpload('shell.php', '<?php echo "x";');

        $this->actingAs($s->user('admin'))->post($uri, $data + ['photo_id' => $file]);

        $this->assertSame([], $this->storedFiles());
    }

    #[DataProvider('uploads')]
    public function test_image_with_php_name_is_stored_as_image(string $uri, array $data): void
    {
        $s = BookingScenario::create();
        // Echter PNG-Inhalt, aber gefährlicher Name: gespeichert wird mit eigener Endung.
        $png = imagecreatetruecolor(10, 10);
        ob_start();
        imagepng($png);
        $file = $this->realUpload('bild.php', ob_get_clean());

        $this->actingAs($s->user('admin'))->post($uri, $data + ['photo_id' => $file]);

        $files = $this->storedFiles();
        $this->assertCount(1, $files);
        $this->assertMatchesRegularExpression('/\.(png|jpg)$/', $files[0]);
    }

    #[DataProvider('uploads')]
    public function test_regular_image_is_stored(string $uri, array $data): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->post($uri, $data + ['photo_id' => UploadedFile::fake()->image('foto.jpg', 10, 10)]);

        $files = $this->storedFiles();
        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.jpg', $files[0]);
    }

    public function test_cropped_picture_from_data_uri_is_stored_as_image(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->post('/admin/pictures', [
            'name' => 'Bild',
            'photo_id' => UploadedFile::fake()->image('foto.jpg', 10, 10),
            'cropped_photo_id' => self::pngDataUri(),
        ])->assertSessionHasNoErrors();

        $cropped = preg_grep('/_cropped\.jpg$/', $this->storedFiles());
        $this->assertCount(1, $cropped);
        $this->assertSame('image/jpeg', mime_content_type($this->publicDir.'/images/'.reset($cropped)));
    }

    public function test_cropped_picture_does_not_read_paths(): void
    {
        $s = BookingScenario::create();

        // Image::make() aus Intervention 2 hat auch Dateipfade und URLs gelesen.
        $this->actingAs($s->user('admin'))->post('/admin/pictures', [
            'name' => 'Bild',
            'photo_id' => UploadedFile::fake()->image('foto.jpg', 10, 10),
            'cropped_photo_id' => base_path('public/favicon.ico'),
        ])->assertSessionHasErrors('cropped_photo_id');

        $this->assertSame([], preg_grep('/_cropped/', $this->storedFiles()));
    }

    public function test_faq_chapter_photo_from_data_uri_is_stored_as_png(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->post('/admin/faq_chapters', [
            'name' => 'Kapitel',
            'new_photo_id' => self::pngDataUri(),
        ])->assertSessionHasNoErrors();

        $files = $this->storedFiles();
        $this->assertCount(1, $files);
        $this->assertStringEndsWith('_faq_chapter.png', $files[0]);
        $this->assertSame('image/png', mime_content_type($this->publicDir.'/images/'.$files[0]));
    }

    /**
     * So schickt das Zuschneide-Werkzeug im Browser das Bild (canvas.toDataURL()).
     */
    private static function pngDataUri(): string
    {
        ob_start();
        imagepng(imagecreatetruecolor(10, 10));

        return 'data:image/png;base64,'.base64_encode(ob_get_clean());
    }

    /**
     * Echte Datei statt UploadedFile::fake(): der Fake leitet den MIME-Typ aus dem Namen ab, nicht aus dem Inhalt.
     */
    private function realUpload(string $clientName, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($path, $content);

        return new UploadedFile($path, $clientName, null, null, true);
    }

    /**
     * @return list<string>
     */
    private function storedFiles(): array
    {
        return array_map('basename', glob($this->publicDir.'/images/*'));
    }
}

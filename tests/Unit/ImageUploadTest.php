<?php

namespace Tests\Unit;

use App\Helper\ImageUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\UnitTestCase;

/**
 * ImageUpload::name(): die Endung kommt aus dem Inhalt, nie aus dem Dateinamen des Uploads.
 */
class ImageUploadTest extends UnitTestCase
{
    /** @var list<string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        array_map('unlink', array_filter($this->tempFiles, 'file_exists'));

        parent::tearDown();
    }

    private static function image(string $type): string
    {
        $image = imagecreatetruecolor(4, 4);
        ob_start();
        match ($type) {
            'png' => imagepng($image),
            'jpeg' => imagejpeg($image),
            'gif' => imagegif($image),
            'webp' => imagewebp($image),
        };

        return ob_get_clean();
    }

    private function upload(string $clientName, string $content): UploadedFile
    {
        $path = $this->tempFiles[] = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($path, $content);

        return new UploadedFile($path, $clientName, null, null, true);
    }

    public static function images(): array
    {
        return [
            'PNG' => ['foto.png', 'png', 'png'],
            'JPEG' => ['foto.jpeg', 'jpeg', 'jpg'],
            'GIF' => ['foto.gif', 'gif', 'gif'],
            'WebP' => ['foto.webp', 'webp', 'webp'],
            'PNG mit .php im Namen' => ['bild.php', 'png', 'png'],
            'JPEG mit falscher Endung' => ['bild.png', 'jpeg', 'jpg'],
        ];
    }

    #[DataProvider('images')]
    public function test_name_uses_extension_of_content(string $clientName, string $type, string $extension): void
    {
        $name = ImageUpload::name($this->upload($clientName, self::image($type)), 'photo_id', '_cropped');

        $this->assertMatchesRegularExpression('/^\d+_[A-Za-z0-9]{8}_cropped\.'.$extension.'$/', $name);
    }

    public static function rejected(): array
    {
        return [
            'PHP' => ['shell.php', '<?php echo "x";'],
            'PHP als Bild getarnt' => ['shell.jpg', '<?php echo "x";'],
            // SVG kann JavaScript enthalten und läge öffentlich in images/
            'SVG' => ['logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'],
            'HTML' => ['seite.html', '<html><body>x</body></html>'],
        ];
    }

    #[DataProvider('rejected')]
    public function test_non_images_are_rejected(string $clientName, string $content): void
    {
        try {
            ImageUpload::name($this->upload($clientName, $content), 'photo_id');
            $this->fail("$clientName hätte abgelehnt werden müssen");
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('photo_id', $e->errors());
        }
    }

    public function test_names_are_unique(): void
    {
        $content = self::image('png');

        $this->assertNotSame(
            ImageUpload::name($this->upload('a.png', $content)),
            ImageUpload::name($this->upload('a.png', $content)),
        );
    }
}

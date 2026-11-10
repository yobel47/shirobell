<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * All customer uploads go through here. Images are decoded and re-encoded to
 * WebP, which strips metadata (EXIF/GPS) and any payload appended to the file.
 * File names are random; the client-provided name is never used.
 */
class MediaService
{
    private const DISK = 'public';

    public function storeImage(UploadedFile $file, string $directory, ?int $maxPx = null, int $quality = 80): string
    {
        $maxPx ??= (int) config('shirobell.invitation.image_max_px', 1600);

        $image = (new ImageManager(new Driver))->read($file->getRealPath());
        $image->scaleDown(width: $maxPx, height: $maxPx);

        $path = trim($directory, '/').'/'.Str::random(40).'.webp';
        Storage::disk(self::DISK)->put($path, (string) $image->toWebp($quality));

        return $path;
    }

    public function storeAudio(UploadedFile $file, string $directory): string
    {
        $path = trim($directory, '/').'/'.Str::random(40).'.mp3';
        Storage::disk(self::DISK)->putFileAs(dirname($path), $file, basename($path));

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public function deleteDirectory(string $directory): void
    {
        Storage::disk(self::DISK)->deleteDirectory($directory);
    }

    /** Validation rules shared by every image field. */
    public static function imageRules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:'.config('shirobell.invitation.image_max_kb', 8192),
            'dimensions:max_width=8000,max_height=8000',
        ];
    }

    public static function audioRules(): array
    {
        return [
            'required',
            'file',
            'mimetypes:audio/mpeg,audio/mp3',
            'max:'.config('shirobell.invitation.music_max_kb', 10240),
        ];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageUploader
{
    public static function upload($image)
    {
        if ($image) {
            $path = "images/" . date("YmdHis") . md5($image) . ".jpg";

            try {
                $result = Storage::disk("oss")->put($path, file_get_contents($image));
                if ($result !== false) {
                    $image = env('OSS_DOMAIN') . '/' . $path;
                }
            } catch (\Throwable $exception) {
                \Log::error("Upload Failed: " . $exception->getMessage(), ['image' => $image]);
            } finally {
                return $image;
            }
        }

        return '';
    }
}

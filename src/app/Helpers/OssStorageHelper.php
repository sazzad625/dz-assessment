<?php


namespace App\Helpers;


use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use League\Flysystem\Util;
use OSS\Core\OssException;
use OSS\OssClient;

class OssStorageHelper
{
    const ENC_SEPARATOR = "##";

    /**
     * @param $uploadedFile
     * @param $width
     * @param $height
     * @return array an array with the path and name on which file is stored
     * e.g [path => 'some/path/to/file', name => 'filename.png']
     * @author ilyas
     * To store uploaded image directly to oss storage with the optimized width and height
     */
    public static function storeUploadedImageToPublic($uploadedFile, $width, $height)
    {
        $fileName = PathHelper::getFileName(Util::normalizePath($uploadedFile->getClientOriginalName()));
        $path = PathHelper::getNewPublicPath();
        $optimizeImage = Image::make($uploadedFile->getRealPath())
            ->resize($width, $height)
            ->stream();
        Storage::disk('oss')->put($path . $fileName, $optimizeImage);

        return [
            'path' => $path,
            'name' => $fileName
        ];
    }

    public static function storeUploadedFileToTemp($uploadedFile)
    {
        $fileName = PathHelper::getFileName(Util::normalizePath($uploadedFile->getClientOriginalName()));
        $path = PathHelper::getNewTempPath();

        $uploadedFile->storeAs($path, $fileName, 'oss');

        return [
            'path' => $path,
            'name' => $fileName
        ];
    }

    /**
     *
     * @param string $path
     * @param string $object
     * @param integer $timeout
     * @return string url
     */
    public static function getSignUrl($path, $timeout = 60)
    {

        $path = config('app.oss_path_prefix') . $path;
        $accessKeyId = config('app.oss_key_id');
        $accessKeySecret = config('app.oss_key_secret');
        $endpoint = config('app.oss_endpoint');
        $bucket = config('app.oss_bucket_name');

        try {

            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
            return $ossClient->signUrl($bucket, $path, $timeout);

        } catch (OssException $e) {
            report($e);
        }
        return '';
    }

    public static function getStoragePathForAssets($path, $name)
    {
        return route('public.storage', $path . $name);
    }

    public static function getSecureStoragePathForAssets($uri)
    {
        return route('secure.storage', $uri);
    }

    public static function encryptAssetURI($uri)
    {
        return config('app.enc_prefix') . Crypt::encryptString($uri . self::ENC_SEPARATOR . Auth::id());
    }

    public static function decryptAssetURI($uri)
    {
        if (!self::isEncryptedURI($uri)) {
            return null;
        }

        try {
            $uri = Str::of($uri)->substr(Str::of(config('app.enc_prefix'))->length());
            $uri = Crypt::decryptString($uri);
        } catch (DecryptException $e) {
            return null;
        }

        $uri = Str::of($uri)->explode(self::ENC_SEPARATOR);

        if ($uri->last() != Auth::id()) {
            abort(403);
        }

        return $uri->first();

    }

    public static function isEncryptedURI($uri)
    {
        return Str::of($uri)->startsWith(config('app.enc_prefix'));
    }

}
